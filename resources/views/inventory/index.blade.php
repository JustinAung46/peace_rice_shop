@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Inventory Management</h1>
    <a href="{{ route('inventory.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        + Add New Product
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <th class="px-6 py-4">Product Name</th>
                    <th class="px-6 py-4">SKU</th>
                    <th class="px-6 py-4 text-right">Selling Price</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $product)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $product->name }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $product->sku ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">{{ number_format($product->current_selling_price) }} MMK</td>
                    <td class="px-6 py-4 text-center">
                        <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                        No products found. Start by adding one!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
