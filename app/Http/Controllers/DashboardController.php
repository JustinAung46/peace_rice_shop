<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Summary Cards (Today)
        $today = Carbon::today()->toDateString();

        $todayStats = DB::table('sales')
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total_amount')
            ->first();

        $totalSalesToday        = $todayStats->total_amount ?? 0;
        $totalTransactionsToday = $todayStats->count ?? 0;

        // Bags sold today: join instead of whereHas to avoid correlated subquery
        $totalBagsSoldToday = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereDate('sales.created_at', $today)
            ->where('sales.status', '!=', 'cancelled')
            ->sum('sale_items.quantity');

        // 2. Top Selling Products (This Month)
        $thisMonth = Carbon::now()->startOfMonth()->toDateTimeString();

        // Map to objects compatible with what the view expects: $item->product->name
        $topSellingProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.created_at', '>=', $thisMonth)
            ->where('sales.status', '!=', 'cancelled')
            ->groupBy('sale_items.product_id', 'products.name')
            ->selectRaw('sale_items.product_id, products.name as product_name, SUM(sale_items.quantity) as total_quantity')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get()
            ->map(fn($row) => (object)[
                'product'        => (object)['name' => $row->product_name],
                'total_quantity' => $row->total_quantity,
                'product_id'     => $row->product_id,
            ]);

        // Fallback to all time if empty
        if ($topSellingProducts->isEmpty()) {
            $topSellingProducts = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.status', '!=', 'cancelled')
                ->groupBy('sale_items.product_id', 'products.name')
                ->selectRaw('sale_items.product_id, products.name as product_name, SUM(sale_items.quantity) as total_quantity')
                ->orderByDesc('total_quantity')
                ->take(5)
                ->get()
                ->map(fn($row) => (object)[
                    'product'        => (object)['name' => $row->product_name],
                    'total_quantity' => $row->total_quantity,
                    'product_id'     => $row->product_id,
                ]);
        }

        // 3. Sales Chart Data (Last 30 Days — single aggregated query)
        $startDate = Carbon::now()->subDays(29)->startOfDay()->toDateTimeString();

        $dailySales = DB::table('sales')
            ->where('created_at', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount')
            ->groupBy('date')
            ->pluck('amount', 'date');

        $dates = [];
        $sales  = [];

        for ($i = 29; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $dateStr = $dateObj->format('Y-m-d');
            $dates[] = $dateObj->format('d M');
            $sales[] = $dailySales[$dateStr] ?? 0;
        }

        $salesChartData = [
            'labels' => $dates,
            'data'   => $sales,
        ];

        // salesByRiceType — kept as a Collection of arrays for ->pluck() compatibility in the view
        $salesByRiceType = collect(DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.created_at', '>=', $thisMonth)
            ->where('sales.status', '!=', 'cancelled')
            ->groupBy('sale_items.product_id', 'products.name')
            ->selectRaw('products.name, SUM(sale_items.quantity) as quantity')
            ->get()
            ->map(fn($row) => ['name' => $row->name, 'quantity' => $row->quantity])
            ->toArray());


        // 5. Stock Status & Alerts — single aggregated query, no N+1
        $stockRows = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin(
                DB::raw('(SELECT product_id, SUM(remaining_quantity) as current_stock
                          FROM stock_batches
                          WHERE remaining_quantity > 0
                          GROUP BY product_id) as sb'),
                'products.id',
                '=',
                'sb.product_id'
            )
            ->where('categories.is_active', true)
            ->where('products.is_active', true)
            ->select('products.id', 'products.name', DB::raw('COALESCE(sb.current_stock, 0) as current_stock'))
            ->orderBy('current_stock')
            ->get();

        $stockStatus = $stockRows->map(fn($row) => [
            'id'            => $row->id,
            'name'          => $row->name,
            'current_stock' => $row->current_stock,
            'low_stock'     => $row->current_stock < 10,
        ]);

        $lowStockAlerts = $stockStatus->where('low_stock', true);

        // 6. Recent Transactions
        $recentTransactions = Sale::with('items')->latest()->take(5)->get();

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
