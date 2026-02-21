<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

use App\Services\StockTransferService;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->withSum('stockBatches as total_stock', 'remaining_quantity')
            ->get();
        return view('inventory.index', compact('products'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        return view('inventory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products',
            'current_selling_price' => 'required|numeric|min:0',
            'price_per_pyi' => 'nullable|numeric|min:0',
            'pyi_per_bag' => 'nullable|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        Product::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $inventory)
    {
        $categories = \App\Models\Category::all();
        return view('inventory.edit', ['product' => $inventory, 'categories' => $categories]);
    }

    public function update(Request $request, Product $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $inventory->id,
            'current_selling_price' => 'required|numeric|min:0',
            'price_per_pyi' => 'nullable|numeric|min:0',
            'pyi_per_bag' => 'nullable|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($inventory->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($inventory->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        $inventory->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully.');
    }

    public function stock()
    {
        $products = Product::all();
        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::all();
        return view('inventory.stock', compact('products', 'warehouses', 'categories'));
    }

    public function storeStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.01',
            'cost_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'batch_code' => 'nullable|string'
        ]);

        \App\Models\StockBatch::create([
            'product_id' => $validated['product_id'],
            'warehouse_id' => $validated['warehouse_id'],
            'original_quantity' => $validated['quantity'],
            'remaining_quantity' => $validated['quantity'],
            'cost_price' => $validated['cost_price'],
            'purchase_date' => $validated['purchase_date'],
            'batch_code' => $validated['batch_code'],
        ]);

        return redirect()->route('inventory.index')->with('success', 'Stock added successfully.');
    }

    public function transfer()
    {
        $products = Product::all();
        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::all();
        return view('inventory.transfer', compact('products', 'warehouses', 'categories'));
    }

    public function storeTransfer(Request $request, StockTransferService $transferService)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $transferService->transfer(
            $validated['product_id'],
            $validated['from_warehouse_id'],
            $validated['to_warehouse_id'],
            $validated['quantity']
        );

        return redirect()->route('inventory.index')->with('success', 'Stock transferred successfully.');
    }

    public function transform()
    {
        $products = Product::whereNotNull('pyi_per_bag')->get();
        $warehouses = \App\Models\Warehouse::all();
        $categories = \App\Models\Category::all();
        return view('inventory.transform', compact('products', 'warehouses', 'categories'));
    }

    public function processTransform(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'original_product_id' => 'required|exists:products,id',
            'target_product_id' => 'required|exists:products,id|different:original_product_id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $originalProduct = Product::findOrFail($validated['original_product_id']);
        $targetProduct = Product::findOrFail($validated['target_product_id']);

        if (!$originalProduct->pyi_per_bag || !$targetProduct->pyi_per_bag) {
            return back()->with('error', 'Both products must have a defined Pyi per Bag for transformation.')->withInput();
        }

        // Check stock
        $totalOriginalStock = \App\Models\StockBatch::where('product_id', $originalProduct->id)
            ->where('warehouse_id', $validated['warehouse_id'])
            ->sum('remaining_quantity');

        if ($totalOriginalStock < $validated['quantity']) {
            return back()->with('error', 'Not enough stock for the original product in the selected warehouse.')->withInput();
        }

        $quantityToDeduct = $validated['quantity'];
        
        $targetQuantityPerOriginalBag = $originalProduct->pyi_per_bag / $targetProduct->pyi_per_bag;

        \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $originalProduct, $targetProduct, $quantityToDeduct, $targetQuantityPerOriginalBag) {
            $batches = \App\Models\StockBatch::where('product_id', $originalProduct->id)
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

                // Create stock for target product
                $targetQty = $deductAmount * $targetQuantityPerOriginalBag;
                $targetCost = $batch->cost_price / $targetQuantityPerOriginalBag;

                \App\Models\StockBatch::create([
                    'product_id' => $targetProduct->id,
                    'warehouse_id' => $validated['warehouse_id'],
                    'original_quantity' => $targetQty,
                    'remaining_quantity' => $targetQty,
                    'cost_price' => $targetCost,
                    'purchase_date' => now(), // Or use original batch date
                    'batch_code' => $batch->batch_code ? $batch->batch_code . '-TR' : 'TR-' . time(),
                ]);
            }

            // Record transformation log table
            \App\Models\StockMovement::create([
                'type' => 'bag_transformation',
                'product_id' => $originalProduct->id,
                'from_warehouse_id' => $validated['warehouse_id'],
                'to_warehouse_id' => $validated['warehouse_id'], // stays in same warehouse
                'target_product_id' => $targetProduct->id,
                'quantity' => $quantityToDeduct,
                'user_id' => auth()->id()
            ]);
        });

        return redirect()->route('inventory.index')->with('success', 'Transformation successful.');
    }
}
