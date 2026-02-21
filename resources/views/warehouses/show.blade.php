@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('warehouses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-block">&larr; Back to Warehouses</a>
        <h1 class="text-2xl font-bold text-slate-800">{{ $warehouse->name }} - Stock Summary</h1>
        <p class="text-slate-500">{{ $warehouse->location }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <th class="px-6 py-4">Product Name</th>
                    <th class="px-6 py-4 text-right">Available Quantity</th>
                    <th class="px-6 py-4 text-right">Last Purchase</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($stockSummary as $stock)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $stock['product_name'] }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">{{ number_format($stock['total_quantity']) }} Bags</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ \Carbon\Carbon::parse($stock['last_purchase_date'])->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-slate-500">
                        No stock currently in this warehouse.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
