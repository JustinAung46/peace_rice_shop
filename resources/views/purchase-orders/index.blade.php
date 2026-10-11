@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Purchase Orders</h1>
            <p class="text-slate-500 text-sm mt-1">Track orders from your suppliers</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Order
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            @foreach(request()->only(['supplier_id', 'receive_status', 'payment_status', 'date_from', 'date_to']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Summary From</label>
                <input type="date" name="summary_date_from" value="{{ request('summary_date_from') }}"
                       class="border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Summary To</label>
                <input type="date" name="summary_date_to" value="{{ request('summary_date_to') }}"
                       class="border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-xl text-sm transition-colors">Apply to Cards</button>
            <a href="{{ route('purchase-orders.index', request()->only(['supplier_id', 'receive_status', 'payment_status', 'date_from', 'date_to'])) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-5 py-2 rounded-xl text-sm transition-colors">Clear</a>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Orders</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $summary['total_orders'] }}</p>
        </div>
        <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Pending Receive</p>
            <p class="text-3xl font-bold text-amber-700 mt-1">{{ $summary['pending_receive'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Cost</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($summary['total_cost']) }} <span class="text-sm font-normal text-slate-500">Ks</span></p>
        </div>
        <div class="bg-red-50 rounded-2xl border border-red-200 p-5 shadow-sm">
            <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Unpaid Balance</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ number_format($summary['total_cost'] - $summary['total_paid']) }} <span class="text-sm font-normal text-red-500">Ks</span></p>
        </div>
    </div>

    @if((request()->filled('summary_date_from') || request()->filled('summary_date_to')) && $summary['total_orders'] === 0)
        <p class="text-sm text-slate-500 -mt-2">No purchase orders fall within the selected summary period.</p>
    @endif

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
    <form method="GET" class="flex flex-wrap items-center gap-3">

        {{-- Keep summary date filters --}}
        @foreach(request()->only(['summary_date_from', 'summary_date_to']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach

        {{-- Supplier --}}
        <div class="w-44">
            <label for="supplier_id" class="block text-xs font-medium text-slate-500 mb-1.5">
                Supplier
            </label>
            <select
                id="supplier_id"
                name="supplier_id"
                class="w-full h-10 border border-slate-200 rounded-2xl px-3 text-sm bg-slate-50
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">All Suppliers</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}"
                        {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                        {{ $sup->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Receiving Status --}}
        <div class="w-42">
            <label for="receive_status" class="block text-xs font-medium text-slate-500 mb-1.5">
                Receiving
            </label>
            <select
                id="receive_status"
                name="receive_status"
                class="w-full h-10 border border-slate-200 rounded-2xl px-3 text-sm bg-slate-50
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All</option>
                <option value="pending" {{ request('receive_status') == 'pending' ? 'selected' : '' }}>
                    Pending
                </option>
                <option value="partial" {{ request('receive_status') == 'partial' ? 'selected' : '' }}>
                    Partial
                </option>
                <option value="received" {{ request('receive_status') == 'received' ? 'selected' : '' }}>
                    Received
                </option>
            </select>
        </div>

        {{-- Payment Status --}}
        <div class="w-42">
            <label for="payment_status" class="block text-xs font-medium text-slate-500 mb-1.5">
                Payment
            </label>
            <select
                id="payment_status"
                name="payment_status"
                class="w-full h-10 border border-slate-200 rounded-2xl px-3 text-sm bg-slate-50
                       focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
            >
                <option value="">All</option>
                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>
                    Unpaid
                </option>
                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>
                    Partial
                </option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>
                    Paid
                </option>
            </select>
        </div>

        {{-- Date Range --}}
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">
                Order Date
            </label>

            <div class="flex items-center gap-2">
                <input
                    id="date_from"
                    type="date"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="w-36 h-10 border border-slate-200 rounded-lg px-3 text-sm bg-slate-50
                           focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >

                <span class="text-slate-400 text-sm">to</span>

                <input
                    id="date_to"
                    type="date"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="w-36 h-10 border border-slate-200 rounded-lg px-3 text-sm bg-slate-50
                           focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-end gap-2 h-full pt-5">
            <button
                type="submit"
                class="h-10 px-4 bg-indigo-600 hover:bg-indigo-700 text-white
                       font-medium text-sm rounded-lg transition-colors shadow-sm"
            >
                Filter
            </button>

            <a
                href="{{ route('purchase-orders.index', request()->only(['summary_date_from', 'summary_date_to'])) }}"
                class="h-10 px-3 flex items-center justify-center
                       text-slate-500 hover:text-slate-800
                       rounded-lg hover:bg-slate-100 transition-colors"
                title="Clear filters"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8 8 0 004.582 9
                             m0 0H9m11 11v-5h-.581m0 0a8.003 8.003
                             0 01-15.357-2m15.357 2H15"/>
                </svg>
            </a>
        </div>

    </form>
</div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($orders->isEmpty())
            <div class="py-16 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-slate-500">No purchase orders found.</p>
            </div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Order #</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Supplier</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Date</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Receive</th>
                    <th class="text-right px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Total Cost</th>
                    <th class="text-right px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Paid</th>
                    <th class="text-right px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Remaining</th>
                    <th class="text-left px-5 py-3 text-slate-500 font-semibold uppercase text-xs tracking-wider">Payment</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($orders as $order)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-4 font-mono font-semibold text-slate-700">{{ $order->order_number }}</td>
                    <td class="px-5 py-4 text-slate-800">{{ $order->supplier->name }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $order->order_date->format('d M Y') }}</td>
                    <td class="px-5 py-4">
                        @php
                            $rColors = ['pending'=>'bg-slate-100 text-slate-600','partial'=>'bg-amber-100 text-amber-700','received'=>'bg-green-100 text-green-700'];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $rColors[$order->receive_status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($order->receive_status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right font-semibold text-slate-800">{{ number_format($order->total_cost) }} Ks</td>
                    <td class="px-5 py-4 text-right text-green-700 font-semibold">{{ number_format($order->amount_paid) }} Ks</td>
                    <td class="px-5 py-4 text-right font-semibold {{ $order->amount_remaining > 0 ? 'text-red-600' : 'text-slate-400' }}">
                        {{ number_format($order->amount_remaining) }} Ks
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $pColors = ['unpaid'=>'bg-red-100 text-red-700','partial'=>'bg-amber-100 text-amber-700','paid'=>'bg-green-100 text-green-700'];
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pColors[$order->payment_status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="inline-flex items-center gap-2">
                            @if($order->receive_status === 'pending')
                            <a href="{{ route('purchase-orders.edit', $order) }}"
                               class="text-amber-600 hover:text-amber-800 font-semibold text-xs border border-amber-200 rounded-lg px-3 py-1.5 hover:border-amber-400 transition-colors">
                                Edit
                            </a>
                            @endif
                            <a href="{{ route('purchase-orders.show', $order) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-semibold text-xs border border-indigo-200 rounded-lg px-3 py-1.5 hover:border-indigo-400 transition-colors">
                                View
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
