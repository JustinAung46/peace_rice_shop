@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('credits.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Credit History</h1>
                <p class="text-slate-500 text-sm mt-0.5">{{ $customer->name }}</p>
            </div>
        </div>
        <button onclick="document.getElementById('payModal').classList.remove('hidden')"
            class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Payment
        </button>
    </div>

    {{-- Customer Summary Card --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-wrap gap-6 items-center">
        <div class="flex-1 min-w-[180px]">
            <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide mb-1">Customer</p>
            <p class="text-xl font-bold text-slate-800">{{ $customer->name }}</p>
            @if($customer->phone)<p class="text-sm text-slate-500 mt-0.5">📞 {{ $customer->phone }}</p>@endif
            @if($customer->address)<p class="text-sm text-slate-500">📍 {{ $customer->address }}</p>@endif
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide mb-1">Outstanding Balance</p>
            <p class="text-3xl font-black {{ $customer->credit_balance > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ number_format($customer->credit_balance) }} Ks
            </p>
            @if($customer->credit_balance == 0)
                <span class="inline-flex items-center gap-1 text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-semibold mt-1">✓ Fully Paid</span>
            @endif
        </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-700">Transaction Timeline</h2>
        </div>

        @if($timeline->isEmpty())
        <div class="text-center py-16 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">No credit history found for this customer</p>
        </div>
        @else
        <div class="divide-y divide-slate-100">
            @foreach($timeline as $entry)
            @if($entry['type'] === 'sale')
            @php $sale = $entry['data']; @endphp
            <div class="px-5 py-4">
                <div class="flex items-start gap-3">
                    {{-- Icon --}}
                    <div class="mt-0.5 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div>
                                <span class="font-semibold text-slate-800">Credit Sale</span>
                                <span class="ml-2 font-mono text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $sale->invoice_number }}</span>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-red-600 text-base">+{{ number_format($entry['amount']) }} Ks <span class="text-xs font-normal text-slate-400">credit</span></p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $sale->created_at->format('d M Y, h:i A') }}</p>

                        {{-- Items --}}
                        @if($sale->items->count())
                        <div class="mt-2 ml-1 space-y-1">
                            @foreach($sale->items as $item)
                            <div class="flex items-center justify-between text-sm text-slate-600 bg-slate-50 rounded px-3 py-1.5">
                                <span>
                                    @if($item->variant && $item->variant->product)
                                        {{ $item->variant->product->name }} – {{ $item->variant->name }}
                                    @else
                                        Item #{{ $item->id }}
                                    @endif
                                    <span class="text-slate-400 text-xs ml-1">× {{ $item->quantity }}</span>
                                </span>
                                <span class="font-medium">{{ number_format($item->total_price) }} Ks</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            @php $payment = $entry['data']; @endphp
            <div class="px-5 py-4 bg-emerald-50/50">
                <div class="flex items-start gap-3">
                    {{-- Icon --}}
                    <div class="mt-0.5 w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    {{-- Content --}}
                    <div class="flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <div>
                                <span class="font-semibold text-slate-800">Payment Received</span>
                                @if($payment->note)
                                    <span class="ml-2 text-xs text-slate-500 italic">{{ $payment->note }}</span>
                                @endif
                            </div>
                            <p class="font-bold text-emerald-600 text-base">-{{ number_format($payment->amount) }} Ks</p>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $payment->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Pay Modal --}}
<div id="payModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Record Credit Payment</h3>
            <button onclick="document.getElementById('payModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('credits.payment.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $customer->id }}">

            <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                <p class="text-xs text-amber-700 font-semibold uppercase tracking-wide mb-1">Customer</p>
                <p class="text-base font-bold text-slate-800">{{ $customer->name }}</p>
                <p class="text-sm text-red-600 font-medium mt-1">Outstanding: {{ number_format($customer->credit_balance) }} Ks</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Amount (Ks)</label>
                <input type="number" name="amount" min="1" max="{{ $customer->credit_balance }}" required
                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-lg font-semibold focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none"
                    placeholder="Enter amount">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Note (optional)</label>
                <input type="text" name="note"
                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none"
                    placeholder="e.g. Cash payment, Bank transfer">
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold text-base hover:bg-emerald-700 active:scale-95 transition-all">
                ✓ Confirm Payment
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endsection
