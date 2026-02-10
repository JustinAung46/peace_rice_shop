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

        $totalSalesToday = Sale::whereDate('created_at', $today)->sum('total_amount');
        
        $totalBagsSoldToday = SaleItem::whereHas('sale', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('quantity');

        $totalTransactionsToday = Sale::whereDate('created_at', $today)->count();

        // 2. Top Selling Products (All time or today? Let's go with Today for consistency with cards, or maybe This Month? 
        // "Top-Selling Rice Type (at least 3 types)" - User didn't specify time range, but usually Trending is better. 
        // Let's do This Month to show broader trends than just today.
        $topSellingProducts = SaleItem::select('product_id', DB::raw('sum(quantity) as total_quantity'))
            ->whereHas('sale', function($q) {
                $q->whereMonth('created_at', Carbon::now()->month);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // If no sales this month, fallback to all time
        if ($topSellingProducts->isEmpty()) {
             $topSellingProducts = SaleItem::select('product_id', DB::raw('sum(quantity) as total_quantity'))
                ->groupBy('product_id')
                ->orderByDesc('total_quantity')
                ->with('product')
                ->take(5)
                ->get();
        }


        // 3. Sales Chart Data (Last 30 Days - Daily Sales Trend)
        $salesChartData = [];
        $dates = [];
        $sales = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates[] = Carbon::now()->subDays($i)->format('d M');
            
            $daySale = Sale::whereDate('created_at', $date)->sum('total_amount');
            $sales[] = $daySale;
        }
        
        $salesChartData = [
            'labels' => $dates,
            'data' => $sales
        ];


        // 4. Sales by Rice Type (For Bar Chart - This Month)
         // Reuse topSelling but maybe fetching all with names
        $salesByRiceType = SaleItem::select('product_id', DB::raw('sum(quantity) as total_quantity'))
            ->whereHas('sale', function($q) {
                 $q->whereMonth('created_at', Carbon::now()->month);
            })
            ->groupBy('product_id')
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'quantity' => $item->total_quantity
                ];
            });

        // 5. Stock Status & Alerts
        // Get all products with their remaining stock (sum of batches)
        // We can do this by iterating products and summing their batches, or query StockBatch logic.
        // Product::with('stockBatches')->get() might be heavy if many batches. 
        // Better:
        $products = Product::all();
        $stockStatus = $products->map(function ($product) {
            $currentStock = $product->stockBatches()->sum('remaining_quantity');
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $currentStock,
                'low_stock' => $currentStock < 10 // Threshold 10
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
