@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Daily Profit Report</h1>
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
                    <td class="px-6 py-4">
                        <ul class="list-disc list-inside">
                        @foreach($sale->items as $item)
                            <li>{{ $item->product->name }} x {{ $item->quantity }}</li>
                        @endforeach
                        </ul>
                    </td>
                    <td class="px-6 py-4 text-right font-medium">{{ number_format($sale->total_amount) }} K</td>
                    <td class="px-6 py-4 text-right text-emerald-600 font-bold">{{ number_format($sale->items->sum('profit')) }} K</td>
                </tr>
                @endforeach
                
                @if($recentSales->isEmpty())
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">No sales recorded today.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
