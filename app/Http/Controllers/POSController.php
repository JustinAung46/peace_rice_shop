<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockBatch;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\StockTransferService;
use App\Models\SalePayment;
use App\Models\Warehouse;

class POSController extends Controller
{
    public function index()
    {
        // Load products with variants + stock count per variant in a more optimized way
        $products = Product::withActiveCategory()->where('is_active', true)
            ->with(['category', 'variants' => function($q) {
                $q->where('is_active', true)
                  ->withSum(['stockBatches as stock_count' => function($sq) {
                      $sq->where('remaining_quantity', '>', 0);
                  }], 'remaining_quantity')
                  ->with(['stockBatches' => function($sq) {
                      $sq->where('remaining_quantity', '>', 0)
                        ->with('warehouse:id,name');
                  }]);
            }])
            ->get();

        $categories = Category::active()->get();
        $customers  = Customer::all();
        $warehouses = Warehouse::all();

        return view('pos.index', compact('products', 'categories', 'customers', 'warehouses'));
    }

    public function checkStock(Request $request)
    {
        $insufficientItems = [];

        foreach ($request->cart as $item) {
            $variantId        = $item['variant_id'];
            $quantityRequested = (int) $item['quantity'];
            $warehouseId      = $item['warehouse_id'];

            $available = StockBatch::where('product_variant_id', $variantId)
                ->where('warehouse_id', $warehouseId)
                ->sum('remaining_quantity');

            if ($available < $quantityRequested) {
                $needed = $quantityRequested - $available;

                $alternateBatch = StockBatch::where('product_variant_id', $variantId)
                    ->where('warehouse_id', '!=', $warehouseId)
                    ->where('remaining_quantity', '>', 0)
                    ->first();

                $fromWarehouseId = $alternateBatch ? $alternateBatch->warehouse_id : null;
                $fromWarehouse   = $fromWarehouseId ? Warehouse::find($fromWarehouseId) : null;

                $variant = ProductVariant::find($variantId);

                $insufficientItems[] = [
                    'variant_id'         => $variantId,
                    'product_name'       => $variant ? ($variant->product->name . ' – ' . $variant->name) : 'Unknown',
                    'needed'             => $needed,
                    'to_warehouse_id'    => $warehouseId,
                    'to_warehouse_name'  => Warehouse::find($warehouseId)->name,
                    'from_warehouse_id'  => $fromWarehouseId,
                    'from_warehouse_name'=> $fromWarehouse ? $fromWarehouse->name : 'No other warehouse has stock',
                ];
            }
        }

        if (!empty($insufficientItems)) {
            return response()->json(['status' => 'insufficient', 'items' => $insufficientItems]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function transferStock(Request $request, StockTransferService $stockTransferService)
    {
        try {
            $validated = $request->validate([
                'product_variant_id' => 'required|integer',
                'from_warehouse_id'  => 'required|integer',
                'to_warehouse_id'    => 'required|integer',
                'quantity'           => 'required|integer|min:1',
            ]);

            $result = $stockTransferService->transferVariant(
                $validated['product_variant_id'],
                $validated['from_warehouse_id'],
                $validated['to_warehouse_id'],
                $validated['quantity']
            );

            return response()->json(['status' => 'success', 'message' => 'Stock transferred successfully', 'data' => $result]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart'                       => 'required|array',
            'cart.*.variant_id'          => 'required|exists:product_variants,id',
            'cart.*.quantity'            => 'required|integer|min:1',
            'cart.*.unit_price'          => 'required|integer|min:0',
            'cart.*.discount'            => 'nullable|integer|min:0',
            'cart.*.warehouse_id'        => 'required|exists:warehouses,id',
            'payments'                   => 'required|array|min:1',
            'payments.*.method'          => 'required|string',
            'payments.*.amount'          => 'required|integer|min:0',
            'customer_id'                => 'nullable|exists:customers,id',
            'sale_type'                  => 'nullable|in:retail,wholesale',
        ]);

        try {
            DB::beginTransaction();

            $date        = now()->format('Ymd');
            $todayCount  = Sale::whereDate('created_at', now()->toDateString())->count();
            $invoiceNumber = 'INV-' . $date . '-' . ($todayCount + 1);

            $paymentMethods    = collect($request->payments)->pluck('method')->unique()->toArray();
            $paymentMethodLabel = count($paymentMethods) > 1 ? 'Multi' : $paymentMethods[0];

            $totalCreditAmount = collect($request->payments)->where('method', 'Credit')->sum('amount');
            $totalOtherAmount  = collect($request->payments)->where('method', '!=', 'Credit')->sum('amount');

            $paymentStatus = 'paid';
            if ($totalCreditAmount > 0) {
                if ($totalOtherAmount > 0) {
                    $paymentStatus = 'partial';
                } else {
                    $paymentStatus = 'unpaid';
                }
            }

            $sale = Sale::create([
                'invoice_number'   => $invoiceNumber,
                'total_amount'     => 0,
                'payment_method'   => $paymentMethodLabel,
                'customer_id'      => $request->customer_id,
                'sale_type'        => $request->sale_type ?? 'retail',
                'credit_remaining' => $totalCreditAmount,
                'payment_status'   => $paymentStatus,
            ]);

            $totalSaleAmount = 0;

            foreach ($request->cart as $item) {
                $variant          = ProductVariant::with('product')->findOrFail($item['variant_id']);
                $quantityRequested = (int) $item['quantity'];
                $unitPrice        = (int) round($item['unit_price']);
                $discount         = (int) round($item['discount'] ?? 0);
                $warehouseId      = $item['warehouse_id'];

                $itemSubtotal = (int) round($unitPrice * $quantityRequested);
                $itemTotal    = $itemSubtotal - $discount;
                $totalSaleAmount += $itemTotal;

                $saleItem = SaleItem::create([
                    'sale_id'            => $sale->id,
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity'           => $quantityRequested,
                    'unit_price'         => $unitPrice,
                    'cost_price'         => 0,
                    'total_cost'         => 0,
                    'subtotal'           => $itemSubtotal,
                    'discount'           => $discount,
                    'total_price'        => $itemTotal,
                ]);

                // FIFO deduction
                $batches = StockBatch::where('product_variant_id', $variant->id)
                    ->where('warehouse_id', $warehouseId)
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->lockForUpdate()
                    ->get();

                $remainingToDeduct    = $quantityRequested;
                $totalCostForThisItem = 0;

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;

                    $take = min((int) $batch->remaining_quantity, (int) $remainingToDeduct);
                    if ($take <= 0) continue;

                    $batch->decrement('remaining_quantity', $take);

                    $batchCost = (int) $batch->cost_price;
                    $totalCostForThisItem += (int) round($batchCost * $take);

                    \App\Models\SaleItemBatch::create([
                        'sale_item_id'   => $saleItem->id,
                        'stock_batch_id' => $batch->id,
                        'quantity'       => $take,
                        'cost_price'     => $batchCost,
                    ]);

                    $remainingToDeduct -= $take;
                }

                $avgCostPrice = $quantityRequested > 0
                    ? (int) round($totalCostForThisItem / $quantityRequested)
                    : 0;

                $saleItem->update([
                    'cost_price' => $avgCostPrice,
                    'total_cost' => $totalCostForThisItem,
                ]);

                if ($remainingToDeduct > 0) {
                    throw new \Exception("Not enough stock for {$variant->product->name} – {$variant->name}. Missing: {$remainingToDeduct}");
                }
            }

            $sale->update(['total_amount' => (int) $totalSaleAmount]);

            foreach ($request->payments as $paymentData) {
                SalePayment::create([
                    'sale_id'        => $sale->id,
                    'payment_method' => $paymentData['method'],
                    'amount'         => $paymentData['amount'],
                ]);

                if ($paymentData['method'] === 'Credit' && !empty($request->customer_id)) {
                    DB::table('customers')
                        ->where('id', $request->customer_id)
                        ->update([
                            'credit_balance' => DB::raw('COALESCE(credit_balance, 0) + ' . (int) $paymentData['amount']),
                        ]);
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'invoice' => $sale->invoice_number]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'Sale is already cancelled.'], 400);
        }

        try {
            DB::beginTransaction();

            // Restore Stock
            foreach ($sale->items as $item) {
                // Get all batches deducted for this item
                $saleItemBatches = \App\Models\SaleItemBatch::where('sale_item_id', $item->id)->get();
                foreach ($saleItemBatches as $batch) {
                    $stockBatch = \App\Models\StockBatch::find($batch->stock_batch_id);
                    if ($stockBatch) {
                        $stockBatch->increment('remaining_quantity', $batch->quantity);
                    }
                }
            }

            // Reverse Customer Credit (if any)
            $creditPaymentAmount = $sale->payments()->where('payment_method', 'Credit')->sum('amount');
            if ($creditPaymentAmount > 0 && $sale->customer_id) {
                DB::table('customers')
                    ->where('id', $sale->customer_id)
                    ->decrement('credit_balance', $creditPaymentAmount);
            }

            // Status Update
            $sale->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Sale cancelled successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to cancel sale: ' . $e->getMessage()], 500);
        }
    }
}
