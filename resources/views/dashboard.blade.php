@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Dashboard</h1>
    <p class="text-slate-500">Welcome back, here's what's happening at your Rice Shop today.</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Total Sales (Today)</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ number_format($totalSalesToday) }} MMK</h3>
            </div>
            <div class="p-2 bg-emerald-100 rounded-lg">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Rice Sold -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Total Rice Sold (Today)</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalBagsSoldToday }} <span class="text-base font-normal text-slate-500">bags</span></h3>
            </div>
            <div class="p-2 bg-amber-100 rounded-lg">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Transactions (Today)</p>
                <h3 class="text-2xl font-bold text-slate-800">{{ $totalTransactionsToday }}</h3>
            </div>
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Trending Product -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-200">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Top Trending (Month)</p>
                @if($topSellingProducts->count() > 0)
                    <h3 class="text-lg font-bold text-slate-800 truncate" title="{{ $topSellingProducts->first()->product->name }}">{{ $topSellingProducts->first()->product->name }}</h3>
                    <p class="text-xs text-green-600 font-medium">+{{ $topSellingProducts->first()->total_quantity }} bags sold</p>
                @else
                    <h3 class="text-lg font-bold text-slate-400">No Data</h3>
                @endif
            </div>
            <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Sales Chart -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Daily Sales Trend (Last 30 Days)</h3>
    <div class="relative h-72 w-full">
        <canvas id="salesTrendChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Sales by Rice Type -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 lg:col-span-1">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Sales by Rice Type (Month)</h3>
        <div class="relative h-64">
           @if($salesByRiceType->count() > 0)
            <canvas id="riceTypeChart"></canvas>
           @else
            <div class="flex items-center justify-center h-full text-slate-400">No Data Available</div>
           @endif
        </div>
    </div>

    <!-- Stock Status -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-slate-800">Stock Status</h3>
            <span class="px-2 py-1 text-xs font-semibold text-slate-500 bg-slate-100 rounded-full">Total Products: {{ $stockStatus->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50">
                    <tr>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">Stock Level</th>
                        <th class="px-4 py-3 text-right">Qty (Bags)</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockStatus->take(6) as $item)
                    <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $item['name'] }}</td>
                        <td class="px-4 py-3">
                            <div class="w-full bg-slate-200 rounded-full h-2.5">
                                <div class="bg-{{ $item['low_stock'] ? 'red' : 'emerald' }}-500 h-2.5 rounded-full" style="width: {{ min(100, max(5, ($item['current_stock'] / 50) * 100)) }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-bold">{{ $item['current_stock'] }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($item['low_stock'])
                                <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 rounded-full">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                         <td colspan="4" class="px-4 py-3 text-center text-slate-500">No stock data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         @if($stockStatus->count() > 6)
            <div class="mt-4 text-center">
                <a href="{{ route('inventory.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All Inventory &rarr;</a>
            </div>
        @endif
    </div>
</div>

<!-- Alerts & Recent Transactions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
     <!-- Alerts -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
            <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            Alerts & Notifications
        </h3>
        
        <div class="space-y-3">
             @if($lowStockAlerts->count() > 0)
                @foreach($lowStockAlerts as $alert)
                <div class="flex items-start p-3 bg-red-50 border border-red-100 rounded-lg">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Low Stock Alert: {{ $alert['name'] }}</h4>
                        <p class="text-xs text-red-600 mt-1">Only {{ $alert['current_stock'] }} bags remaining. Please restock soon.</p>
                    </div>
                </div>
                @endforeach
            @else
                <div class="flex items-center p-3 bg-slate-50 border border-slate-100 rounded-lg">
                     <svg class="w-5 h-5 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                     </svg>
                     <p class="text-sm text-slate-500">No active alerts. Everything looks good!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Recent Transactions</h3>
        <div class="flow-root">
             <ul role="list" class="-my-4 divide-y divide-slate-100">
                @forelse($recentTransactions as $sale)
                <li class="py-3 flex justify-between items-center hover:bg-slate-50 -mx-4 px-4 transition-colors">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-slate-800">Invoice: {{ $sale->invoice_number }}</span>
                        <span class="text-xs text-slate-500">{{ $sale->created_at->diffForHumans() }}</span>
                    </div>
                     <div class="flex flex-col items-end">
                        <span class="text-sm font-bold text-emerald-600">+{{ number_format($sale->total_amount) }} MMK</span>
                        <span class="text-xs text-slate-500">{{ $sale->items->count() }} items</span>
                    </div>
                </li>
                @empty
                 <li class="py-4 text-center text-slate-500 text-sm">No recent transactions.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Sales Trend Chart ---
        const salesctx = document.getElementById('salesTrendChart').getContext('2d');
        const salesChart = new Chart(salesctx, {
            type: 'line',
            data: {
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'Total Sales (MMK)',
                    data: @json($salesChartData['data']),
                    borderColor: '#10b981', // Emerald 500
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                         ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            },
                             maxTicksLimit: 10
                        }
                    }
                }
            }
        });

        // --- Sales By Rice Type Chart ---
        @if($salesByRiceType->count() > 0)
        const riceCtx = document.getElementById('riceTypeChart').getContext('2d');
        const riceChart = new Chart(riceCtx, {
            type: 'bar', // OR 'doughnut'
            data: {
                labels: @json($salesByRiceType->pluck('name')),
                datasets: [{
                    label: 'Bags Sold',
                    data: @json($salesByRiceType->pluck('quantity')),
                    backgroundColor: [
                        '#f59e0b', // Amber 500
                        '#10b981', // Emerald 500
                        '#3b82f6', // Blue 500
                        '#6366f1', // Indigo 500
                        '#ec4899', // Pink 500
                    ],
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                 plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                     x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
