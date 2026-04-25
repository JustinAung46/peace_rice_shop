<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\StockBatch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount(['stockBatches as total_stock' => function($query) {
            $query->select(DB::raw('sum(remaining_quantity)'));
        }])->get();

        return view('warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        Warehouse::create($request->all());

        return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function show(Warehouse $warehouse)
    {
        $categories = \App\Models\Category::active()->get();
        $rows = StockBatch::where('warehouse_id', $warehouse->id)
            ->where('remaining_quantity', '>', 0)
            ->with(['product.category', 'variant'])
            ->get()
            ->groupBy('product_variant_id')
            ->map(function ($batches) {
                $first = $batches->first();
                return [
                    'product_id'        => $first->product_id,
                    'product_name'      => $first->product->name,
                    'category_id'       => $first->product->category_id,
                    'category_name'     => optional($first->product->category)->name,
                    'variant_id'        => $first->product_variant_id,
                    'variant_name'      => optional($first->variant)->name,
                    'unit_label'        => optional($first->variant)->unit_label,
                    'total_quantity'    => $batches->sum('remaining_quantity'),
                    'last_purchase_date'=> $batches->max('purchase_date'),
                ];
            })
            ->values();

        return view('warehouses.show', [
            'warehouse' => $warehouse,
            'stockRows' => $rows,
            'categories' => $categories
        ]);
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $warehouse->update($request->all());

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has stock
        $hasStock = StockBatch::where('warehouse_id', $warehouse->id)
            ->where('remaining_quantity', '>', 0)
            ->exists();

        if ($hasStock) {
            return back()->with('error', 'Cannot delete warehouse with remaining stock.');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }
}
