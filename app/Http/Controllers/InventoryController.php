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
        $products = Product::withActiveCategory()->with(['category', 'variants' => function ($q) {
                $q->withSum(['stockBatches as total_stock' => function ($sq) {
                    $sq->where('remaining_quantity', '>', 0);
                }], 'remaining_quantity');
            }])
            ->get();

        $categories = \App\Models\Category::active()->get();

        return view('inventory.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = \App\Models\Category::active()->get();
        return view('inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            // Variants array
            'variants'               => 'required|array|min:1',
            'variants.*.name'        => 'required|string|max:255',
            'variants.*.unit_label'  => 'required|string|max:50',
            'variants.*.selling_price' => 'required|integer|min:0',
            'variants.*.sku'               => 'nullable|string|distinct',
            'variants.*.bag_factor'        => 'required|numeric|min:0',
            'variants.*.retail_price'      => 'nullable|integer|min:0',
            'variants.*.is_active'         => 'nullable|boolean',
            'is_active'                    => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'category_id', 'description']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $data['image_path'] = $file->store('products', 'public');
            
            try {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $img = $manager->read($file->getRealPath());
                $img->cover(300, 300);
                
                $filename = basename($data['image_path']);
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('products/thumbnails');
                \Illuminate\Support\Facades\Storage::disk('public')->put('products/thumbnails/' . $filename, (string) $img->toJpeg(80));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Thumbnail generation failed: ' . $e->getMessage());
            }
        }

        $product = Product::create($data);

        foreach ($request->variants as $variantData) {
            $product->variants()->create([
                'name'          => $variantData['name'],
                'unit_label'    => $variantData['unit_label'],
                'selling_price' => $variantData['selling_price'],
                'sku'           => $variantData['sku'] ?? null,
                'bag_factor'    => $variantData['bag_factor'] ?? null,
                'retail_price'  => $variantData['retail_price'] ?? null,
                'is_active'     => isset($variantData['is_active']),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $inventory)
    {
        $inventory->load('variants');
        $categories = \App\Models\Category::active()->get();
        return view('inventory.edit', ['product' => $inventory, 'categories' => $categories]);
    }

    public function update(Request $request, Product $inventory)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'variants'               => 'required|array|min:1',
            'variants.*.name'        => 'required|string|max:255',
            'variants.*.unit_label'  => 'required|string|max:50',
            'variants.*.selling_price' => 'required|integer|min:0',
            'variants.*.sku'               => 'nullable|string',
            'variants.*.bag_factor'        => 'nullable|numeric|min:0',
            'variants.*.retail_price'      => 'nullable|integer|min:0',
            'variants.*.is_active'         => 'nullable|boolean',
            'is_active'                    => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'category_id', 'description']);
        $data['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            if ($inventory->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($inventory->image_path);
                
                $oldFilename = basename($inventory->image_path);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists('products/thumbnails/' . $oldFilename)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('products/thumbnails/' . $oldFilename);
                }
            }
            $file = $request->file('image');
            $data['image_path'] = $file->store('products', 'public');
            
            try {
                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                $img = $manager->read($file->getRealPath());
                $img->cover(300, 300);
                
                $filename = basename($data['image_path']);
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('products/thumbnails');
                \Illuminate\Support\Facades\Storage::disk('public')->put('products/thumbnails/' . $filename, (string) $img->toJpeg(80));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Thumbnail generation failed: ' . $e->getMessage());
            }
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
                'bag_factor'    => $variantData['bag_factor'] ?? null,
                'retail_price'  => $variantData['retail_price'] ?? null,
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
        $products   = Product::withActiveCategory()->with('variants')->get();
        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::active()->get();
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

    // ─── Batches List ────────────────────────────────────────────────────────
    
    public function batches(Request $request)
    {
        $query = StockBatch::with(['product', 'variant', 'warehouse'])
            ->where('remaining_quantity', '>', 0);
            
        if ($request->has('product_id') && $request->product_id != '') {
            $query->where('product_id', $request->product_id);
        }
        
        if ($request->has('category_id') && $request->category_id != '') {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        if ($request->has('warehouse_id') && $request->warehouse_id != '') {
            $query->where('warehouse_id', $request->warehouse_id);
        }
            
        $batches = $query->orderBy('purchase_date', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate(20)
                         ->withQueryString();
                         
        $products = Product::withActiveCategory()->orderBy('name')->get();
        $categories = \App\Models\Category::active()->orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::orderBy('name')->get();
        
        return view('inventory.batches', compact('batches', 'products', 'categories', 'warehouses'));
    }

    // ─── Transfer ─────────────────────────────────────────────────────────────

    public function transfer()
    {
        $products = Product::withActiveCategory()->where('is_active', true)
            ->with(['variants' => function($q) {
                $q->with(['stockBatches' => function($sq) {
                    $sq->where('remaining_quantity', '>', 0)
                      ->select('id', 'product_variant_id', 'warehouse_id', 'remaining_quantity');
                }]);
            }])
            ->get();
        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::active()->get();
        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'variants' => $p->variants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'selling_price' => $v->selling_price,
                        'unit_label' => $v->unit_label,
                        'stock_batches' => $v->stockBatches
                    ];
                })
            ];
        })->toJson();
        return view('inventory.transfer', compact('products', 'warehouses', 'categories', 'productsJson'));
    }

    public function storeTransfer(Request $request, StockTransferService $transferService)
    {
        $validated = $request->validate([
            'transfers' => 'required|string',
        ]);

        $transfers = json_decode($validated['transfers'], true);

        if (!is_array($transfers) || empty($transfers)) {
            return back()->with('error', 'No valid transfer items provided.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make(['items' => $transfers], [
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.from_warehouse_id'  => 'required|exists:warehouses,id',
            'items.*.to_warehouse_id'    => 'required|exists:warehouses,id|different:items.*.from_warehouse_id',
            'items.*.quantity'           => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($transfers, $transferService) {
                foreach ($transfers['items'] ?? $transfers as $item) {
                    $transferService->transferVariant(
                        $item['product_variant_id'],
                        $item['from_warehouse_id'],
                        $item['to_warehouse_id'],
                        $item['quantity']
                    );
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Stock transferred successfully.');
    }

    // ─── Transform (rice bag size conversion) ────────────────────────────────

    public function transform()
    {
        // Only show products that have at least one variant with bag_factor set
        $products = Product::withActiveCategory()->with(['variants' => function ($q) {
            $q->whereNotNull('bag_factor')
              ->with(['stockBatches' => function($sq) {
                  $sq->where('remaining_quantity', '>', 0)
                    ->select('id', 'product_variant_id', 'warehouse_id', 'remaining_quantity');
              }]);
        }])->whereHas('variants', function ($q) {
            $q->whereNotNull('bag_factor');
        })->get();

        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::active()->get();
        $productsJson = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'variants' => $p->variants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'selling_price' => $v->selling_price,
                        'unit_label' => $v->unit_label,
                        'bag_factor' => $v->bag_factor,
                        'stock_batches' => $v->stockBatches
                    ];
                })
            ];
        })->toJson();
        return view('inventory.transform', compact('products', 'warehouses', 'categories', 'productsJson'));
    }

    public function processTransform(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'transforms'   => 'required|string',
        ]);

        $transforms = json_decode($validated['transforms'], true);

        if (!is_array($transforms) || empty($transforms)) {
            return back()->with('error', 'No valid transformation items provided.');
        }

        $validator = \Illuminate\Support\Facades\Validator::make(['items' => $transforms], [
            'items.*.source_variant_id' => 'required|exists:product_variants,id',
            'items.*.target_variant_id' => 'required|exists:product_variants,id|different:items.*.source_variant_id',
            'items.*.quantity'          => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $warehouseId = $request->input('warehouse_id');
        $rows = $transforms;

        $originalVariantIds = collect($rows)->pluck('source_variant_id')->unique()->filter()->values()->all();
        $targetVariantIds = collect($rows)->pluck('target_variant_id')->unique()->filter()->values()->all();
        $variantIds = array_values(array_unique(array_merge($originalVariantIds, $targetVariantIds)));

        $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        $totalRequested = [];
        foreach ($rows as $index => $row) {
            $original = $variants->get($row['source_variant_id']);
            $target = $variants->get($row['target_variant_id']);

            if (!$original || !$target) {
                return back()->with('error', "Item " . ($index + 1) . ": invalid variant selection.")->withInput();
            }

            if (!$original->bag_factor || !$target->bag_factor) {
                return back()->with('error', "Item " . ($index + 1) . ": both variants must have \"Bag Factor\" set.")->withInput();
            }

            $totalRequested[$original->id] = ($totalRequested[$original->id] ?? 0) + $row['quantity'];
        }

        $availableStock = StockBatch::whereIn('product_variant_id', array_keys($totalRequested))
            ->where('warehouse_id', $warehouseId)
            ->groupBy('product_variant_id')
            ->selectRaw('product_variant_id, SUM(remaining_quantity) as total')
            ->pluck('total', 'product_variant_id')
            ->toArray();

        foreach ($totalRequested as $variantId => $quantity) {
            if (($availableStock[$variantId] ?? 0) < $quantity) {
                return back()->with('error', 'Not enough stock in the selected warehouse for one or more source variants.')->withInput();
            }
        }

        try {
            DB::transaction(function () use ($rows, $warehouseId, $variants) {
                foreach ($rows as $row) {
                    $originalVariant = $variants->get($row['source_variant_id']);
                    $targetVariant   = $variants->get($row['target_variant_id']);
                    $quantityToDeduct = $row['quantity'];
                    $targetQtyPerOriginalBag = $originalVariant->bag_factor / $targetVariant->bag_factor;

                    $batches = StockBatch::where('product_variant_id', $originalVariant->id)
                        ->where('warehouse_id', $warehouseId)
                        ->where('remaining_quantity', '>', 0)
                        ->orderBy('purchase_date', 'asc')
                        ->orderBy('id', 'asc')
                        ->lockForUpdate()
                        ->get();

                    $remainingToDeduct = $quantityToDeduct;
                    foreach ($batches as $batch) {
                        if ($remainingToDeduct <= 0) {
                            break;
                        }

                        $deductAmount = min($batch->remaining_quantity, $remainingToDeduct);
                        $batch->decrement('remaining_quantity', $deductAmount);
                        $remainingToDeduct -= $deductAmount;

                        $targetQty = $deductAmount * $targetQtyPerOriginalBag;
                        $targetCost = $batch->cost_price / $targetQtyPerOriginalBag;

                        StockBatch::create([
                            'product_id'         => $targetVariant->product_id,
                            'product_variant_id' => $targetVariant->id,
                            'warehouse_id'       => $warehouseId,
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
                        'from_warehouse_id' => $warehouseId,
                        'to_warehouse_id'   => $warehouseId,
                        'target_product_id' => $targetVariant->product_id,
                        'target_variant_id' => $targetVariant->id,
                        'quantity'          => $quantityToDeduct,
                        'user_id'           => auth()->id(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Transformation failed: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('inventory.index')->with('success', 'Batch transformation successful.');
    }
}
