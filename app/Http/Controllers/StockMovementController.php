<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index()
    {
        $movements = \App\Models\StockMovement::with([
            'product', 
            'productVariant',
            'fromWarehouse', 
            'toWarehouse', 
            'targetProduct', 
            'targetVariant',
            'user'
        ])->latest()->paginate(20);

        return view('inventory.movements.index', compact('movements'));
    }
}
