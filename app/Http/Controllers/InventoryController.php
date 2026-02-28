<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Services\StockTransferService;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // ─── Product CRUD ────────────────────────────────────────────────────────

    public function index()
    {
        $products = Product::with(['category', 'variants'])
            ->get()
            ->map(function ($product) {
                // Attach total_stock per variant
                $product->variants->each(function ($variant) {
                    $variant->total_stock = StockBatch::where('product_variant_id', $variant->id)
                        ->sum('remaining_quantity');
                });
                return $product;
            });

        return view('inventory.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Variants array
            'variants'               => 'required|array|min:1',
            'variants.*.name'        => 'required|string|max:255',
            'variants.*.unit_label'  => 'required|string|max:50',
            'variants.*.selling_price' => 'required|integer|min:0',
            'variants.*.sku'               => 'nullable|string|distinct',
            'variants.*.pyi_per_bag'       => 'nullable|integer|min:1',
            'variants.*.price_per_pyi'     => 'nullable|integer|min:0',
            'variants.*.is_active'         => 'nullable|boolean',
            'is_active'                    => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'category_id', 'description']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        foreach ($request->variants as $variantData) {
            $product->variants()->create([
                'name'          => $variantData['name'],
                'unit_label'    => $variantData['unit_label'],
                'selling_price' => $variantData['selling_price'],
                'sku'           => $variantData['sku'] ?? null,
                'pyi_per_bag'   => $variantData['pyi_per_bag'] ?? null,
                'price_per_pyi' => $variantData['price_per_pyi'] ?? null,
                'is_active'     => isset($variantData['is_active']),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $inventory)
    {
        $inventory->load('variants');
        $categories = \App\Models\Category::all();
        return view('inventory.edit', ['product' => $inventory, 'categories' => $categories]);
    }

    public function update(Request $request, Product $inventory)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'variants'               => 'required|array|min:1',
            'variants.*.name'        => 'required|string|max:255',
            'variants.*.unit_label'  => 'required|string|max:50',
            'variants.*.selling_price' => 'required|integer|min:0',
            'variants.*.sku'               => 'nullable|string',
            'variants.*.pyi_per_bag'       => 'nullable|integer|min:1',
            'variants.*.price_per_pyi'     => 'nullable|integer|min:0',
            'variants.*.is_active'         => 'nullable|boolean',
            'is_active'                    => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'category_id', 'description']);
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            if ($inventory->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($inventory->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $inventory->update($data);

        // Sync variants: update existing ones (by id), create new, delete removed
        $incomingIds = collect($request->variants)->pluck('id')->filter()->values();

        // Delete variants not in the incoming list (only if they have no stock)
        $inventory->variants()->whereNotIn('id', $incomingIds)->each(function ($variant) {
            $hasStock = StockBatch::where('product_variant_id', $variant->id)
                ->where('remaining_quantity', '>', 0)->exists();
            if (!$hasStock) {
                $variant->delete();
            }
        });

        foreach ($request->variants as $variantData) {
            $attrs = [
                'name'          => $variantData['name'],
                'unit_label'    => $variantData['unit_label'],
                'selling_price' => $variantData['selling_price'],
                'sku'           => $variantData['sku'] ?? null,
                'pyi_per_bag'   => $variantData['pyi_per_bag'] ?? null,
                'price_per_pyi' => $variantData['price_per_pyi'] ?? null,
                'is_active'     => isset($variantData['is_active']),
            ];

            if (!empty($variantData['id'])) {
                ProductVariant::where('id', $variantData['id'])
                    ->where('product_id', $inventory->id)
                    ->update($attrs);
            } else {
                $inventory->variants()->create($attrs);
            }
        }

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully.');
    }

    // ─── Stock In ────────────────────────────────────────────────────────────

    public function stock(Request $request)
    {
        $products   = Product::with('variants')->get();
        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::all();
        $prefProductId = $request->query('product_id');
        $prefVariantId = $request->query('product_variant_id');
        $prefWarehouseId = $request->query('warehouse_id');
        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'variants' => $p->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'selling_price' => $v->selling_price,
                        'unit_label' => $v->unit_label
                    ];
                })->values()->toArray()
            ];
        })->values()->toArray();
        return view('inventory.stock', compact('products', 'warehouses', 'categories', 'prefProductId', 'prefVariantId', 'prefWarehouseId', 'productsJson'));
    }

    public function storeStock(Request $request)
    {
        $validated = $request->validate([
            'product_id'         => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'warehouse_id'       => 'required|exists:warehouses,id',
            'quantity'           => 'required|integer|min:1',
            'cost_price'         => 'required|integer|min:0',
            'purchase_date'      => 'required|date',
            'batch_code'         => 'nullable|string',
        ]);

        StockBatch::create([
            'product_id'         => $validated['product_id'],
            'product_variant_id' => $validated['product_variant_id'],
            'warehouse_id'       => $validated['warehouse_id'],
            'original_quantity'  => $validated['quantity'],
            'remaining_quantity' => $validated['quantity'],
            'cost_price'         => $validated['cost_price'],
            'purchase_date'      => $validated['purchase_date'],
            'batch_code'         => $validated['batch_code'] ?? null,
        ]);

        StockMovement::create([
            'type'               => 'in',
            'product_id'         => $validated['product_id'],
            'product_variant_id' => $validated['product_variant_id'],
            'to_warehouse_id'    => $validated['warehouse_id'],
            'quantity'           => $validated['quantity'],
            'user_id'            => auth()->id(),
        ]);

        return redirect()->route('inventory.index')->with('success', 'Stock added successfully.');
    }

    // ─── Transfer ─────────────────────────────────────────────────────────────

    public function transfer()
    {
        $products   = Product::with('variants')->get();
        $warehouses = \App\Models\Warehouse::all();
        return view('inventory.transfer', compact('products', 'warehouses'));
    }

    public function storeTransfer(Request $request, StockTransferService $transferService)
    {
        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'from_warehouse_id'  => 'required|exists:warehouses,id',
            'to_warehouse_id'    => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity'           => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($validated['product_variant_id']);

        $transferService->transferVariant(
            $variant->id,
            $validated['from_warehouse_id'],
            $validated['to_warehouse_id'],
            $validated['quantity']
        );

        return redirect()->route('inventory.index')->with('success', 'Stock transferred successfully.');
    }

    // ─── Transform (rice bag size conversion) ────────────────────────────────

    public function transform()
    {
        // Only show products that have at least one variant with pyi_per_bag set
        $products = Product::with(['variants' => function ($q) {
            $q->whereNotNull('pyi_per_bag');
        }])->whereHas('variants', function ($q) {
            $q->whereNotNull('pyi_per_bag');
        })->get();

        $warehouses = \App\Models\Warehouse::all();
        return view('inventory.transform', compact('products', 'warehouses'));
    }

    public function processTransform(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id'        => 'required|exists:warehouses,id',
            'original_variant_id' => 'required|exists:product_variants,id',
            'target_variant_id'   => 'required|exists:product_variants,id|different:original_variant_id',
            'quantity'            => 'required|integer|min:1',
        ]);

        $originalVariant = ProductVariant::findOrFail($validated['original_variant_id']);
        $targetVariant   = ProductVariant::findOrFail($validated['target_variant_id']);

        if (!$originalVariant->pyi_per_bag || !$targetVariant->pyi_per_bag) {
            return back()->with('error', 'Both variants must have "Pyi per Bag" set.')->withInput();
        }

        $totalOriginalStock = StockBatch::where('product_variant_id', $originalVariant->id)
            ->where('warehouse_id', $validated['warehouse_id'])
            ->sum('remaining_quantity');

        if ($totalOriginalStock < $validated['quantity']) {
            return back()->with('error', 'Not enough stock for the original variant in the selected warehouse.')->withInput();
        }

        $quantityToDeduct          = $validated['quantity'];
        $targetQtyPerOriginalBag   = $originalVariant->pyi_per_bag / $targetVariant->pyi_per_bag;

        DB::transaction(function () use ($validated, $originalVariant, $targetVariant, $quantityToDeduct, $targetQtyPerOriginalBag) {
            $batches = StockBatch::where('product_variant_id', $originalVariant->id)
                ->where('warehouse_id', $validated['warehouse_id'])
                ->where('remaining_quantity', '>', 0)
                ->orderBy('purchase_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $remainingToDeduct = $quantityToDeduct;

            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) break;

                $deductAmount = min($batch->remaining_quantity, $remainingToDeduct);
                $batch->remaining_quantity -= $deductAmount;
                $batch->save();

                $remainingToDeduct -= $deductAmount;

                $targetQty  = $deductAmount * $targetQtyPerOriginalBag;
                $targetCost = $batch->cost_price / $targetQtyPerOriginalBag;

                StockBatch::create([
                    'product_id'         => $targetVariant->product_id,
                    'product_variant_id' => $targetVariant->id,
                    'warehouse_id'       => $validated['warehouse_id'],
                    'original_quantity'  => $targetQty,
                    'remaining_quantity' => $targetQty,
                    'cost_price'         => (int) round($targetCost),
                    'purchase_date'      => now(),
                    'batch_code'         => ($batch->batch_code ? $batch->batch_code . '-TR' : 'TR-' . time()),
                ]);
            }

            StockMovement::create([
                'type'              => 'bag_transformation',
                'product_id'        => $originalVariant->product_id,
                'product_variant_id'=> $originalVariant->id,
                'from_warehouse_id' => $validated['warehouse_id'],
                'to_warehouse_id'   => $validated['warehouse_id'],
                'target_product_id' => $targetVariant->product_id,
                'target_variant_id' => $targetVariant->id,
                'quantity'          => $quantityToDeduct,
                'user_id'           => auth()->id(),
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Transformation successful.');
    }
}
