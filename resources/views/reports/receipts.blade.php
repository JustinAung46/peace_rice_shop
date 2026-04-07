@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Receipts</h1>
            <p class="text-slate-500 text-sm mt-1">View all sales receipts, order items, and payment details</p>
        </div>
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
                            <button onclick="document.getElementById('modal-{{ $receipt->id }}').classList.remove('hidden')"
                                class="inline-flex items-center gap-1 text-xs bg-slate-100 text-slate-600 px-3 py-1.5 rounded-md hover:bg-slate-200 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                View
                            </button>
                            @if($receipt->status !== 'cancelled')
                            <button onclick="cancelReportSale({{ $receipt->id }}, '{{ $receipt->invoice_number }}')" class="ml-1 inline-flex items-center gap-1 text-xs bg-white text-rose-600 border border-rose-200 px-3 py-1.5 rounded-md hover:bg-rose-50 transition-colors font-medium">
                                Cancel
                            </button>
                            @endif

                            {{-- Modal for this receipt --}}
                            <div id="modal-{{ $receipt->id }}" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex flex-col items-center justify-center p-4">
                                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col text-left">
                                    {{-- Modal Header --}}
                                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-bold text-slate-800">Receipt Details</h3>
                                            <span class="font-mono text-sm text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $receipt->invoice_number }}</span>
                                        </div>
                                        <button onclick="document.getElementById('modal-{{ $receipt->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    
                                    {{-- Modal Body --}}
                                    <div class="p-6 overflow-y-auto space-y-6 bg-slate-50/50">
                                        
                                        {{-- Info Row --}}
                                        <div class="flex flex-wrap gap-4 justify-between items-start bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                                            <div>
                                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Customer</p>
                                                <p class="font-semibold text-slate-800">{{ $receipt->customer ? $receipt->customer->name : 'Walk-in' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Date</p>
                                                <p class="text-slate-700 text-sm font-medium">{{ $receipt->created_at->format('d M Y, h:i A') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Assigned Type</p>
                                                <p class="text-slate-700 text-sm font-medium capitalize">{{ $receipt->sale_type }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Status</p>
                                                @if($receipt->status === 'cancelled')
                                                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">Cancelled</span>
                                                @elseif($receipt->payment_status === 'paid')
                                                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">Paid</span>
                                                @elseif($receipt->payment_status === 'partial')
                                                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">Partial</span>
                                                @else
                                                    <span class="text-xs font-semibold text-rose-500 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Unpaid</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Order Items --}}
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Order Items
                                            </h4>
                                            <div class="border border-slate-200 rounded-lg overflow-hidden bg-white">
                                                <table class="w-full text-sm">
                                                    <thead class="bg-slate-50 border-b border-slate-200">
                                                        <tr class="text-xs text-slate-500 uppercase tracking-wide">
                                                            <th class="px-4 py-2 text-left font-semibold">Item</th>
                                                            <th class="px-4 py-2 text-center font-semibold">Qty</th>
                                                            <th class="px-4 py-2 text-right font-semibold">Price</th>
                                                            <th class="px-4 py-2 text-right font-semibold">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100">
                                                        @foreach($receipt->items as $item)
                                                        <tr>
                                                            <td class="px-4 py-2.5">
                                                                <p class="font-medium text-slate-800">{{ $item->variant->product->name ?? 'Unknown' }}</p>
                                                                <p class="text-xs text-slate-500">{{ $item->variant->name ?? '' }}</p>
                                                            </td>
                                                            <td class="px-4 py-2.5 text-center text-slate-600">{{ $item->quantity }}</td>
                                                            <td class="px-4 py-2.5 text-right text-slate-600">{{ number_format($item->unit_price) }} Ks</td>
                                                            <td class="px-4 py-2.5 text-right font-semibold text-slate-800">{{ number_format($item->total_price) }} Ks</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-slate-50 border-t border-slate-200">
                                                        <tr>
                                                            <td colspan="3" class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wide">Grand Total</td>
                                                            <td class="px-4 py-3 text-right font-black text-lg text-indigo-700">{{ number_format($receipt->total_amount) }} Ks</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                        {{-- Payment Details --}}
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                Payment Breakdown
                                            </h4>
                                            
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                @if($receipt->payments->isNotEmpty())
                                                    <div class="space-y-2">
                                                        @foreach($receipt->payments as $payment)
                                                        <div class="flex justify-between items-center text-sm">
                                                            <div class="flex items-center gap-2 text-slate-600 font-medium">
                                                                <span class="w-2 h-2 rounded-full {{ $payment->payment_method === 'Credit' ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                                                {{ $payment->payment_method }}
                                                            </div>
                                                            <span class="font-bold text-slate-800">{{ number_format($payment->amount) }} Ks</span>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-sm text-slate-400 italic">No payments recorded at checkout.</p>
                                                @endif

                                                <hr class="my-3 border-slate-100">

                                                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg border border-slate-200">
                                                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Credit Remaining</span>
                                                    @if($receipt->credit_remaining > 0)
                                                        <span class="font-bold text-rose-600">{{ number_format($receipt->credit_remaining) }} Ks</span>
                                                    @else
                                                        <span class="font-bold text-emerald-600">0 Ks</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    
                                </div>
                            </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
