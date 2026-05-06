@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="mt-1 text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Receive Goods</h1>
            <p class="text-slate-500 text-sm mt-0.5">
                Order: <span class="font-medium text-slate-700">{{ $purchaseOrder->order_number }}</span>
                &nbsp;·&nbsp; Supplier: <span class="font-medium text-slate-700">{{ $purchaseOrder->supplier->name }}</span>
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('purchase-orders.receive.store', $purchaseOrder) }}" id="receive-form" class="space-y-6">
        @csrf

        {{-- Receipt Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-700 mb-4 border-b border-slate-100 pb-3">Receipt Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Received Date <span class="text-red-500">*</span></label>
                    <input type="date" name="received_date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                    <input type="text" name="notes" placeholder="Optional notes for this receipt..."
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        {{-- Items to Receive --}}
        <div class="space-y-4">
            @foreach($pendingItems as $loopIdx => $item)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="item-card-{{ $item->id }}">
                {{-- Item Header --}}
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50 border-b border-slate-200">
                    <div>
                        <span class="font-semibold text-slate-800">{{ $item->product->name }}</span>
                        @if($item->variant)
                        <span class="ml-2 text-slate-500 text-sm">({{ $item->variant->name }})</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-slate-500">Ordered: <strong>{{ number_format($item->quantity_ordered) }}</strong></span>
                        <span class="text-green-700">Received: <strong>{{ number_format($item->quantity_received) }}</strong></span>
                        <span class="text-amber-700 font-semibold">Remaining: {{ number_format($item->quantity_remaining) }}</span>
                    </div>
                </div>

                <div class="px-5 py-4 space-y-4">
                    <input type="hidden" name="items[{{ $loopIdx }}][order_item_id]" value="{{ $item->id }}">

                    {{-- How many arrived for this item --}}
                    <div class="flex items-end gap-4">
                        <div class="flex-1 max-w-xs">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Total Quantity Arrived <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="arrived-{{ $item->id }}"
                                   min="1" max="{{ $item->quantity_remaining }}"
                                   placeholder="Max: {{ $item->quantity_remaining }}"
                                   oninput="updateArrivedTotal({{ $item->id }}, {{ $item->quantity_remaining }})"
                                   class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg font-semibold">
                            <p class="text-xs text-slate-400 mt-1">Max: {{ number_format($item->quantity_remaining) }} units</p>
                        </div>
                        <div id="arrived-status-{{ $item->id }}" class="text-sm text-slate-400 pb-2"></div>
                    </div>

                    {{-- Warehouse split --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-slate-700">Distribute to Warehouses</label>
                            <button type="button" onclick="addWarehouseRow({{ $item->id }}, {{ $loopIdx }})"
                                    class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                + Add Warehouse
                            </button>
                        </div>

                        <div id="wh-rows-{{ $item->id }}" class="space-y-2">
                            {{-- Initial warehouse row --}}
                            <div class="wh-row flex items-center gap-3" data-item="{{ $item->id }}">
                                <select name="items[{{ $loopIdx }}][warehouses][0][warehouse_id]" required
                                        class="flex-1 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select warehouse...</option>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="items[{{ $loopIdx }}][warehouses][0][quantity]"
                                       min="1" placeholder="Qty"
                                       oninput="checkWarehouseSum({{ $item->id }})"
                                       class="w-28 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 wh-qty-input" required>
                                <span class="w-8"></span>{{-- spacer for delete btn alignment --}}
                            </div>
                        </div>

                        {{-- Validation feedback --}}
                        <div id="wh-feedback-{{ $item->id }}" class="mt-2 text-xs font-medium hidden"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit" id="submit-btn"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-8 py-3 rounded-xl shadow transition-colors">
                Confirm Receipt & Update Stock
            </button>
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-8 py-3 rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
const WAREHOUSES = @json($warehouses->map(fn($w) => ['id' => $w->id, 'name' => $w->name])->values()->toArray());

// Tracks warehouse row counters per item
const whCounters = {};
// Tracks loop index per item id
const itemLoopIdx = @json($pendingItems->values()->mapWithKeys(fn($item, $i) => [$item->id => $i])->toArray());

function addWarehouseRow(itemId, loopIdx) {
    if (!whCounters[itemId]) whCounters[itemId] = 1;
    const idx = whCounters[itemId]++;

    const container = document.getElementById(`wh-rows-${itemId}`);
    const div = document.createElement('div');
    div.className = 'wh-row flex items-center gap-3';
    div.dataset.item = itemId;

    const whOptions = WAREHOUSES.map(w => `<option value="${w.id}">${w.name}</option>`).join('');

    div.innerHTML = `
        <select name="items[${loopIdx}][warehouses][${idx}][warehouse_id]" required
                class="flex-1 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Select warehouse...</option>
            ${whOptions}
        </select>
        <input type="number" name="items[${loopIdx}][warehouses][${idx}][quantity]"
               min="1" placeholder="Qty"
               oninput="checkWarehouseSum(${itemId})"
               class="w-28 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 wh-qty-input" required>
        <button type="button" onclick="removeWhRow(this, ${itemId})"
                class="w-8 text-red-400 hover:text-red-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

function removeWhRow(btn, itemId) {
    btn.closest('.wh-row').remove();
    checkWarehouseSum(itemId);
}

function updateArrivedTotal(itemId, maxQty) {
    checkWarehouseSum(itemId);
}

function checkWarehouseSum(itemId) {
    const arrivedInput = document.getElementById(`arrived-${itemId}`);
    const arrived = parseInt(arrivedInput?.value || 0);

    const rows = document.querySelectorAll(`.wh-row[data-item="${itemId}"] .wh-qty-input`);
    let sum = 0;
    rows.forEach(input => { sum += parseInt(input.value || 0); });

    const feedback = document.getElementById(`wh-feedback-${itemId}`);
    const status = document.getElementById(`arrived-status-${itemId}`);

    if (arrived > 0) {
        if (sum === arrived) {
            feedback.textContent = `✓ Warehouse total matches (${sum} units)`;
            feedback.className = 'mt-2 text-xs font-medium text-green-600';
            feedback.classList.remove('hidden');
            status.textContent = '';
        } else if (sum > arrived) {
            feedback.textContent = `⚠ Warehouse total (${sum}) exceeds arrived (${arrived}) — please fix`;
            feedback.className = 'mt-2 text-xs font-medium text-red-600';
            feedback.classList.remove('hidden');
        } else {
            feedback.textContent = `Warehouse total: ${sum} / ${arrived} units allocated`;
            feedback.className = 'mt-2 text-xs font-medium text-amber-600';
            feedback.classList.remove('hidden');
        }
    } else {
        feedback.classList.add('hidden');
    }
}

// Validate form before submit
document.getElementById('receive-form').addEventListener('submit', function(e) {
    const itemCards = document.querySelectorAll('[id^="item-card-"]');
    let valid = true;

    itemCards.forEach(card => {
        const itemId = card.id.replace('item-card-', '');
        const arrivedInput = document.getElementById(`arrived-${itemId}`);
        const arrived = parseInt(arrivedInput?.value || 0);

        if (!arrived || arrived < 1) {
            valid = false;
            arrivedInput.classList.add('border-red-400');
            return;
        }

        const rows = card.querySelectorAll('.wh-qty-input');
        let sum = 0;
        rows.forEach(input => { sum += parseInt(input.value || 0); });

        if (sum !== arrived) {
            valid = false;
            const fb = document.getElementById(`wh-feedback-${itemId}`);
            if (fb) {
                fb.textContent = `⚠ Warehouse total (${sum}) must equal arrived quantity (${arrived})`;
                fb.className = 'mt-2 text-xs font-medium text-red-600';
                fb.classList.remove('hidden');
            }
        }
    });

    if (!valid) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please make sure all arrived quantities are entered and warehouse totals match.',
            confirmButtonColor: '#4f46e5',
        });
    }
});
</script>
@endsection
