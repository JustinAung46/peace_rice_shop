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

        return view('pos.index', compact('products', 'categories', 'customers'));
    }

    public function checkStock(Request $request)
    {
        $insufficientItems = [];

        foreach ($request->cart as $item) {

            $product = Product::findOrFail($item['id']);
            $quantityRequested = (float) $item['quantity'];

            $available = StockBatch::where('product_id', $product->id)
                ->where('warehouse_id', 1) // Shop
                ->sum('remaining_quantity');

            if ($available < $quantityRequested) {

                $insufficientItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'needed' => $quantityRequested - $available,
                    'from_warehouse' => 'Warehouse 2',  // Display name
                    'from_warehouse_id' => 2,
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
            'payment_method' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        try {
            DB::beginTransaction();

            $date = now()->format('Ymd');

            $todayCount = Sale::whereDate('created_at', now()->toDateString())->count();
            $sequence = $todayCount + 1;

            $invoiceNumber = 'INV-' . $date . '-' . $sequence;
            // Create Sale Record
            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'total_amount' => 0, // Will update after calculating items
                'payment_method' => $request->payment_method,
                'customer_id' => $request->customer_id,
            ]);

            $totalSaleAmount = 0;

            foreach ($request->cart as $item) {
                $product = Product::findOrFail($item['id']);
                $quantityRequested = (float) $item['quantity'];
                $unitPrice = (int) round($item['unit_price']); // Ensure integer
                $discount = (int) round($item['discount'] ?? 0); // Ensure integer
                
                // 1. Calculate Revenue for this line item (subtotal before batch division)
                // Note: We'll calculate the actual subtotal and lineTotal per batch to be precise
                
                // 2. FIFO Stock Deduction & SaleItem creation
                $remainingToDeduct = $quantityRequested;

                // Get batches for this product in Shop 1 (ID: 1), ordered by date
                $batches = StockBatch::where('product_id', $product->id)
                    ->where('warehouse_id', 1) // Shop 1
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                $remainingDiscount = $discount; // track remaining discount

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    // Take only what is needed
                    $take = min((float)$batch->remaining_quantity, (float)$remainingToDeduct);

                    if ($take <= 0) {
                        continue;
                    }                    

                    // Deduct stock
                    $batch->decrement('remaining_quantity', $take);

                    // Subtotal for this slice
                    $batchSubtotal = (int) round($unitPrice * $take);

                    // Smart discount distribution
                    if ($remainingToDeduct == $take) {
                        // Last batch → assign all remaining discount
                        $batchDiscount = $remainingDiscount;
                    } else {
                        $ratio = $take / $quantityRequested;
                        $batchDiscount = (int) round($discount * $ratio);
                        $remainingDiscount -= $batchDiscount;
                    }

                    $batchTotal = $batchSubtotal - $batchDiscount;

                    // 3. Create SaleItem for this batch
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'stock_batch_id' => $batch->id,
                        'quantity' => $take,
                        'unit_price' => $unitPrice,
                        'cost_price' => (int) $batch->cost_price,
                        'subtotal' => $batchSubtotal,
                        'discount' => $batchDiscount,
                        'total_price' => $batchTotal,
                    ]);

                    $totalSaleAmount += $batchTotal;
                    $remainingToDeduct -= $take;
                }

                if ($remainingToDeduct > 0) { 
                    throw new \Exception("Not enough stock for {$product->name}. Missing: {$remainingToDeduct}");
                }
            }

            $sale->update(['total_amount' => (int) $totalSaleAmount]);

            // 4. Handle Credit Payment
            if ($request->payment_method === 'Credit' && $request->customer_id) {
                $customer = Customer::find($request->customer_id);
                // Increase credit balance (money they owe)
                $customer->increment('credit_balance', $totalSaleAmount);
            }

            DB::commit();

            return response()->json(['success' => true, 'invoice' => $sale->invoice_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
