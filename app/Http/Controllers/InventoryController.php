<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('inventory.index', compact('products'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products',
            'current_selling_price' => 'required|numeric|min:0',
            'price_per_pyi' => 'nullable|numeric|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Route binding will automatically fetch the product by ID
        // Route::resource('inventory', ...) expects {inventory} parameter, which defaults to 'inventory' => Product model if type hinted
        // But since standard resource param is singular of resource name, let's verify if we need to be explicit.
        // Usually Laravel automatically resolves it.
        return view('inventory.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'current_selling_price' => 'required|numeric|min:0',
            'price_per_pyi' => 'nullable|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully.');
    }

    public function stock()
    {
        $products = Product::all();
        $warehouses = \App\Models\Warehouse::all();
        return view('inventory.stock', compact('products', 'warehouses'));
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
        return view('inventory.transfer', compact('products', 'warehouses'));
    }

    public function storeTransfer(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $quantityToTransfer = $validated['quantity'];
        $productId = $validated['product_id'];
        $fromId = $validated['from_warehouse_id'];
        $toId = $validated['to_warehouse_id'];

        // Get batches from source warehouse, oldest first (FIFO)
        $batches = \App\Models\StockBatch::where('product_id', $productId)
            ->where('warehouse_id', $fromId)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('purchase_date', 'asc')
            ->get();

        $totalAvailable = $batches->sum('remaining_quantity');

        if ($totalAvailable < $quantityToTransfer) {
            return back()->withErrors(['quantity' => 'Not enough stock in source warehouse. Available: ' . $totalAvailable]);
        }

        \DB::transaction(function () use ($batches, $quantityToTransfer, $toId, $productId) {
            $remainingToTransfer = $quantityToTransfer;

            foreach ($batches as $batch) {
                if ($remainingToTransfer <= 0) break;

                $take = min($batch->remaining_quantity, $remainingToTransfer);
                
                // Deduct from source batch
                $batch->decrement('remaining_quantity', $take);
                
                // Create new batch in destination warehouse with SAME cost price
                \App\Models\StockBatch::create([
                    'product_id' => $productId,
                    'warehouse_id' => $toId,
                    'original_quantity' => $take,
                    'remaining_quantity' => $take,
                    'cost_price' => $batch->cost_price, // PRESERVE COST
                    'purchase_date' => $batch->purchase_date,
                    'batch_code' => $batch->batch_code ? $batch->batch_code . '-TR' : null,
                ]);

                $remainingToTransfer -= $take;
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Stock transferred successfully.');
    }
}
