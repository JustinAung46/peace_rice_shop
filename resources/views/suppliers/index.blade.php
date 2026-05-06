@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Suppliers</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your product suppliers</p>
        </div>
        <a href="{{ route('suppliers.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Supplier
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if($suppliers->isEmpty())
            <div class="py-20 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                </svg>
                <p class="text-slate-500">No suppliers yet. <a href="{{ route('suppliers.create') }}" class="text-indigo-600 font-medium hover:underline">Add one</a>.</p>
            </div>
        @else
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-6 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Supplier</th>
                    <th class="text-left px-6 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Phone</th>
                    <th class="text-left px-6 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Orders</th>
                    <th class="text-right px-6 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Outstanding Balance</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($suppliers as $supplier)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800">{{ $supplier->name }}</div>
                        @if($supplier->address)
                        <div class="text-slate-400 text-xs mt-0.5">{{ Str::limit($supplier->address, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $supplier->phone ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('purchase-orders.index', ['supplier_id' => $supplier->id]) }}"
                           class="text-indigo-600 hover:underline font-medium">
                            {{ $supplier->purchase_orders_count }} order(s)
                        </a>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($supplier->total_outstanding > 0)
                            <span class="font-semibold text-red-600">{{ number_format($supplier->total_outstanding) }} Ks</span>
                        @else
                            <span class="text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}"
                           class="inline-flex items-center gap-1 text-slate-600 hover:text-indigo-600 font-medium text-xs border border-slate-200 rounded-lg px-3 py-1.5 hover:border-indigo-300 transition-colors">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline"
                              onsubmit="return confirm('Delete this supplier? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 font-medium text-xs border border-red-200 rounded-lg px-3 py-1.5 hover:border-red-400 transition-colors">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
