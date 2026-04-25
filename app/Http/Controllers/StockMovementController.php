<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\StockMovement::with([
            'product', 
            'productVariant',
            'fromWarehouse', 
            'toWarehouse', 
            'targetProduct', 
            'targetVariant',
            'user'
        ]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('product_id')) {
            $query->where(function($q) use ($request) {
                $q->where('product_id', $request->product_id)
                  ->orWhere('target_product_id', $request->product_id);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->latest()->paginate(20)->withQueryString();

        $types = \App\Models\StockMovement::distinct()->whereNotNull('type')->pluck('type');
        $products = \App\Models\Product::withActiveCategory()->orderBy('name')->get();
        $categories = \App\Models\Category::active()->orderBy('name')->get();

        return view('inventory.movements.index', compact('movements', 'types', 'products', 'categories'));
    }
}
