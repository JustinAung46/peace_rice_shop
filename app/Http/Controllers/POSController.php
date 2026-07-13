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

    /**
     * Check stock availability for an entire cart in batch — no N+1.
     *
     * Instead of querying stock per cart item inside a loop, we:
     *  1. Collect all (variant_id, warehouse_id) pairs from the cart.
     *  2. Pull current stock sums in a single GROUP BY query.
     *  3. Pull all alternative warehouse batches in a single query.
     *  4. Pull all needed variants + their products in a single eager-load.
     */
    public function checkStock(Request $request)
    {
        $cart = $request->cart ?? [];

        if (empty($cart)) {
            return response()->json(['status' => 'ok']);
        }

        // ── 1. Batch-fetch stock sums for all (variant, warehouse) pairs ────────
        $variantIds  = collect($cart)->pluck('variant_id')->unique()->values()->all();
        $warehouseIds = collect($cart)->pluck('warehouse_id')->unique()->values()->all();

        // Single query: SUM per (variant_id, warehouse_id)
        $stockMap = StockBatch::whereIn('product_variant_id', $variantIds)
            ->whereIn('warehouse_id', $warehouseIds)
            ->where('remaining_quantity', '>', 0)
            ->groupBy('product_variant_id', 'warehouse_id')
            ->selectRaw('product_variant_id, warehouse_id, SUM(remaining_quantity) as total')
            ->get()
            ->groupBy('product_variant_id')
            ->map(fn($rows) => $rows->pluck('total', 'warehouse_id'));

        // ── 2. Find which items are insufficient ────────────────────────────────
        $insufficientVariantIds = [];
        $insufficientRequests   = [];

        foreach ($cart as $item) {
            $variantId         = $item['variant_id'];
            $warehouseId       = $item['warehouse_id'];
            $quantityRequested = (int) $item['quantity'];
            $available         = $stockMap[$variantId][$warehouseId] ?? 0;

            if ($available < $quantityRequested) {
                $insufficientVariantIds[] = $variantId;
                $insufficientRequests[]   = [
                    'variant_id'        => $variantId,
                    'warehouse_id'      => $warehouseId,
                    'quantity_requested'=> $quantityRequested,
                    'available'         => $available,
                    'needed'            => $quantityRequested - $available,
                ];
            }
        }

        if (empty($insufficientRequests)) {
            return response()->json(['status' => 'ok']);
        }

        // ── 3. Batch-fetch variants + products for insufficient items ───────────
        $insufficientVariantIds = array_unique($insufficientVariantIds);
        $variantsById = ProductVariant::with('product')
            ->whereIn('id', $insufficientVariantIds)
            ->get()
            ->keyBy('id');

        // ── 4. Batch-fetch alternate warehouse stock for insufficient variants ──
        // One query: find another warehouse that has stock for these variants
        $alternateStock = StockBatch::whereIn('product_variant_id', $insufficientVariantIds)
            ->whereNotIn('warehouse_id', $warehouseIds)
            ->where('remaining_quantity', '>', 0)
            ->groupBy('product_variant_id', 'warehouse_id')
            ->selectRaw('product_variant_id, warehouse_id, SUM(remaining_quantity) as total')
            ->orderByDesc('total')
            ->get()
            ->groupBy('product_variant_id')
            ->map(fn($rows) => $rows->first()); // best alternate warehouse per variant

        // ── 5. Batch-fetch all warehouses needed for the response ───────────────
        $allNeededWarehouseIds = collect($insufficientRequests)->pluck('warehouse_id')
            ->merge(collect($alternateStock)->pluck('warehouse_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $warehousesById = Warehouse::whereIn('id', $allNeededWarehouseIds)
            ->get()
            ->keyBy('id');

        // ── 6. Build the response ────────────────────────────────────────────────
        $insufficientItems = [];
        foreach ($insufficientRequests as $req) {
            $variantId   = $req['variant_id'];
            $warehouseId = $req['warehouse_id'];
            $variant     = $variantsById->get($variantId);
            $alternate   = $alternateStock->get($variantId);

            $fromWarehouseId = $alternate ? $alternate->warehouse_id : null;

            $insufficientItems[] = [
                'variant_id'          => $variantId,
                'product_name'        => $variant
                    ? ($variant->product->name . ' – ' . $variant->name)
                    : 'Unknown',
                'needed'              => $req['needed'],
                'to_warehouse_id'     => $warehouseId,
                'to_warehouse_name'   => $warehousesById->get($warehouseId)?->name ?? 'Unknown',
                'from_warehouse_id'   => $fromWarehouseId,
                'from_warehouse_name' => $fromWarehouseId
                    ? ($warehousesById->get($fromWarehouseId)?->name ?? 'Unknown')
                    : 'No other warehouse has stock',
            ];
        }

        return response()->json(['status' => 'insufficient', 'items' => $insufficientItems]);
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

    public function store(Request $request, \App\Services\ReceiptFormatter $receiptFormatter)
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

        // ── Pre-load all variants + products BEFORE the transaction ─────────────
        // This avoids N+1 queries inside the locked transaction window.
        $variantIds = collect($request->cart)->pluck('variant_id')->unique()->all();
        $variantsById = ProductVariant::with('product')
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        // ── Set a short lock wait timeout to fail fast instead of hanging 50s ───
        // Default InnoDB lock_wait_timeout is 50s which exceeds PHP's 30+2s limit.
        DB::statement('SET SESSION innodb_lock_wait_timeout = 5');

        try {
            DB::beginTransaction();

            $date          = now()->format('Ymd');
            $todayCount    = Sale::whereDate('created_at', now()->toDateString())->count();
            $invoiceNumber = 'INV-' . $date . '-' . ($todayCount + 1);

            // Determine Payment Status
            $totalCreditAmount = collect($request->payments)->where('method', 'Credit')->sum('amount');
            $totalOtherAmount  = collect($request->payments)->where('method', '!=', 'Credit')->sum('amount');

            $paymentStatus = 'paid';
            if ($totalCreditAmount > 0) {
                $paymentStatus = ($totalOtherAmount > 0) ? 'partial' : 'unpaid';
            }

            // Create Sale
            $sale = Sale::create([
                'invoice_number'   => $invoiceNumber,
                'total_amount'     => 0, // updated below
                'payment_method'   => count($request->payments) > 1 ? 'Multi' : $request->payments[0]['method'],
                'customer_id'      => $request->customer_id,
                'sale_type'        => $request->sale_type ?? 'retail',
                'credit_remaining' => $totalCreditAmount,
                'payment_status'   => $paymentStatus,
            ]);

            $totalSaleAmount = 0;

            foreach ($request->cart as $item) {
                // Use pre-loaded variant — no query inside the transaction loop
                $variant           = $variantsById->get($item['variant_id']);
                $quantityRequested = (int) $item['quantity'];
                $unitPrice         = (int) round($item['unit_price']);
                $discount          = (int) round($item['discount'] ?? 0);
                $warehouseId       = $item['warehouse_id'];

                $itemSubtotal      = $unitPrice * $quantityRequested;
                $itemTotal         = $itemSubtotal - $discount;
                $totalSaleAmount  += $itemTotal;

                $saleItem = SaleItem::create([
                    'sale_id'            => $sale->id,
                    'product_id'         => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity'           => $quantityRequested,
                    'unit_price'         => $unitPrice,
                    'cost_price'         => 0, // calculated via FIFO below
                    'total_cost'         => 0,
                    'subtotal'           => $itemSubtotal,
                    'discount'           => $discount,
                    'total_price'        => $itemTotal,
                ]);

                // FIFO Stock Deduction — lockForUpdate is correct here to prevent
                // race conditions between concurrent sales of the same variant.
                $batches = StockBatch::where('product_variant_id', $variant->id)
                    ->where('warehouse_id', $warehouseId)
                    ->where('remaining_quantity', '>', 0)
                    ->orderBy('purchase_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->get();

                $remainingToDeduct    = $quantityRequested;
                $totalCostForThisItem = 0;
                $saleItemBatchInserts = [];

                foreach ($batches as $batch) {
                    if ($remainingToDeduct <= 0) break;
                    $take = min((int) $batch->remaining_quantity, $remainingToDeduct);
                    if ($take <= 0) continue;

                    $batch->decrement('remaining_quantity', $take);
                    $totalCostForThisItem += ($batch->cost_price * $take);

                    $saleItemBatchInserts[] = [
                        'sale_item_id'   => $saleItem->id,
                        'stock_batch_id' => $batch->id,
                        'quantity'       => $take,
                        'cost_price'     => $batch->cost_price,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];

                    $remainingToDeduct -= $take;
                }

                if ($remainingToDeduct > 0) {
                    throw new \Exception("Not enough stock for {$variant->product->name}. Missing: {$remainingToDeduct}");
                }

                // Bulk-insert SaleItemBatch rows — one INSERT instead of N INSERTs
                if (!empty($saleItemBatchInserts)) {
                    \App\Models\SaleItemBatch::insert($saleItemBatchInserts);
                }

                $saleItem->update([
                    'cost_price' => $quantityRequested > 0 ? (int) round($totalCostForThisItem / $quantityRequested) : 0,
                    'total_cost' => $totalCostForThisItem,
                ]);
            }

            $sale->update(['total_amount' => $totalSaleAmount]);

            // Handle Payments
            foreach ($request->payments as $paymentData) {
                SalePayment::create([
                    'sale_id'        => $sale->id,
                    'payment_method' => $paymentData['method'],
                    'amount'         => $paymentData['amount'],
                ]);

                if ($paymentData['method'] === 'Credit' && !empty($request->customer_id)) {
                    DB::table('customers')->where('id', $request->customer_id)
                        ->increment('credit_balance', (int) $paymentData['amount']);
                }
            }

            DB::commit();

            // Refresh sale to get final timestamps/relations for formatter
            $sale->refresh();

            return response()->json([
                'success' => true,
                'invoice' => $sale->invoice_number,
                'receipt' => [
                    'invoiceNumber' => $sale->invoice_number,
                    'dateTime'      => $sale->created_at->format('d M Y H:i'),
                    'customerName'  => $sale->customer?->name ?? 'Walk-in Customer',
                    'total'         => $sale->total_amount,
                    'formatted_receipt' => $receiptFormatter->format($sale),
                ],
                'formatted_receipt' => $receiptFormatter->format($sale),
            ]);

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
