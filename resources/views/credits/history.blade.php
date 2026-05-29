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
        <div class="flex items-center gap-2">
            @can('admin')
            <a href="{{ route('credits.audit', $customer) }}"
                class="flex items-center gap-2 text-sm bg-slate-700 text-white px-4 py-2 rounded-lg hover:bg-slate-800 transition-colors font-medium">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Audit Log
            </a>
            @endcan
            <button onclick="openGeneralPayModal()"
                class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Payment
            </button>
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
                        <th class="text-left px-5 py-3 font-semibold">Date &amp; Time</th>
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
                        <th class="text-left px-5 py-3 font-semibold">Date &amp; Time</th>
                        <th class="text-left px-5 py-3 font-semibold">Note</th>
                        <th class="text-left px-5 py-3 font-semibold">Allocated To</th>
                        <th class="text-right px-5 py-3 font-semibold">Payment Amount</th>
                        @can('admin')
                        <th class="text-center px-5 py-3 font-semibold">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $payment->note ?: '-' }}
                            @if($payment->original_amount && $payment->original_amount !== $payment->amount)
                                <span class="ml-1 text-xs text-amber-600 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded font-medium">Edited</span>
                            @endif
                        </td>
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
                        @can('admin')
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    onclick="openEditModal({{ $payment->id }}, {{ $payment->amount }}, '{{ addslashes($payment->note ?? '') }}')"
                                    class="inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-1.5 rounded-md hover:bg-indigo-100 transition-colors font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                <button
                                    onclick="openDeleteModal({{ $payment->id }}, {{ $payment->amount }}, '{{ $payment->created_at->format('d M Y') }}')"
                                    class="inline-flex items-center gap-1 text-xs bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1.5 rounded-md hover:bg-rose-100 transition-colors font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- Record Payment Modal --}}
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

{{-- Edit Payment Modal (Admin Only) --}}
@can('admin')
<div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Edit Payment</h3>
                <p class="text-xs text-slate-400 mt-0.5">Balances will be recalculated automatically</p>
            </div>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3 flex items-start gap-3">
                <svg class="w-5 h-5 text-indigo-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-indigo-700">Changing the amount will reverse all current allocations and re-apply them using the new amount. This action is logged in the audit trail.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">New Amount (Ks)</label>
                <input type="number" name="amount" id="edit_amount" min="1" required
                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-lg font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                    placeholder="Enter corrected amount">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Note (optional)</label>
                <input type="text" name="note" id="edit_note"
                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                    placeholder="e.g. Corrected amount, Bank transfer">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold text-base hover:bg-indigo-700 active:scale-95 transition-all">
                ✓ Save Changes
            </button>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal (Admin Only) --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
        <div class="p-6 text-center">
            <div class="w-14 h-14 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Delete Payment?</h3>
            <p class="text-sm text-slate-500 mb-1" id="delete_payment_info"></p>
            <p class="text-xs text-rose-600 font-medium mb-5">This will restore the customer's credit balance and reverse all allocations. This action is logged in the audit trail.</p>

            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-slate-100 text-slate-700 py-2.5 rounded-xl font-semibold hover:bg-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 bg-rose-600 text-white py-2.5 rounded-xl font-bold hover:bg-rose-700 active:scale-95 transition-all">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<script>
// --- Record Payment Modal ---
function openPaySpecificModal(saleId, invoiceNumber, balance) {
    document.getElementById('pay_sale_id').value = saleId;
    document.getElementById('pay_modal_invoice_info').textContent = 'Paying for Invoice: ' + invoiceNumber;
    document.getElementById('pay_modal_invoice_info').classList.remove('hidden');
    document.getElementById('pay_modal_balance').textContent = balance.toLocaleString();
    document.getElementById('pay_amount').max = balance;
    document.getElementById('pay_amount').value = balance;
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

// --- Edit Modal ---
function openEditModal(paymentId, amount, note) {
    document.getElementById('editForm').action = '/credits/payment/' + paymentId;
    document.getElementById('edit_amount').value = amount;
    document.getElementById('edit_note').value = note;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.getElementById('editModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// --- Delete Modal ---
function openDeleteModal(paymentId, amount, date) {
    document.getElementById('deleteForm').action = '/credits/payment/' + paymentId;
    document.getElementById('delete_payment_info').textContent =
        'Payment of ' + amount.toLocaleString() + ' Ks recorded on ' + date + ' will be permanently deleted.';
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endsection
