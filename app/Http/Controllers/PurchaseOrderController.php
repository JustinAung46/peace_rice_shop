<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderReceipt;
use App\Models\PurchaseOrderReceiptItem;
use App\Models\PurchaseOrderPayment;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier')
            ->orderByDesc('order_date')
            ->orderByDesc('id');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('receive_status')) {
            $query->where('receive_status', $request->receive_status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders    = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        // Summary cards
        $summary = [
            'total_orders'    => PurchaseOrder::count(),
            'pending_receive' => PurchaseOrder::where('receive_status', '!=', 'received')->count(),
            'total_cost'      => PurchaseOrder::sum('total_cost'),
            'total_paid'      => PurchaseOrder::sum('amount_paid'),
            'total_remaining' => PurchaseOrder::selectRaw('SUM(total_cost - amount_paid)')->value(DB::raw('SUM(total_cost - amount_paid)')) ?? 0,
        ];

        return view('purchase-orders.index', compact('orders', 'suppliers', 'summary'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $suppliers  = Supplier::orderBy('name')->get();
        $products   = Product::with('variants')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        $productsJson = json_encode(
            $products->map(fn ($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'variants' => $p->variants->map(fn ($v) => [
                    'id'         => $v->id,
                    'name'       => $v->name,
                    'unit_label' => $v->unit_label,
                ])->values()->toArray(),
            ])->values()->toArray()
        );

        return view('purchase-orders.create', compact('suppliers', 'products', 'warehouses', 'productsJson'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'               => 'required|exists:suppliers,id',
            'order_date'                => 'required|date',
            'expected_date'             => 'nullable|date|after_or_equal:order_date',
            'notes'                     => 'nullable|string|max:1000',
            'items'                     => 'required|array|min:1',
            'items.*.product_id'        => 'required|exists:products,id',
            'items.*.product_variant_id'=> 'nullable|exists:product_variants,id',
            'items.*.quantity_ordered'  => 'required|integer|min:1',
            'items.*.cost_price'        => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $totalCost = collect($validated['items'])
                ->sum(fn($item) => $item['quantity_ordered'] * $item['cost_price']);

            $order = PurchaseOrder::create([
                'supplier_id'    => $validated['supplier_id'],
                'order_number'   => PurchaseOrder::generateOrderNumber(),
                'order_date'     => $validated['order_date'],
                'expected_date'  => $validated['expected_date'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'total_cost'     => $totalCost,
                'amount_paid'    => 0,
                'payment_status' => 'unpaid',
                'receive_status' => 'pending',
                'created_by'     => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id'   => $order->id,
                    'product_id'          => $item['product_id'],
                    'product_variant_id'  => $item['product_variant_id'] ?: null,
                    'quantity_ordered'    => $item['quantity_ordered'],
                    'quantity_received'   => 0,
                    'cost_price'          => $item['cost_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order created successfully.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'supplier',
            'createdBy',
            'items.product',
            'items.variant',
            'receipts.receivedBy',
            'receipts.receiptItems.warehouse',
            'receipts.receiptItems.orderItem.product',
            'receipts.receiptItems.orderItem.variant',
            'payments.paidBy',
        ]);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->receive_status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending orders can be edited.');
        }

        $purchaseOrder->load('items.product', 'items.variant');
        $suppliers  = Supplier::orderBy('name')->get();
        $products   = Product::with('variants')->orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        $productsJson = json_encode(
            $products->map(fn ($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'variants' => $p->variants->map(fn ($v) => [
                    'id'         => $v->id,
                    'name'       => $v->name,
                    'unit_label' => $v->unit_label,
                ])->values()->toArray(),
            ])->values()->toArray()
        );

        $existingItemsJson = json_encode(
            $purchaseOrder->items->map(fn ($item) => [
                'product_id'         => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity_ordered'   => $item->quantity_ordered,
                'cost_price'         => $item->cost_price,
            ])->values()->toArray()
        );

        return view('purchase-orders.edit', compact(
            'purchaseOrder', 'suppliers', 'products', 'warehouses', 'productsJson', 'existingItemsJson'
        ));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->receive_status !== 'pending') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only pending orders can be edited.');
        }

        $validated = $request->validate([
            'supplier_id'                => 'required|exists:suppliers,id',
            'order_date'                 => 'required|date',
            'expected_date'              => 'nullable|date|after_or_equal:order_date',
            'notes'                      => 'nullable|string|max:1000',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity_ordered'   => 'required|integer|min:1',
            'items.*.cost_price'         => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $totalCost = collect($validated['items'])
                ->sum(fn($item) => $item['quantity_ordered'] * $item['cost_price']);

            $purchaseOrder->update([
                'supplier_id'   => $validated['supplier_id'],
                'order_date'    => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes'         => $validated['notes'] ?? null,
                'total_cost'    => $totalCost,
            ]);

            // Replace all items
            $purchaseOrder->items()->delete();

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id'  => $purchaseOrder->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?: null,
                    'quantity_ordered'   => $item['quantity_ordered'],
                    'quantity_received'  => 0,
                    'cost_price'         => $item['cost_price'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order updated successfully.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->receive_status !== 'pending') {
            return back()->with('error', 'Cannot delete an order that has received stock.');
        }

        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted.');
    }

    // ─── Create Receipt Form ──────────────────────────────────────────────────

    public function createReceipt(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->receive_status === 'received') {
            return back()->with('error', 'This order has already been fully received.');
        }

        $purchaseOrder->load(['items.product', 'items.variant']);
        $warehouses = Warehouse::orderBy('name')->get();

        // Only items with remaining quantity
        $pendingItems = $purchaseOrder->items->filter(fn($item) => $item->quantity_remaining > 0);

        return view('purchase-orders.receive', compact('purchaseOrder', 'pendingItems', 'warehouses'));
    }

    // ─── Store Receipt ────────────────────────────────────────────────────────

    public function storeReceipt(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->receive_status === 'received') {
            return back()->with('error', 'This order is already fully received.');
        }

        $validated = $request->validate([
            'received_date'                          => 'required|date',
            'delivery_rate'                          => 'nullable|integer|min:0',
            'notes'                                  => 'nullable|string|max:1000',
            'items'                                  => 'required|array|min:1',
            'items.*.order_item_id'                  => 'required|exists:purchase_order_items,id',
            'items.*.warehouses'                     => 'required|array|min:1',
            'items.*.warehouses.*.warehouse_id'      => 'required|exists:warehouses,id',
            'items.*.warehouses.*.quantity'          => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            $receipt = PurchaseOrderReceipt::create([
                'purchase_order_id' => $purchaseOrder->id,
                'received_date'     => $validated['received_date'],
                'delivery_rate'     => $validated['delivery_rate'] ?? 0,
                'notes'             => $validated['notes'] ?? null,
                'received_by'       => auth()->id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $orderItem = PurchaseOrderItem::findOrFail($itemData['order_item_id']);

                // Security: item must belong to this order
                if ($orderItem->purchase_order_id !== $purchaseOrder->id) {
                    throw new \Exception('Invalid order item.');
                }

                $bagFactor = optional($orderItem->variant)->bag_factor ?? 1.0;
                $deliveryRate = $validated['delivery_rate'] ?? 0;
                
                $purchaseCost = $orderItem->cost_price;
                $deliveryCost = (int) round($deliveryRate * $bagFactor);
                $landedCost = $purchaseCost + $deliveryCost;

                $totalArrived = collect($itemData['warehouses'])->sum('quantity');

                // Must not exceed remaining
                if ($totalArrived > $orderItem->quantity_remaining) {
                    throw new \Exception(
                        "Quantity arrived ({$totalArrived}) exceeds remaining quantity ({$orderItem->quantity_remaining}) for " .
                        optional($orderItem->variant)->name ?? $orderItem->product->name
                    );
                }

                foreach ($itemData['warehouses'] as $whData) {
                    if ((int)$whData['quantity'] <= 0) continue;

                    // Create receipt item
                    $receiptItem = PurchaseOrderReceiptItem::create([
                        'purchase_order_receipt_id' => $receipt->id,
                        'purchase_order_item_id'    => $orderItem->id,
                        'warehouse_id'              => $whData['warehouse_id'],
                        'quantity'                  => $whData['quantity'],
                        'purchase_cost'             => $purchaseCost,
                        'delivery_cost'             => $deliveryCost,
                        'landed_cost'               => $landedCost,
                    ]);

                    // Create stock batch — this is the core financial action
                    $batch = StockBatch::create([
                        'product_id'                     => $orderItem->product_id,
                        'product_variant_id'             => $orderItem->product_variant_id,
                        'warehouse_id'                   => $whData['warehouse_id'],
                        'original_quantity'              => $whData['quantity'],
                        'remaining_quantity'             => $whData['quantity'],
                        'cost_price'                     => $landedCost,
                        'purchase_cost'                  => $purchaseCost,
                        'delivery_cost'                  => $deliveryCost,
                        'landed_cost'                    => $landedCost,
                        'purchase_date'                  => $validated['received_date'],
                        'batch_code'                     => $purchaseOrder->order_number,
                        'purchase_order_receipt_item_id' => $receiptItem->id,
                    ]);

                    // Stock movement record
                    StockMovement::create([
                        'type'               => 'in',
                        'product_id'         => $orderItem->product_id,
                        'product_variant_id' => $orderItem->product_variant_id,
                        'to_warehouse_id'    => $whData['warehouse_id'],
                        'quantity'           => $whData['quantity'],
                        'user_id'            => auth()->id(),
                    ]);
                }

                // Update quantity received on the order item
                $orderItem->increment('quantity_received', $totalArrived);
            }

            // Refresh and recalculate receive status
            $purchaseOrder->refresh();
            $purchaseOrder->load('items');
            $purchaseOrder->recalculateReceiveStatus();
            $purchaseOrder->save();
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Goods received and stock updated successfully.');
    }

    // ─── Store Payment ────────────────────────────────────────────────────────

    public function storePayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $remaining = $purchaseOrder->amount_remaining;

        $validated = $request->validate([
            'amount'       => "required|integer|min:1|max:{$remaining}",
            'payment_date' => 'required|date',
            'note'         => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $purchaseOrder) {
            PurchaseOrderPayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'amount'            => $validated['amount'],
                'payment_date'      => $validated['payment_date'],
                'note'              => $validated['note'] ?? null,
                'paid_by'           => auth()->id(),
            ]);

            $purchaseOrder->increment('amount_paid', $validated['amount']);
            $purchaseOrder->refresh();
            $purchaseOrder->recalculatePaymentStatus();
            $purchaseOrder->save();
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Payment of ' . number_format($validated['amount']) . ' Ks recorded.');
    }
}
