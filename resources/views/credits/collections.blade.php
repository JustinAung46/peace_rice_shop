@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('credits.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors" title="Back to Credit Report">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Daily Credit Collections</h1>
            <p class="text-sm text-slate-500">Money received for earlier credit sales; this is separate from sales revenue.</p>
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ route('credits.collections') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg transition-colors">Filter Results</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                    <tr>
                        <th class="px-6 py-3">Collection Date</th>
                        <th class="px-6 py-3 text-right">Cash</th>
                        <th class="px-6 py-3 text-right">KPay</th>
                        <th class="px-6 py-3 text-right">Total Collected</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($collections as $collection)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ \Carbon\Carbon::parse($collection->date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right text-emerald-700">{{ number_format($collection->cash) }} Ks</td>
                        <td class="px-6 py-4 text-right text-indigo-700">{{ number_format($collection->kpay) }} Ks</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800">{{ number_format($collection->total) }} Ks</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">No credit collections found for the selected period.</td></tr>
                    @endforelse
                </tbody>
                @if($collections->isNotEmpty())
                <tfoot class="bg-slate-50 font-bold text-slate-800 border-t border-slate-200">
                    <tr>
                        <td class="px-6 py-4">TOTAL</td>
                        <td class="px-6 py-4 text-right text-emerald-700">{{ number_format($collections->sum('cash')) }} Ks</td>
                        <td class="px-6 py-4 text-right text-indigo-700">{{ number_format($collections->sum('kpay')) }} Ks</td>
                        <td class="px-6 py-4 text-right">{{ number_format($collections->sum('total')) }} Ks</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
