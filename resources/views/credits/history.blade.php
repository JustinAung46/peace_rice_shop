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
                <h1 class="text-2xl font-bold text-slate-800">Credit Invoices</h1>
                <p class="text-slate-500 text-sm mt-0.5">{{ $customer->name }}</p>
            </div>
        </div>
        <button onclick="openGeneralPayModal()"
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

    {{-- Invoices Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-semibold text-slate-700">Credit Invoices</h2>
            <span class="text-sm text-slate-400">{{ $creditSales->count() }} record(s)</span>
        </div>

        @if($creditSales->isEmpty())
        <div class="text-center py-16 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">No credit sales found for this customer</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <th class="text-left px-5 py-3 font-semibold">Invoice Number</th>
                        <th class="text-left px-5 py-3 font-semibold">Date & Time</th>
                        <th class="text-right px-5 py-3 font-semibold">Total Amount</th>
                        <th class="text-right px-5 py-3 font-semibold">Pay Amount</th>
                        <th class="text-right px-5 py-3 font-semibold">Remaining</th>
                        <th class="text-center px-5 py-3 font-semibold">Status</th>
                        <th class="text-center px-5 py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($creditSales as $sale)
                    @php
                        $paidAmount = $sale->total_amount - $sale->credit_remaining;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 font-mono font-medium text-indigo-600">{{ $sale->invoice_number }}</td>
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3.5 text-right font-medium text-slate-700">{{ number_format($sale->total_amount) }} Ks</td>
                        <td class="px-5 py-3.5 text-right text-emerald-600">{{ number_format($paidAmount) }} Ks</td>
                        <td class="px-5 py-3.5 text-right font-bold text-red-600">{{ number_format($sale->credit_remaining) }} Ks</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sale->payment_status === 'paid')
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Paid</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">Partial</span>
                            @else
                                <span class="text-xs font-semibold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sale->credit_remaining > 0)
                            <button onclick="openPaySpecificModal({{ $sale->id }}, '{{ $sale->invoice_number }}', {{ $sale->credit_remaining }})"
                                class="text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-md hover:bg-emerald-700 transition-colors font-medium">
                                Pay
                            </button>
                            @else
                            <span class="text-xs text-slate-400 font-medium">Clear</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Payments History Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mt-6">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
            <h2 class="font-semibold text-slate-700">Payment History</h2>
        </div>

        @php
            $payments = \App\Models\CreditPayment::with('allocations.sale')->where('customer_id', $customer->id)->orderByDesc('created_at')->get();
        @endphp

        @if($payments->isEmpty())
        <div class="text-center py-16 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm">No payment history found</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <th class="text-left px-5 py-3 font-semibold">Date & Time</th>
                        <th class="text-left px-5 py-3 font-semibold">Note</th>
                        <th class="text-left px-5 py-3 font-semibold">Allocated To</th>
                        <th class="text-right px-5 py-3 font-semibold">Payment Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $payment->note ?: '-' }}</td>
                        <td class="px-5 py-3.5 text-slate-600">
                            @if($payment->allocations->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                @foreach($payment->allocations as $alloc)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs px-2 py-0.5 rounded border border-emerald-100">
                                        {{ $alloc->sale->invoice_number }} <span class="opacity-70">({{ number_format($alloc->amount) }} Ks)</span>
                                    </span>
                                @endforeach
                                </div>
                            @else
                                <span class="text-slate-400 italic text-xs">Unallocated (Legacy)</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold text-emerald-600">{{ number_format($payment->amount) }} Ks</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
            <input type="hidden" name="sale_id" id="pay_sale_id" value="">

            <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                <p class="text-xs text-amber-700 font-semibold uppercase tracking-wide mb-1">Customer</p>
                <p class="text-base font-bold text-slate-800">{{ $customer->name }}</p>
                <p class="text-sm text-red-600 font-medium mt-1">Outstanding: <span id="pay_modal_balance">{{ number_format($customer->credit_balance) }}</span> Ks</p>
                <p id="pay_modal_invoice_info" class="text-xs text-indigo-700 font-medium mt-1 hidden"></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Amount (Ks)</label>
                <input type="number" name="amount" id="pay_amount" min="1" max="{{ $customer->credit_balance }}" required
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
function openPaySpecificModal(saleId, invoiceNumber, balance) {
    document.getElementById('pay_sale_id').value = saleId;
    document.getElementById('pay_modal_invoice_info').textContent = 'Paying for Invoice: ' + invoiceNumber;
    document.getElementById('pay_modal_invoice_info').classList.remove('hidden');
    document.getElementById('pay_modal_balance').textContent = balance.toLocaleString();
    document.getElementById('pay_amount').max = balance;
    document.getElementById('pay_amount').value = balance; // Pre-fill with the remaining amount
    document.getElementById('payModal').classList.remove('hidden');
}

function openGeneralPayModal() {
    document.getElementById('pay_sale_id').value = '';
    document.getElementById('pay_modal_invoice_info').classList.add('hidden');
    document.getElementById('pay_modal_balance').textContent = '{{ number_format($customer->credit_balance) }}';
    document.getElementById('pay_amount').max = '{{ $customer->credit_balance }}';
    document.getElementById('pay_amount').value = '';
    document.getElementById('payModal').classList.remove('hidden');
}

document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endsection
