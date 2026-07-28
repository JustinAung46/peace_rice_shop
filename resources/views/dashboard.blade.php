@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl text-slate-900 font-bold tracking-tight">Dashboard</h1>
        <p class="text-slate-500 mt-1">Welcome back, here's what's happening at your Rice Shop today.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
            {{ \Carbon\Carbon::now()->format('l, F j, Y') }}
        </span>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl p-6 border border-slate-100 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-emerald-500/10 rounded-full w-24 h-24 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Total Sales (Today)</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalSalesToday) }} <span class="text-sm font-medium text-slate-500">MMK</span></h3>
            </div>
            <div class="p-2.5 bg-emerald-50 rounded-xl ring-1 ring-emerald-100 text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Rice Sold -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl p-6 border border-slate-100 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-amber-500/10 rounded-full w-24 h-24 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Total Rice Sold (Today)</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ $totalBagsSoldToday }} <span class="text-sm font-medium text-slate-500">bags</span></h3>
            </div>
            <div class="p-2.5 bg-amber-50 rounded-xl ring-1 ring-amber-100 text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl p-6 border border-slate-100 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-blue-500/10 rounded-full w-24 h-24 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div>
                <p class="text-slate-500 text-sm font-medium mb-1">Transactions (Today)</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ $totalTransactionsToday }}</h3>
            </div>
            <div class="p-2.5 bg-blue-50 rounded-xl ring-1 ring-blue-100 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Trending Product -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl p-6 border border-slate-100 transition-all duration-300 hover:-translate-y-1 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 bg-indigo-500/10 rounded-full w-24 h-24 group-hover:scale-150 transition-transform duration-500"></div>
        <div class="flex justify-between items-start relative z-10">
            <div class="w-[calc(100%-3.5rem)]">
                <p class="text-slate-500 text-sm font-medium mb-1">Top Trending (Month)</p>
                @if($topSellingProducts->count() > 0)
                    <h3 class="text-lg font-bold text-slate-900 truncate" title="{{ $topSellingProducts->first()->product->name }}">{{ $topSellingProducts->first()->product->name }}</h3>
                    <p class="text-xs text-emerald-600 font-medium mt-1 inline-flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        {{ $topSellingProducts->first()->total_quantity }} bags
                    </p>
                @else
                    <h3 class="text-lg font-bold text-slate-400">No Data</h3>
                @endif
            </div>
            <div class="p-2.5 bg-indigo-50 rounded-xl ring-1 ring-indigo-100 text-indigo-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Sales Chart -->
<div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 mb-8 transition-shadow duration-300">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-slate-900">Daily Sales Trend</h3>
        <span class="text-sm text-slate-500">Last 30 Days</span>
    </div>
    <div class="relative h-72 w-full">
        <canvas id="salesTrendChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Sales by Rice Type -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 lg:col-span-1 transition-shadow duration-300 flex flex-col">
        <h3 class="text-lg font-bold text-slate-900 mb-6">Sales by Rice Type</h3>
        <div class="relative h-64 flex-grow">
           @if($salesByRiceType->count() > 0)
            <canvas id="riceTypeChart"></canvas>
           @else
            <div class="flex items-center justify-center h-full text-slate-400">No Data Available</div>
           @endif
        </div>
    </div>

    <!-- Stock Status -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 lg:col-span-2 transition-shadow duration-300 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-900">Stock Status</h3>
            <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-0.5 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/10">Total: {{ $stockStatus->count() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 rounded-lg">
                    <tr>
                        <th class="px-4 py-3 rounded-l-lg font-semibold">Product</th>
                        <th class="px-4 py-3 font-semibold">Stock Level</th>
                        <th class="px-4 py-3 text-right font-semibold">Qty (Bags)</th>
                        <th class="px-4 py-3 text-center rounded-r-lg font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockStatus->take(6) as $item)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-4 py-3.5 font-medium text-slate-900">{{ $item['name'] }}</td>
                        <td class="px-4 py-3.5">
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden ring-1 ring-inset ring-black/5">
                                <div class="h-full rounded-full transition-all duration-500 group-hover:brightness-110 {{ $item['low_stock'] ? 'bg-red-500' : 'bg-emerald-500' }}" style="width: {{ min(100, max(5, ($item['current_stock'] / 50) * 100)) }}%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-right font-bold text-slate-700">{{ $item['current_stock'] }}</td>
                        <td class="px-4 py-3.5 text-center">
                            @if($item['low_stock'])
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Low Stock</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/10">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                         <td colspan="4" class="px-4 py-8 text-center text-slate-500">No stock data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         @if($stockStatus->count() > 6)
            <div class="mt-4 text-center border-t border-slate-100 pt-4">
                <a href="{{ route('inventory.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center group">
                    View All Inventory
                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Alerts & Recent Transactions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
     <!-- Alerts -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 transition-shadow duration-300">
        <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
            <div class="p-2 bg-amber-50 rounded-lg mr-3 ring-1 ring-amber-100">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            Alerts & Notifications
        </h3>
        
        <div class="space-y-4">
             @if($lowStockAlerts->count() > 0)
                @foreach($lowStockAlerts as $alert)
                <div class="flex items-start p-4 bg-red-50/50 border border-red-100 rounded-xl transition-colors hover:bg-red-50">
                    <div class="bg-red-100 text-red-600 p-1.5 rounded-lg mr-4 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-red-900">Low Stock: {{ $alert['name'] }}</h4>
                        <p class="text-sm text-red-700 mt-1">Only <span class="font-bold">{{ $alert['current_stock'] }}</span> bags remaining. Restock recommended.</p>
                    </div>
                </div>
                @endforeach
            @else
                <div class="flex items-center justify-center p-8 bg-slate-50 border border-dashed border-slate-200 rounded-xl">
                    <div class="text-center">
                         <div class="mx-auto w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mb-3 text-slate-400">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                         </div>
                         <p class="text-sm font-medium text-slate-600">No active alerts</p>
                         <p class="text-xs text-slate-500 mt-1">Everything is looking good!</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 p-6 transition-shadow duration-300">
        <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center">
            <div class="p-2 bg-emerald-50 rounded-lg mr-3 ring-1 ring-emerald-100">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            Recent Transactions
        </h3>
        <div class="flow-root">
             <ul role="list" class="-my-2 divide-y divide-slate-100">
                @forelse($recentTransactions as $sale)
                <li class="py-3.5 flex justify-between items-center hover:bg-slate-50/80 -mx-4 px-4 transition-colors rounded-lg group cursor-pointer">
                    <div class="flex items-center">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 mr-3"></div>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-900 group-hover:text-emerald-600 transition-colors">{{ $sale->invoice_number }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">{{ $sale->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-bold text-slate-900">+{{ number_format($sale->total_amount) }} <span class="text-xs text-slate-500 font-normal">MMK</span></span>
                        <span class="text-xs font-medium text-slate-500 mt-0.5 bg-slate-100 px-2 py-0.5 rounded-full">{{ $sale->items->count() }} items</span>
                    </div>
                </li>
                @empty
                 <li class="py-8 text-center text-slate-500 text-sm border border-dashed border-slate-200 rounded-xl mt-2 bg-slate-50">No recent transactions.</li>
                @endforelse
            </ul>
        </div>
        @if($recentTransactions->count() > 0)
        <div class="mt-6 text-center border-t border-slate-100 pt-4">
             <a href="{{ route('reports.receipts') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center group">
                 View All Transactions
                 <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
             </a>
         </div>
         @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Sales Trend Chart ---
        const salesctx = document.getElementById('salesTrendChart').getContext('2d');
        
        // Create gradient
        let gradient = salesctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald 500
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        const salesChart = new Chart(salesctx, {
            type: 'line',
            data: {
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'Total Sales (MMK)',
                    data: @json($salesChartData['data']),
                    borderColor: '#10b981', // Emerald 500
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4, // Smooth curves
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)', // slate-900
                        titleFont: {
                            size: 13,
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 14,
                            family: "'Inter', sans-serif",
                            weight: 'bold'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('en-US').format(context.parsed.y) + ' MMK';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f8fafc',
                            drawBorder: false,
                        },
                        border: { display: false },
                        ticks: {
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            },
                            color: '#64748b',
                            callback: function(value) {
                                if(value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if(value >= 1000) return (value / 1000).toFixed(1) + 'K';
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        border: { display: false },
                        ticks: {
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            },
                            color: '#94a3b8',
                            maxTicksLimit: 8,
                            maxRotation: 0
                        }
                    }
                }
            }
        });

        // --- Sales By Rice Type Chart ---
        @if($salesByRiceType->count() > 0)
        const riceCtx = document.getElementById('riceTypeChart').getContext('2d');
        const riceChart = new Chart(riceCtx, {
            type: 'doughnut', 
            data: {
                labels: @json($salesByRiceType->pluck('name')),
                datasets: [{
                    label: 'Bags Sold',
                    data: @json($salesByRiceType->pluck('quantity')),
                    backgroundColor: [
                        '#3b82f6', // blue-500
                        '#10b981', // emerald-500
                        '#f59e0b', // amber-500
                        '#8b5cf6', // violet-500
                        '#ec4899', // pink-500
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleFont: {
                            size: 13,
                            family: "'Inter', sans-serif"
                        },
                        bodyFont: {
                            size: 14,
                            family: "'Inter', sans-serif",
                            weight: 'bold'
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed + ' bags';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
