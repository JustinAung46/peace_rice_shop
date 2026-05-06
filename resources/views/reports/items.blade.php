@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center">
        <a href="{{ route('reports.index') }}" class="mr-4 text-slate-500 hover:text-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Sale Items Report</h1>
    </div>
    
    <a href="{{ route('reports.items.export', request()->query()) }}" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md text-sm font-semibold">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Export CSV
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden">
    <div class="p-6 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
        <h2 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Report
        </h2>
        <form action="{{ route('reports.items') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Start Date</label>
                <div class="relative">
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm pl-10 transition-shadow transition-colors duration-200">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">End Date</label>
                <div class="relative">
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm pl-10 transition-shadow transition-colors duration-200">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                <select name="category_id" class="w-full rounded-xl border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm transition-shadow transition-colors duration-200 hover:border-blue-300 cursor-pointer">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Product</label>
                <select name="product_id" class="w-full rounded-xl border-slate-200 bg-white focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm transition-shadow transition-colors duration-200 hover:border-blue-300 cursor-pointer">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg text-sm font-semibold flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-slate-500 uppercase font-bold text-xs tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Image</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4 text-center">Total Qty Sold</th>
                    <th class="px-6 py-4 text-right">Total Revenue</th>
                    <th class="px-6 py-4 text-right">Total Cost</th>
                    <th class="px-6 py-4 text-right">Margin %</th>
                    <th class="px-6 py-4 text-right">Total Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($items as $item)
                <tr class="hover:bg-blue-50/30 transition-colors duration-150">
                    <td class="px-6 py-4">
                        @if($item->product->image_path)
                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-12 w-12 object-cover rounded-xl border border-slate-200 shadow-sm">
                        @else
                            <div class="h-12 w-12 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl border border-slate-200 shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-slate-800 font-semibold text-base">
                            {{ $item->product->name }}
                            @if($item->variant)
                                <span class="text-sm text-slate-500 font-normal">({{ $item->variant->name }})</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                            {{ $item->product->category->name ?? 'No Category' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-slate-700 bg-slate-50 px-3 py-1 rounded-lg border border-slate-100">{{ number_format($item->total_quantity) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">{{ number_format($item->total_revenue) }} K</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ number_format($item->total_cost) }} K</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-600">
                        @php
                            $profit = $item->total_revenue - $item->total_cost;
                            $margin = $item->total_revenue > 0 ? ($profit / $item->total_revenue) * 100 : 0;
                        @endphp
                        {{ number_format($margin, 1) }}%
                    </td>
                    <td class="px-6 py-4 text-right font-bold {{ ($item->total_revenue - $item->total_cost) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($item->total_revenue - $item->total_cost) }} K
                    </td>
                </tr>
                @endforeach
                
                @if($items->isEmpty())
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-slate-400 bg-slate-50/50">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-lg font-medium text-slate-500">No sales found</p>
                            <p class="text-sm text-slate-400 mt-1">Try adjusting your date range or filters</p>
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
            @if($items->isNotEmpty())
            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right text-slate-700 uppercase tracking-wider text-xs">Total for All Pages</td>
                    <td class="px-6 py-4 text-center text-slate-800">{{ number_format($summary['total_quantity']) }}</td>
                    <td class="px-6 py-4 text-right text-slate-800">{{ number_format($summary['total_revenue']) }} K</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ number_format($summary['total_cost']) }} K</td>
                    <td class="px-6 py-4 text-right text-slate-600">
                        @php
                            $overallMargin = $summary['total_revenue'] > 0 ? ($summary['total_profit'] / $summary['total_revenue']) * 100 : 0;
                        @endphp
                        {{ number_format($overallMargin, 1) }}%
                    </td>
                    <td class="px-6 py-4 text-right {{ $summary['total_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ number_format($summary['total_profit']) }} K
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @if($items->hasPages())
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection

