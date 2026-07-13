@extends('layouts.app')

@section('content')

@php
    $subtotal   = $sale->items->sum('subtotal');
    $totalDisc  = $sale->items->sum('discount');
    $grandTotal = $sale->total_amount;
@endphp

{{-- Toolbar --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('reports.receipts') }}"
        class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition-colors font-medium">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Receipts
    </a>
    <div class="flex items-center gap-2">
        @if($sale->status !== 'cancelled')
        <button onclick="cancelThisSale({{ $sale->id }}, '{{ $sale->invoice_number }}')"
            class="inline-flex items-center gap-1.5 text-sm bg-white text-rose-600 border border-rose-200 px-4 py-2 rounded-lg hover:bg-rose-50 transition-colors font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancel Sale
        </button>
        @endif
        <button onclick="window.print()"
            class="inline-flex items-center gap-1.5 text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
    </div>
</div>

{{-- Receipt Paper --}}
<div class="max-w-4xl mx-auto" id="receipt-paper">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden relative">

        {{-- Cancelled Watermark --}}
        @if($sale->status === 'cancelled')
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 rotate-[-20deg]">
            <span class="text-[7rem] font-black text-rose-200/70 uppercase tracking-widest leading-none select-none">
                CANCELLED
            </span>
        </div>
        @endif

        {{-- ===== RECEIPT HEADER ===== --}}
        <div class="bg-gradient-to-r from-indigo-700 to-indigo-500 px-8 py-7 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-black tracking-tight">Peace Rice Shop</h1>
                    <p class="text-indigo-200 text-sm mt-0.5">Sales Receipt</p>
                </div>
                <div class="text-right">
                    <p class="text-indigo-200 text-xs uppercase tracking-widest font-semibold mb-1">Invoice No.</p>
                    <p class="text-xl font-mono font-black tracking-wide">{{ $sale->invoice_number }}</p>
                    <p class="text-indigo-200 text-sm mt-1">{{ $sale->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- ===== BILL TO / META ===== --}}
        <div class="px-8 py-5 border-b border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-6 bg-slate-50/60">
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Bill To</p>
                <p class="font-semibold text-slate-800 text-sm">
                    {{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}
                </p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Sale Type</p>
                <p class="font-semibold text-slate-800 text-sm capitalize">{{ $sale->sale_type }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Date Issued</p>
                <p class="font-semibold text-slate-800 text-sm">{{ $sale->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest mb-1">Status</p>
                @if($sale->status === 'cancelled')
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Cancelled
                    </span>
                @elseif($sale->payment_status === 'paid')
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Paid
                    </span>
                @elseif($sale->payment_status === 'partial')
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Partial
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Unpaid
                    </span>
                @endif
            </div>
        </div>

        {{-- ===== ITEMS TABLE ===== --}}
        <div class="px-8 py-6">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-slate-200">
                        <th class="text-left pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest w-8">#</th>
                        <th class="text-left pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Description</th>
                        <th class="text-left pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Warehouse</th>
                        <th class="text-center pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Qty</th>
                        <th class="text-right pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Unit Price</th>
                        <th class="text-right pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Discount</th>
                        <th class="text-right pb-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sale->items as $index => $item)
                    @php
                        $warehouses = $item->batches
                            ->map(fn($b) => optional(optional($b->stockBatch)->warehouse)->name)
                            ->filter()->unique()->values();
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 text-slate-400 text-xs align-top pt-4">{{ $index + 1 }}</td>
                        <td class="py-4 align-top">
                            <p class="font-semibold text-slate-800">{{ $item->variant->product->name ?? 'Unknown Product' }}</p>
                            @if($item->variant && $item->variant->name)
                                <p class="text-xs text-slate-400 mt-0.5">{{ $item->variant->name }}</p>
                            @endif
                        </td>
                        <td class="py-4 align-top">
                            @if($warehouses->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($warehouses as $wh)
                                        <span class="inline-block text-xs bg-violet-50 text-violet-700 border border-violet-100 px-2 py-0.5 rounded-full font-medium whitespace-nowrap">
                                            {{ $wh }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="py-4 text-center align-top">
                            <span class="inline-block bg-slate-100 text-slate-700 font-bold text-xs px-2.5 py-1 rounded-full">
                                {{ $item->quantity }}
                            </span>
                        </td>
                        <td class="py-4 text-right text-slate-600 align-top tabular-nums">
                            {{ number_format($item->unit_price) }} Ks
                        </td>
                        <td class="py-4 text-right align-top tabular-nums">
                            @if($item->discount > 0)
                                <span class="text-rose-500 font-medium">- {{ number_format($item->discount) }} Ks</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="py-4 text-right font-bold text-slate-800 align-top tabular-nums">
                            {{ number_format($item->total_price) }} Ks
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 italic text-sm">No items found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ===== TOTALS + PAYMENT SIDE BY SIDE ===== --}}
        <div class="px-8 pb-8 grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-6">

            {{-- Payment Breakdown (left) --}}
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Payment Breakdown</p>
                <div class="space-y-2.5">
                    @if($sale->payments->isNotEmpty())
                        @foreach($sale->payments as $payment)
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="w-2 h-2 rounded-full {{ $payment->payment_method === 'Credit' ? 'bg-rose-400' : 'bg-emerald-400' }}"></span>
                                {{ $payment->payment_method }}
                            </div>
                            <span class="font-semibold text-slate-700 tabular-nums">{{ number_format($payment->amount) }} Ks</span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-slate-400 italic">No payments recorded.</p>
                    @endif

                    @if($sale->credit_remaining > 0)
                    <div class="flex justify-between items-center text-sm pt-3 mt-1 border-t border-dashed border-slate-200">
                        <div class="flex items-center gap-2 text-rose-500 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Credit Outstanding
                        </div>
                        <span class="font-bold text-rose-500 tabular-nums">{{ number_format($sale->credit_remaining) }} Ks</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Totals (right) --}}
            <div class="md:border-l md:border-slate-100 md:pl-8">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Summary</p>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-medium text-slate-700 tabular-nums">{{ number_format($subtotal) }} Ks</span>
                    </div>
                    @if($totalDisc > 0)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-rose-500">Discount</span>
                        <span class="font-medium text-rose-500 tabular-nums">- {{ number_format($totalDisc) }} Ks</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center pt-3 mt-1 border-t-2 border-slate-800">
                        <span class="font-black text-slate-800 text-base">Grand Total</span>
                        <span class="font-black text-indigo-700 text-xl tabular-nums">{{ number_format($grandTotal) }} Ks</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="bg-slate-50 border-t border-slate-100 px-8 py-5 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                {{ $sale->items->sum('quantity') }} item(s) &bull; Issued {{ $sale->created_at->diffForHumans() }}
            </p>
            <p class="text-xs text-slate-400 italic">Thank you for your business!</p>
        </div>

    </div>{{-- end receipt paper --}}
</div>

{{-- Print Styles --}}
<style>
@media print {
    body * { visibility: hidden; }
    #receipt-paper, #receipt-paper * { visibility: visible; }
    #receipt-paper { position: fixed; top: 0; left: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>

<script>
async function cancelThisSale(saleId, invoiceNumber) {
    const result = await Swal.fire({
        title: 'Cancel Sale?',
        text: `Are you sure you want to cancel ${invoiceNumber}? This will return stock and restore customer credit balances completely.`,
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
                Swal.fire({ icon: 'success', title: 'Cancelled', text: 'Sale has been cancelled successfully.', timer: 2000, showConfirmButton: false })
                    .then(() => { window.location.href = '{{ route("reports.receipts") }}'; });
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
