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

        // Calculate stats manually since profit column is removed
        $todaysItems = SaleItem::whereHas('sale', function($q) {
            $q->where('status', '!=', 'cancelled');
        })->whereDate('created_at', $today)->get();
        
        $totalRevenue = $todaysItems->sum('total_price');
        $totalCost = $todaysItems->sum('total_cost');
        $totalProfit = $totalRevenue - $totalCost;
        
        $margin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Recent Transactions (Last 50)
        $recentSales = Sale::with('items.product', 'items.variant', 'customer')->latest()->take(50)->get();

        return view('reports.index', compact('totalRevenue', 'totalProfit', 'totalCost', 'margin', 'recentSales'));
    }

    public function dailyReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $dailyStats = SaleItem::selectRaw('DATE(created_at) as date, 
                                        COUNT(DISTINCT sale_id) as transaction_count, 
                                        SUM(total_price) as revenue,
                                        SUM(total_cost) as total_cost')
            ->whereHas('sale', function($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $dailyStats->map(function ($stat) {
            $stat->profit = $stat->revenue - $stat->total_cost;
            return $stat;
        });

        return view('reports.daily', compact('dailyStats', 'startDate', 'endDate'));
    }

    public function saleItemsReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $productId = $request->input('product_id');
        $categoryId = $request->input('category_id');

        $query = SaleItem::with(['product', 'product.category', 'variant'])
            ->selectRaw('product_id, product_variant_id, SUM(quantity) as total_quantity, SUM(total_price) as total_revenue, SUM(total_cost) as total_cost')
            ->whereHas('sale', function($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('product_id', 'product_variant_id');

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($categoryId) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $items = $query->orderByDesc('total_revenue')->paginate(50)->withQueryString();

        $products = \App\Models\Product::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('reports.items', compact('items', 'products', 'categories', 'startDate', 'endDate'));
    }

    public function receipts(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        
        $query = Sale::with(['customer', 'items.variant.product', 'payments']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        
        if ($request->filled('invoice_number')) {
            $query->where('invoice_number', 'like', '%' . $request->input('invoice_number') . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $receipts = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('reports.receipts', compact('receipts', 'customers', 'startDate', 'endDate'));
    }
}
