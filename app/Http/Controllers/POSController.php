<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\StockTransferService;
use App\Models\SalePayment;
use App\Models\Warehouse;

class POSController extends Controller
{
    public function index()
    {
        // Get products with their total available stock in Shop 1
        $products = Product::with(['category', 'stockBatches' => function($query) {
            $query->where('remaining_quantity', '>', 0);
        }])->get()->map(function($product) {
            $product->stock_count = $product->stockBatches->sum('remaining_quantity');
            // If no category, assign a default 'Uncategorized' placeholder ID or leave null, handled in frontend
            return $product;
        });

        $categories = Category::all();
        $customers = Customer::all();
        $warehouses = Warehouse::all();

        return view('pos.index', compact('products', 'categories', 'customers', 'warehouses'));
    }

    public function checkStock(Request $request)
    {
        $insufficientItems = [];

        foreach ($request->cart as $item) {

            $product = Product::findOrFail($item['id']);
            $quantityRequested = (float) $item['quantity'];
            $warehouseId = $item['warehouse_id'] ?? 1; // Default to Shop 1

            $available = StockBatch::where('product_id', $product->id)
                ->where('warehouse_id', $warehouseId)
                ->sum('remaining_quantity');

            if ($available < $quantityRequested) {
                // The warehouse we WANTED to sell from is short.
                // We need to find ANOTHER warehouse that HAS the stock.
                $needed = $quantityRequested - $available;
                
                // Find a warehouse that has ANY stock of this product
                // (In a real system, you might pick the one with the MOST stock or closest)
                $alternateBatch = StockBatch::where('product_id', $product->id)
                    ->where('warehouse_id', '!=', $warehouseId)
                    ->where('remaining_quantity', '>', 0)
                    ->first();
                
                $fromWarehouseId = $alternateBatch ? $alternateBatch->warehouse_id : null;
                $fromWarehouse = $fromWarehouseId ? Warehouse::find($fromWarehouseId) : null;

                $insufficientItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'needed' => $needed,
                    'to_warehouse_id' => $warehouseId, // The destination is the POS selection
                    'to_warehouse_name' => Warehouse::find($warehouseId)->name,
                    'from_warehouse_id' => $fromWarehouseId,
                    'from_warehouse_name' => $fromWarehouse ? $fromWarehouse->name : 'No other warehouse has stock',
                ];
            }
        }

        if (!empty($insufficientItems)) {
            return response()->json([
                'status' => 'insufficient',
                'items' => $insufficientItems
            ]);
        }

        return response()->json([
            'status' => 'ok'
        ]);
    }

public function transferStock(Request $request, StockTransferService $stockTransferService)
{
    try {
        // Validate the request
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'from_warehouse_id' => 'required|integer', 
            'to_warehouse_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        // Call the service with correct parameters
        $result = $stockTransferService->transfer(
            $validated['product_id'],
            $validated['from_warehouse_id'],
            $validated['to_warehouse_id'],
            $validated['quantity']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Stock transferred successfully',
            'data' => $result
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|numeric|min:0.01',
            'cart.*.unit_price' => 'required|numeric|min:0', // Manual price override
            'cart.*.discount' => 'nullable|numeric|min:0', // Line item discount
            'cart.*.warehouse_id' => 'nullable|exists:warehouses,id',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        try {
            DB::beginTransaction();

            $date = now()->format('Ymd');

            $todayCount = Sale::whereDate('created_at', now()->toDateString())->count();
            $sequence = $todayCount + 1;

            $invoiceNumber = 'INV-' . $date . '-' . $sequence;
            
            // Determine primary/display payment method
            $paymentMethods = collect($request->payments)->pluck('method')->unique()->toArray();
            $paymentMethodLabel = count($paymentMethods) > 1 ? 'Multi' : $paymentMethods[0];

            // Create Sale Record
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'total_amount' => 0, // Will update after calculating items
                'payment_method' => $paymentMethodLabel,
                'customer_id' => $request->customer_id,
            ]);

            $totalSaleAmount = 0;

            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);
                $quantityRequested = (float) $item['quantity'];
                $unitPrice = (int) round($item['unit_price']); // Ensure integer
                $discount = (int) round($item['discount'] ?? 0); // Ensure integer
                $warehouseId = $item['warehouse_id'] ?? 1; // Default to Shop 1
                
                // 1. Calculate Revenue for this line item (subtotal before batch division)
                // Note: We'll calculate the actual subtotal and lineTotal per batch to be precise
                
                // 2. FIFO Stock Deduction & SaleItem creation
                $remainingToDeduct = $quantityRequested;

                // Get batches for this product in specific warehouse, ordered by date
                $batches = StockBatch::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouseId)
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                $itemSubtotal = (int) round($unitPrice * $quantityRequested);
                $itemTotal = $itemSubtotal - $discount;
                $totalSaleAmount += $itemTotal;

                // 3. Create ONE SaleItem for this product
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantityRequested,
                    'unit_price' => $unitPrice,
                    'cost_price' => 0, // Update below after calculating from batches
                    'total_cost' => 0, // Update below after calculating from batches
                    'subtotal' => $itemSubtotal,
                    'discount' => $discount,
                    'total_price' => $itemTotal,
                ]);

                $totalCostForThisItem = 0;

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    // Take only what is needed
                    $take = min((float)$batch->remaining_quantity, (float)$remainingToDeduct);

                    if ($take <= 0) {
                        continue;
                    }

                    // Deduct stock
                    $batch->decrement('remaining_quantity', $take);

                    $batchCost = (int) $batch->cost_price;
                    $totalCostForThisItem += (int) round($batchCost * $take);

                    // 4. Create SaleItemBatch to record the FIFO slice
                    \App\Models\SaleItemBatch::create([
                        'sale_item_id' => $saleItem->id,
                        'stock_batch_id' => $batch->id,
                        'quantity' => $take,
                        'cost_price' => $batchCost,
                    ]);

                    $remainingToDeduct -= $take;
                }

                // Update the single SaleItem with correct aggregated costs
                $avgCostPrice = $quantityRequested > 0 ? (int) round($totalCostForThisItem / $quantityRequested) : 0;
                $saleItem->update([
                    'cost_price' => $avgCostPrice,
                    'total_cost' => $totalCostForThisItem,
                ]);

                if ($remainingToDeduct > 0) { 
                    throw new \Exception("Not enough stock for {$product->name}. Missing: {$remainingToDeduct}");
                }
            }

            $sale->update(['total_amount' => (int) $totalSaleAmount]);

            // 4. Handle Payments & Credit
            foreach ($request->payments as $paymentData) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'payment_method' => $paymentData['method'],
                    'amount' => $paymentData['amount'],
                ]);
                \Log::info('Processing payment:', $paymentData);
                
                if ($paymentData['method'] === 'Credit') {
                     \Log::info('Credit payment detected. Customer ID: ' . $request->customer_id);
                }

                if ($paymentData['method'] === 'Credit' && !empty($request->customer_id)) {
                    // Use DB facade with raw query to handle NULL values safely (COALESCE)
                    $affected = DB::table('customers')
                        ->where('id', $request->customer_id)
                        ->update([
                            'credit_balance' => DB::raw('COALESCE(credit_balance, 0) + ' . (int)$paymentData['amount'])
                        ]);

                    if ($affected) {
                         \Log::info("Successfully incremented credit for customer {$request->customer_id} by {$paymentData['amount']}");
                    } else {
                         // If affected is 0, it might mean the value didn't change (e.g. adding 0) or row not found.
                         // But if customer exists, it should work now even if NULL.
                         \Log::warning("Update executed but 0 rows affected for customer {$request->customer_id}. Amount: {$paymentData['amount']}");
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'invoice' => $sale->invoice_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
