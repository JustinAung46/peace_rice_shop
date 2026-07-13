@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Receipts</h1>
            <p class="text-slate-500 text-sm mt-1">View all sales receipts, order items, and payment details</p>
        </div>
        <a href="{{ route('pos.index') }}"
   class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 hover:shadow-md">
    <i class="fas fa-cash-register"></i>
    Open POS
</a>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
        <form method="GET" action="{{ route('reports.receipts') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Date From</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Date To</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Invoice #</label>
                <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="INV-..."
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Customer</label>
                <select name="customer_id" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none bg-white">
                    <option value="">All Customers</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-xs font-semibold text-slate-500 mb-1 uppercase tracking-wide">Payment Status</label>
                <select name="payment_status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none bg-white">
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.receipts') }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Receipts Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700">All Receipts</h2>
            <span class="text-sm text-slate-400">{{ $receipts->total() }} record(s)</span>
        </div>

        @if($receipts->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm">No receipts found for the given criteria</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <th class="text-left px-5 py-3 font-semibold">Date & Time</th>
                        <th class="text-left px-5 py-3 font-semibold">Invoice</th>
                        <th class="text-left px-5 py-3 font-semibold">Customer</th>
                        <th class="text-center px-5 py-3 font-semibold">Status</th>
                        <th class="text-right px-5 py-3 font-semibold">Total Amount</th>
                        <th class="text-center px-5 py-3 font-semibold">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($receipts as $receipt)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $receipt->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-5 py-3 font-mono font-medium text-indigo-600">{{ $receipt->invoice_number }}</td>
                        <td class="px-5 py-3">
                            @if($receipt->customer)
                                <span class="font-medium text-slate-800">{{ $receipt->customer->name }}</span>
                            @else
                                <span class="text-slate-400 italic">Walk-in</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($receipt->status === 'cancelled')
                                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Cancelled</span>
                            @elseif($receipt->payment_status === 'paid')
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Paid</span>
                            @elseif($receipt->payment_status === 'partial')
                                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">Partial</span>
                            @else
                                <span class="text-xs font-semibold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Unpaid</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-medium {{ $receipt->status === 'cancelled' ? 'line-through text-slate-400' : 'text-slate-800' }}">{{ number_format($receipt->total_amount) }} Ks</td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('reports.receipts.show', $receipt->id) }}"
                                class="inline-flex items-center gap-1 text-xs bg-indigo-50 text-indigo-600 border border-indigo-100 px-3 py-1.5 rounded-md hover:bg-indigo-100 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </a>
                            @if($receipt->status !== 'cancelled')
                            <button onclick="cancelReportSale({{ $receipt->id }}, '{{ $receipt->invoice_number }}')" class="ml-1 inline-flex items-center gap-1 text-xs bg-white text-rose-600 border border-rose-200 px-3 py-1.5 rounded-md hover:bg-rose-50 transition-colors font-medium">
                                Cancel
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($receipts->hasPages())
        <div class="px-5 py-3 border-t border-slate-100 bg-slate-50">
            {{ $receipts->links() }}
        </div>
        @endif
        @endif
    </div>

</div>

{{-- SweetAlert2 is already bundled in app.js (window.Swal) --}}
<script>
// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id && e.target.id.startsWith('modal-')) {
        e.target.classList.add('hidden');
    }
});

async function cancelReportSale(saleId, invoiceNumber) {
    const result = await Swal.fire({
        title: 'Cancel Sale?',
        text: `Are you sure you want to cancel the sale ${invoiceNumber}? This will return stock and restore customer credit balances completely.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#cbd5e1',
        confirmButtonText: 'Yes, Cancel it!'
    });

    if (result.isConfirmed) {
        Swal.fire({ title: 'Cancelling...', text: 'Please wait', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        try {
            const url = '{{ url("pos/sales") }}/' + saleId + '/cancel';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            if (res.ok && data.success) {
                Swal.fire({ icon: 'success', title: 'Cancelled', text: 'Sale has been cancelled successfully.', timer: 2000, showConfirmButton: false }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'Failed to cancel sale.' });
        }
    }
}
</script>
@endsection
