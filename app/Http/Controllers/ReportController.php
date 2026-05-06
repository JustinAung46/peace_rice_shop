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
            ->selectRaw('sale_items.product_id, sale_items.product_variant_id, SUM(sale_items.quantity) as total_quantity, SUM(sale_items.total_price) as total_revenue, SUM(sale_items.total_cost) as total_cost')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('product_variants', 'sale_items.product_variant_id', '=', 'product_variants.id')
            ->whereHas('sale', function($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->whereDate('sale_items.created_at', '>=', $startDate)
            ->whereDate('sale_items.created_at', '<=', $endDate)
            ->groupBy('sale_items.product_id', 'sale_items.product_variant_id', 'categories.name', 'products.name', 'product_variants.name');

        if ($productId) {
            $query->where('sale_items.product_id', $productId);
        }

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $allItemTotals = (clone $query)->get();
        $summary = [
            'total_quantity' => $allItemTotals->sum('total_quantity'),
            'total_revenue' => $allItemTotals->sum('total_revenue'),
            'total_cost' => $allItemTotals->sum('total_cost'),
            'total_profit' => $allItemTotals->sum('total_revenue') - $allItemTotals->sum('total_cost'),
        ];

        $items = $query->orderBy('categories.name')
            ->orderBy('products.name')
            ->orderBy('product_variants.name')
            ->paginate(50)->withQueryString();

        $products = \App\Models\Product::withActiveCategory()->orderBy('name')->get();
        $categories = \App\Models\Category::active()->orderBy('name')->get();

        return view('reports.items', compact('items', 'products', 'categories', 'startDate', 'endDate', 'summary'));
    }

    public function exportSaleItems(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $productId = $request->input('product_id');
        $categoryId = $request->input('category_id');

        $query = SaleItem::with(['product', 'product.category', 'variant'])
            ->selectRaw('sale_items.product_id, sale_items.product_variant_id, SUM(sale_items.quantity) as total_quantity, SUM(sale_items.total_price) as total_revenue, SUM(sale_items.total_cost) as total_cost')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('product_variants', 'sale_items.product_variant_id', '=', 'product_variants.id')
            ->whereHas('sale', function($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->whereDate('sale_items.created_at', '>=', $startDate)
            ->whereDate('sale_items.created_at', '<=', $endDate)
            ->groupBy('sale_items.product_id', 'sale_items.product_variant_id', 'categories.name', 'products.name', 'product_variants.name');

        if ($productId) {
            $query->where('sale_items.product_id', $productId);
        }

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        $items = $query->orderBy('categories.name')
            ->orderBy('products.name')
            ->orderBy('product_variants.name')
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=sale_items_report_{$startDate}_{$endDate}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($items) {
            $file = fopen('php://output', 'w');
            // Adding UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Product', 'Variant', 'Category', 'Total Qty Sold', 'Total Revenue', 'Total Cost', 'Total Profit', 'Margin %']);

            $sumQuantity = 0;
            $sumRevenue = 0;
            $sumCost = 0;

            foreach ($items as $item) {
                $profit = $item->total_revenue - $item->total_cost;
                $margin = $item->total_revenue > 0 ? ($profit / $item->total_revenue) * 100 : 0;
                
                $sumQuantity += $item->total_quantity;
                $sumRevenue += $item->total_revenue;
                $sumCost += $item->total_cost;
                
                fputcsv($file, [
                    $item->product->name,
                    $item->variant ? $item->variant->name : '',
                    $item->product->category ? $item->product->category->name : 'No Category',
                    $item->total_quantity,
                    $item->total_revenue,
                    $item->total_cost,
                    $profit,
                    round($margin, 1) . '%'
                ]);
            }

            $sumProfit = $sumRevenue - $sumCost;
            $sumMargin = $sumRevenue > 0 ? ($sumProfit / $sumRevenue) * 100 : 0;
            
            fputcsv($file, [
                'TOTAL',
                '',
                '',
                $sumQuantity,
                $sumRevenue,
                $sumCost,
                $sumProfit,
                round($sumMargin, 1) . '%'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
