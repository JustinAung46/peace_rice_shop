@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Credit Report</h1>
            <p class="text-slate-500 text-sm mt-1">All credit sales and outstanding balances</p>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    {{-- Outstanding Balance Cards --}}
    @if($customersWithBalance->count())
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <h2 class="text-base font-semibold text-slate-700 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Outstanding Balances
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($customersWithBalance as $c)
            <div class="flex items-center justify-between bg-amber-50 border border-amber-100 rounded-lg px-4 py-3">
                <div>
                    <p class="font-semibold text-slate-800">{{ $c->name }}</p>
                    @if($c->phone)<p class="text-xs text-slate-500">{{ $c->phone }}</p>@endif
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-red-600">{{ number_format($c->credit_balance) }} Ks</p>
                    <div class="flex gap-1 mt-1 justify-end">
                        <button onclick="openPayModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ $c->credit_balance }})"
                            class="text-xs bg-emerald-600 text-white px-2 py-1 rounded-md hover:bg-emerald-700 transition-colors">
                            Pay
                        </button>
                        <a href="{{ route('credits.history', $c) }}"
                            class="text-xs bg-slate-700 text-white px-2 py-1 rounded-md hover:bg-slate-600 transition-colors">
                            History
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('credits.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Customer</label>
                <select name="customer_id"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none bg-white">
                    <option value="">All Customers</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('credits.index') }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Credit Sales Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700">Credit Transactions</h2>
            <span class="text-sm text-slate-400">{{ $creditSales->count() }} record(s)</span>
        </div>

        @if($creditSales->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">No credit transactions found</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <th class="text-left px-5 py-3 font-semibold">Date</th>
                        <th class="text-left px-5 py-3 font-semibold">Invoice</th>
                        <th class="text-left px-5 py-3 font-semibold">Customer</th>
                        <th class="text-right px-5 py-3 font-semibold">Sale Total</th>
                        <th class="text-right px-5 py-3 font-semibold">Status</th>
                        <th class="text-right px-5 py-3 font-semibold">Credit Remaining</th>
                        <th class="text-center px-5 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($creditSales as $sale)
                    @php
                        $creditAmount = $sale->payments->where('payment_method', 'Credit')->sum('amount');
                        $cashAmount   = $sale->payments->where('payment_method', '!=', 'Credit')->sum('amount');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3.5 font-mono font-medium text-indigo-600">{{ $sale->invoice_number }}</td>
                        <td class="px-5 py-3.5">
                            @if($sale->customer)
                                <span class="font-medium text-slate-800">{{ $sale->customer->name }}</span>
                                @if($sale->customer->phone)
                                <br><span class="text-xs text-slate-400">{{ $sale->customer->phone }}</span>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Walk-in</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-medium text-slate-700">{{ number_format($sale->total_amount) }} Ks</td>
                        <td class="px-5 py-3.5 text-right">
                            @if($sale->payment_status === 'paid')
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Paid</span>
                            @elseif($sale->payment_status === 'partial')
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">Partial</span>
                            @else
                                <span class="text-xs font-semibold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if($sale->credit_remaining > 0)
                                <span class="font-bold text-red-600">{{ number_format($sale->credit_remaining) }} Ks</span>
                            @else
                                <span class="font-bold text-slate-400">0 Ks</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sale->customer)
                            <a href="{{ route('credits.history', $sale->customer) }}"
                                class="inline-flex items-center gap-1 text-xs bg-slate-100 text-slate-600 px-3 py-1.5 rounded-md hover:bg-slate-200 transition-colors font-medium">
                                History
                            </a>
                            @endif
                        </td>
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
            <button onclick="closePayModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('credits.payment.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="customer_id" id="pay_customer_id">

            <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
                <p class="text-xs text-amber-700 font-semibold uppercase tracking-wide mb-1">Customer</p>
                <p id="pay_customer_name" class="text-base font-bold text-slate-800"></p>
                <p class="text-sm text-red-600 font-medium mt-1">Outstanding: <span id="pay_balance"></span> Ks</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Amount (Ks)</label>
                <input type="number" name="amount" id="pay_amount" min="1" required
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
function openPayModal(customerId, customerName, balance) {
    document.getElementById('pay_customer_id').value = customerId;
    document.getElementById('pay_customer_name').textContent = customerName;
    document.getElementById('pay_balance').textContent = balance.toLocaleString();
    document.getElementById('pay_amount').max = balance;
    document.getElementById('pay_amount').value = '';
    document.getElementById('payModal').classList.remove('hidden');
}
function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
}
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>
@endsection
