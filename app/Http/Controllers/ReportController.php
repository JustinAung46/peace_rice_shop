<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Daily Stats
        $todaysItems = SaleItem::whereDate('created_at', $today)->get();
        
        $totalRevenue = $todaysItems->sum('total_price');
        $totalProfit = $todaysItems->sum('profit');
        $totalCost = $totalRevenue - $totalProfit;
        
        $margin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Recent Transactions (Last 50)
        $recentSales = Sale::with('items.product')->latest()->take(50)->get();

        return view('reports.index', compact('totalRevenue', 'totalProfit', 'totalCost', 'margin', 'recentSales'));
    }
}
