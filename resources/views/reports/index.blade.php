@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Sales Reports</h1>
    <div class="flex space-x-3">
        <a href="{{ route('reports.daily') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Daily Report
        </a>
        <a href="{{ route('reports.items') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Sale Items Report
        </a>
    </div>
</div>

<div class="mb-6">
    <h2 class="text-xl font-semibold text-slate-700">Today's Overview</h2>
    <span class="text-sm text-slate-500">{{ \Carbon\Carbon::today()->format('d M Y') }}</span>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm font-medium text-slate-500 mb-1">Total Revenue</p>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($totalRevenue) }} K</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm font-medium text-slate-500 mb-1">Cost of Goods (FIFO)</p>
        <p class="text-2xl font-bold text-slate-600">{{ number_format($totalCost) }} K</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-emerald-200 bg-emerald-50">
        <p class="text-sm font-medium text-emerald-700 mb-1">Net Profit</p>
        <p class="text-2xl font-bold text-emerald-700">{{ number_format($totalProfit) }} K</p>
    </div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm font-medium text-slate-500 mb-1">Profit Margin</p>
        <p class="text-2xl font-bold text-indigo-600">{{ number_format($margin, 1) }}%</p>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
        <h2 class="text-lg font-semibold text-slate-800">Recent Transactions</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                <tr>
                    <th class="px-6 py-3">Invoice</th>
                    <th class="px-6 py-3">Time</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3">Items</th>
                    <th class="px-6 py-3 text-right">Revenue</th>
                    <th class="px-6 py-3 text-right">Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($recentSales as $sale)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $sale->invoice_number }}</td>
                    <td class="px-6 py-4">{{ $sale->created_at->format('h:i A') }}</td>
                    <td class="px-6 py-4">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td class="px-6 py-4">
                        <ul class="list-disc list-inside">
                        @foreach($sale->items as $item)
                            <li>{{ $item->product->name }} x {{ $item->quantity }}</li>
                        @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4 text-right font-medium">{{ number_format($sale->total_amount) }} K</td>
                    <td class="px-6 py-4 text-right text-emerald-600 font-bold">
                        @php
                            $saleProfit = $sale->items->sum(function($item) {
                                return $item->total_price - ($item->cost_price * $item->quantity);
                            });
                        @endphp
                        {{ number_format($saleProfit) }} K
                    </td>
                </tr>
                @endforeach
                
                @if($recentSales->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400">No sales recorded yet.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
