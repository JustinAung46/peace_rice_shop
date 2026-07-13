@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Back + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('purchase-orders.index') }}" class="mt-1 text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-800">{{ $purchaseOrder->order_number }}</h1>
                    @php
                        $rColors = ['pending'=>'bg-slate-100 text-slate-600','partial'=>'bg-amber-100 text-amber-700','received'=>'bg-green-100 text-green-700'];
                        $pColors = ['unpaid'=>'bg-red-100 text-red-700','partial'=>'bg-amber-100 text-amber-700','paid'=>'bg-green-100 text-green-700'];
                    @endphp
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $rColors[$purchaseOrder->receive_status] }}">
                        {{ ucfirst($purchaseOrder->receive_status) }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $pColors[$purchaseOrder->payment_status] }}">
                        {{ ucfirst($purchaseOrder->payment_status) }}
                    </span>
                </div>
                <p class="text-slate-500 text-sm mt-1">
                    Supplier: <span class="font-medium text-slate-700">{{ $purchaseOrder->supplier->name }}</span>
                    &nbsp;·&nbsp; Order Date: {{ $purchaseOrder->order_date->format('d M Y') }}
                    @if($purchaseOrder->expected_date)
                    &nbsp;·&nbsp; Expected: {{ $purchaseOrder->expected_date->format('d M Y') }}
                    @endif
                </p>
                @if($purchaseOrder->createdBy)
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Created by <span class="font-semibold text-slate-600">{{ $purchaseOrder->createdBy->name }}</span>
                </p>
                @endif
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            @if($purchaseOrder->receive_status === 'pending')
            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
               class="inline-flex items-center gap-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold px-4 py-2.5 rounded-xl transition-colors text-sm border border-amber-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            @endif
            @if($purchaseOrder->receive_status !== 'received')
            <a href="{{ route('purchase-orders.receive', $purchaseOrder) }}"
               class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Receive Goods
            </a>
            @endif
            @if($purchaseOrder->receive_status === 'pending')
            <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}"
                  onsubmit="return confirm('Delete this order? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold px-4 py-2.5 rounded-xl transition-colors text-sm border border-red-200">
                    Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Cost</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($purchaseOrder->total_cost) }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Ks</p>
        </div>
        <div class="bg-green-50 rounded-2xl border border-green-200 shadow-sm p-5 text-center">
            <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Amount Paid</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ number_format($purchaseOrder->amount_paid) }}</p>
            <p class="text-xs text-green-500 mt-0.5">Ks</p>
        </div>
        <div class="bg-red-50 rounded-2xl border border-red-200 shadow-sm p-5 text-center">
            <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Remaining</p>
            <p class="text-2xl font-bold text-red-700 mt-1">{{ number_format($purchaseOrder->amount_remaining) }}</p>
            <p class="text-xs text-red-400 mt-0.5">Ks</p>
        </div>
    </div>

    {{-- Payment Progress Bar --}}
    @if($purchaseOrder->total_cost > 0)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex justify-between text-sm text-slate-600 mb-2">
            <span class="font-medium">Payment Progress</span>
            <span class="font-semibold">{{ $purchaseOrder->payment_percent }}%</span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
            <div class="h-3 rounded-full transition-all duration-500 {{ $purchaseOrder->payment_percent >= 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                 style="width: {{ min(100, $purchaseOrder->payment_percent) }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-slate-400 mt-1.5">
            <span>Paid: {{ number_format($purchaseOrder->amount_paid) }} Ks</span>
            <span>Remaining: {{ number_format($purchaseOrder->amount_remaining) }} Ks</span>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Order Items Table --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden lg:col-span-2">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h2 class="font-semibold text-slate-700">Order Items</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-slate-500 font-semibold text-xs uppercase">Product</th>
                        <th class="text-right px-5 py-3 text-slate-500 font-semibold text-xs uppercase">Ordered</th>
                        <th class="text-right px-5 py-3 text-slate-500 font-semibold text-xs uppercase">Received</th>
                        <th class="text-right px-5 py-3 text-slate-500 font-semibold text-xs uppercase">Remaining</th>
                        <th class="text-right px-5 py-3 text-slate-500 font-semibold text-xs uppercase">Cost/Unit</th>
                        <th class="text-right px-5 py-3 text-slate-500 font-semibold text-xs uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchaseOrder->items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-800">{{ $item->product->name }}</div>
                            @if($item->variant)
                            <div class="text-xs text-slate-400">{{ $item->variant->name }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-700">{{ number_format($item->quantity_ordered) }}</td>
                        <td class="px-5 py-3.5 text-right text-green-700 font-semibold">{{ number_format($item->quantity_received) }}</td>
                        <td class="px-5 py-3.5 text-right {{ $item->quantity_remaining > 0 ? 'text-amber-600 font-semibold' : 'text-slate-400' }}">
                            {{ number_format($item->quantity_remaining) }}
                        </td>
                        <td class="px-5 py-3.5 text-right text-slate-600">{{ number_format($item->cost_price) }} Ks</td>
                        <td class="px-5 py-3.5 text-right font-semibold text-slate-800">{{ number_format($item->subtotal) }} Ks</td>
                    </tr>
                    @endforeach
                    <tr class="bg-slate-50 border-t border-slate-200">
                        <td colspan="5" class="px-5 py-3 text-right font-semibold text-slate-600">Total Order Cost</td>
                        <td class="px-5 py-3 text-right font-bold text-slate-800 text-base">{{ number_format($purchaseOrder->total_cost) }} Ks</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Receipt History --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
                <h2 class="font-semibold text-slate-700">Receive History</h2>
            </div>
            @if($purchaseOrder->receipts->isEmpty())
                <div class="py-10 text-center text-slate-400 text-sm">No goods received yet.</div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($purchaseOrder->receipts as $receipt)
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <span class="font-semibold text-slate-700 text-sm">{{ $receipt->received_date->format('d M Y') }}</span>
                            @if($receipt->receivedBy)
                            <span class="ml-2 inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-600 font-medium px-2 py-0.5 rounded-full">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $receipt->receivedBy->name }}
                            </span>
                            @endif
                        </div>
                        @if($receipt->notes)
                        <span class="text-xs text-slate-400 italic">{{ $receipt->notes }}</span>
                        @endif
                    </div>
                    <div class="space-y-1">
                        @foreach($receipt->receiptItems as $rItem)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600">
                                {{ $rItem->orderItem->product->name }}
                                @if($rItem->orderItem->variant)
                                <span class="text-slate-400">({{ $rItem->orderItem->variant->name }})</span>
                                @endif
                                → <span class="text-indigo-600 font-medium">{{ $rItem->warehouse->name }}</span>
                            </span>
                            <span class="font-semibold text-slate-800">{{ number_format($rItem->quantity) }} units</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Payment History + Make Payment --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <h2 class="font-semibold text-slate-700">Payment History</h2>
            </div>

            {{-- Make Payment Form --}}
            @if($purchaseOrder->amount_remaining > 0)
            <div class="px-5 py-4 border-b border-slate-100 bg-indigo-50">
                <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider mb-3">Record Payment</p>
                <form method="POST" action="{{ route('purchase-orders.payment.store', $purchaseOrder) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Amount (Ks) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" min="1" max="{{ $purchaseOrder->amount_remaining }}"
                                   placeholder="e.g. {{ number_format($purchaseOrder->amount_remaining) }}"
                                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('amount') border-red-400 @enderror"
                                   required>
                            @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-slate-400 mt-0.5">Max: {{ number_format($purchaseOrder->amount_remaining) }} Ks</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Payment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required
                                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Note</label>
                        <input type="text" name="note" placeholder="Optional..."
                               class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors">
                        Record Payment
                    </button>
                </form>
            </div>
            @endif

            {{-- Payment log --}}
            @if($purchaseOrder->payments->isEmpty())
                <div class="py-8 text-center text-slate-400 text-sm">No payments recorded yet.</div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($purchaseOrder->payments->sortByDesc('payment_date') as $payment)
                <div class="px-5 py-3.5 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-green-700">{{ number_format($payment->amount) }} Ks</div>
                        <div class="text-xs text-slate-400">{{ $payment->payment_date->format('d M Y') }}</div>
                        @if($payment->note)
                        <div class="text-xs text-slate-500 italic mt-0.5">{{ $payment->note }}</div>
                        @endif
                        @if($payment->paidBy)
                        <div class="mt-1 inline-flex items-center gap-1 text-xs bg-green-50 text-green-700 font-medium px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Paid by {{ $payment->paidBy->name }}
                        </div>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Totals --}}
            <div class="px-5 py-4 bg-slate-50 border-t border-slate-200 space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Cost</span>
                    <span class="font-semibold text-slate-800">{{ number_format($purchaseOrder->total_cost) }} Ks</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Paid</span>
                    <span class="font-semibold text-green-700">{{ number_format($purchaseOrder->amount_paid) }} Ks</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-1.5">
                    <span class="font-semibold {{ $purchaseOrder->amount_remaining > 0 ? 'text-red-600' : 'text-slate-600' }}">Remaining</span>
                    <span class="font-bold {{ $purchaseOrder->amount_remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($purchaseOrder->amount_remaining) }} Ks
                    </span>
                </div>
            </div>
        </div>

    </div>

    @if($purchaseOrder->notes)
    <div class="bg-slate-50 rounded-2xl border border-slate-200 px-5 py-4 text-sm text-slate-600">
        <span class="font-semibold text-slate-700">Notes:</span> {{ $purchaseOrder->notes }}
    </div>
    @endif
</div>
@endsection
