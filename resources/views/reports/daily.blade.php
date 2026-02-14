@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center">
        <a href="{{ route('reports.index') }}" class="mr-4 text-slate-500 hover:text-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Daily Sales Report</h1>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
    <form action="{{ route('reports.daily') }}" method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200">
            Filter Results
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                <tr>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3 text-center">Transactions</th>
                    <th class="px-6 py-3 text-right">Revenue</th>
                    <th class="px-6 py-3 text-right">Cost</th>
                    <th class="px-6 py-3 text-right">Profit</th>
                    <th class="px-6 py-3 text-right">Margin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($dailyStats as $stat)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ \Carbon\Carbon::parse($stat->date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">{{ $stat->transaction_count }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">{{ number_format($stat->revenue) }} K</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ number_format($stat->total_cost) }} K</td>
                    <td class="px-6 py-4 text-right font-bold {{ $stat->profit >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($stat->profit) }} K
                    </td>
                    <td class="px-6 py-4 text-right">
                        @php $margin = $stat->revenue > 0 ? ($stat->profit / $stat->revenue) * 100 : 0; @endphp
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $margin >= 20 ? 'bg-emerald-100 text-emerald-700' : ($margin >= 10 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                            {{ number_format($margin, 1) }}%
                        </span>
                    </td>
                </tr>
                @endforeach
                
                @if($dailyStats->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400">No data found for the selected period.</td>
                </tr>
                @endif
            </tbody>
            @if(!$dailyStats->isEmpty())
            <tfoot class="bg-slate-50 font-bold text-slate-800 border-t border-slate-200">
                <tr>
                    <td class="px-6 py-4">TOTAL</td>
                    <td class="px-6 py-4 text-center">{{ $dailyStats->sum('transaction_count') }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($dailyStats->sum('revenue')) }} K</td>
                    <td class="px-6 py-4 text-right text-slate-600">{{ number_format($dailyStats->sum('total_cost')) }} K</td>
                    <td class="px-6 py-4 text-right text-emerald-700">{{ number_format($dailyStats->sum('profit')) }} K</td>
                    <td class="px-6 py-4 text-right">
                        @php 
                            $totalRevenue = $dailyStats->sum('revenue');
                            $totalProfit = $dailyStats->sum('profit');
                            $totalMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
                        @endphp
                        {{ number_format($totalMargin, 1) }}%
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
