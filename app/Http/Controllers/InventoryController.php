<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

use App\Services\StockTransferService;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
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
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Product::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = \App\Models\Category::all();
        return view('inventory.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'current_selling_price' => 'required|numeric|min:0',
            'price_per_pyi' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
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
}
