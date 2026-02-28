<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Summary Cards (Today)
        $today = Carbon::today();

        $todayStats = Sale::whereDate('created_at', $today)
            ->selectRaw('count(*) as count, sum(total_amount) as total_amount')
            ->first();

        $totalSalesToday = $todayStats->total_amount ?? 0;
        $totalTransactionsToday = $todayStats->count ?? 0;
        
        $totalBagsSoldToday = SaleItem::whereHas('sale', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('quantity');

        // 2. Top Selling Products (This Month)
        $thisMonth = Carbon::now()->startOfMonth();
        
        $topSellingProducts = SaleItem::select('product_id', DB::raw('sum(quantity) as total_quantity'))
            ->whereHas('sale', function($q) use ($thisMonth) {
                $q->where('created_at', '>=', $thisMonth);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Fallback to all time if empty
        if ($topSellingProducts->isEmpty()) {
             $topSellingProducts = SaleItem::select('product_id', DB::raw('sum(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->orderByDesc('total_quantity')
                ->with('product')
                ->take(5)
                ->get();
        }

        // 3. Sales Chart Data (Last 30 Days - Aggregated Query)
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        
        $dailySales = Sale::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, sum(total_amount) as amount')
            ->groupBy('date')
            ->get()
            ->pluck('amount', 'date');

        $dates = [];
        $sales = [];

        for ($i = 29; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $dates[] = $dateObj->format('d M');
            $sales[] = $dailySales[$dateStr] ?? 0;
        }
        
        $salesChartData = [
            'labels' => $dates,
            'data' => $sales
        ];

        // 4. Sales by Rice Type (This Month) - Reuse logic but more efficient
        $salesByRiceType = SaleItem::select('product_id', DB::raw('sum(quantity) as total_quantity'))
            ->whereHas('sale', function($q) use ($thisMonth) {
                 $q->where('created_at', '>=', $thisMonth);
            })
            ->groupBy('product_id')
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'quantity' => $item->total_quantity
                ];
            });

        // 5. Stock Status & Alerts (Optimized with withSum)
        $stockStatus = Product::withSum(['stockBatches as current_stock' => function($query) {
                $query->where('remaining_quantity', '>', 0);
            }], 'remaining_quantity')
            ->get()
            ->map(function ($product) {
                $currentStock = $product->current_stock ?? 0;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $currentStock,
                    'low_stock' => $currentStock < 10
                ];
            })->sortBy('current_stock');

        $lowStockAlerts = $stockStatus->where('low_stock', true);

        // 6. Recent Transactions
        $recentTransactions = Sale::with('items')
            ->latest()
            ->take(5)
            ->get();
            
        return view('dashboard', compact(
            'totalSalesToday',
            'totalBagsSoldToday',
            'totalTransactionsToday',
            'topSellingProducts',
            'salesChartData',
            'salesByRiceType',
            'stockStatus',
            'lowStockAlerts',
            'recentTransactions'
        ));
    }
}
