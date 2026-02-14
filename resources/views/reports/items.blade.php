@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div class="flex items-center">
        <a href="{{ route('reports.index') }}" class="mr-4 text-slate-500 hover:text-slate-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Sale Items Report</h1>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 mb-8">
    <form action="{{ route('reports.items') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">End Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
            <select name="category_id" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Product</label>
            <select name="product_id" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 text-sm font-medium h-10">
            Apply Filters
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 uppercase font-medium border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Image</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Invoice</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4 text-center">Qty</th>
                    <th class="px-6 py-4 text-right">Unit Price</th>
                    <th class="px-6 py-4 text-right">Revenue</th>
                    <th class="px-6 py-4 text-right">Cost</th>
                    <th class="px-6 py-4 text-right">Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($items as $item)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        @if($item->product->image_path)
                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product->name }}" class="h-10 w-10 object-cover rounded-lg border border-slate-200">
                        @else
                            <div class="h-10 w-10 flex items-center justify-center bg-slate-100 text-slate-400 rounded-lg border border-slate-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-slate-800 font-medium">{{ $item->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $item->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-blue-600 font-medium">{{ $item->sale->invoice_number }}</span>
                        <div class="text-xs text-slate-400">{{ $item->sale->customer->name ?? 'Walk-in' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-slate-800">{{ $item->product->name }}</div>
                        <div class="text-xs text-slate-400">{{ $item->product->category->name ?? 'No Category' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center font-medium">{{ number_format($item->quantity) }}</td>
                    <td class="px-6 py-4 text-right">{{ number_format($item->unit_price) }} K</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">{{ number_format($item->total_price) }} K</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ number_format($item->cost_price * $item->quantity) }} K</td>
                    <td class="px-6 py-4 text-right font-bold {{ ($item->total_price - ($item->cost_price * $item->quantity)) >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ number_format($item->total_price - ($item->cost_price * $item->quantity)) }} K
                    </td>
                </tr>
                @endforeach
                
                @if($items->isEmpty())
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        No items found matching the selected filters.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
