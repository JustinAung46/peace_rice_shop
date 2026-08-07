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

    {{-- Info banner --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl px-5 py-3 text-sm text-indigo-700 flex items-center gap-3">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Check only the items you are receiving now. Unchecked items will be skipped and can be received later.</span>
    </div>

    <form method="POST" action="{{ route('purchase-orders.receive.store', $purchaseOrder) }}" id="receive-form" class="space-y-6">
        @csrf

        {{-- Receipt Info --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-700 mb-4 border-b border-slate-100 pb-3">Receipt Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Received Date <span class="text-red-500">*</span></label>
                    <input type="date" name="received_date" value="{{ now()->format('Y-m-d') }}" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Delivery Rate (per full bag)</label>
                    <div class="relative">
                        <input type="number" name="delivery_rate" min="0" placeholder="0"
                               class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 pr-12">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                            Ks
                        </div>
                    </div>
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

                {{-- Item Header with toggle checkbox --}}
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50 border-b border-slate-200">
                    <div class="flex items-center gap-3">
                        {{-- Toggle checkbox --}}
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   id="include-{{ $item->id }}"
                                   class="sr-only peer item-toggle"
                                   data-item="{{ $item->id }}"
                                   onchange="toggleItem({{ $item->id }})">
                            <div class="w-10 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-400 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                        <div>
                            <span class="font-semibold text-slate-800">{{ $item->product->name }}</span>
                            @if($item->variant)
                            <span class="ml-2 text-slate-500 text-sm">({{ $item->variant->name }})</span>
                            @endif
                            <span class="ml-3 text-xs text-slate-400 font-medium">Toggle to receive this item</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-slate-500">Ordered: <strong>{{ number_format($item->quantity_ordered) }}</strong></span>
                        <span class="text-green-700">Received: <strong>{{ number_format($item->quantity_received) }}</strong></span>
                        <span class="text-amber-700 font-semibold">Remaining: {{ number_format($item->quantity_remaining) }}</span>
                    </div>
                </div>

                {{-- Item Body (collapsed by default) --}}
                <div id="item-body-{{ $item->id }}" class="hidden px-5 py-4 space-y-4">
                    <input type="hidden" name="items[{{ $loopIdx }}][order_item_id]" value="{{ $item->id }}" id="hidden-item-id-{{ $item->id }}" disabled>

                    {{-- Quantity arrived --}}
                    <div class="flex items-end gap-4">
                        <div class="flex-1 max-w-xs">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Total Quantity Arriving <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="arrived-{{ $item->id }}"
                                   name="items[{{ $loopIdx }}][arrived_display]"
                                   min="1" max="{{ $item->quantity_remaining }}"
                                   placeholder="Max: {{ $item->quantity_remaining }}"
                                   oninput="updateArrivedTotal({{ $item->id }}, {{ $item->quantity_remaining }})"
                                   class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg font-semibold"
                                   disabled>
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
                                <select name="items[{{ $loopIdx }}][warehouses][0][warehouse_id]"
                                        class="flex-1 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        disabled>
                                    <option value="">Select warehouse...</option>
                                    @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="items[{{ $loopIdx }}][warehouses][0][quantity]"
                                       min="1" placeholder="Qty"
                                       oninput="checkWarehouseSum({{ $item->id }})"
                                       class="w-28 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 wh-qty-input"
                                       disabled>
                                <span class="w-8"></span>
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
                Confirm Receipt &amp; Update Stock
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
const itemLoopIdx = @json($pendingItems->values()->mapWithKeys(fn($item, $i) => [$item->id => $i])->toArray());

// ---------- Toggle include/exclude an item ----------
function toggleItem(itemId) {
    const checkbox   = document.getElementById(`include-${itemId}`);
    const body       = document.getElementById(`item-body-${itemId}`);
    const hiddenId   = document.getElementById(`hidden-item-id-${itemId}`);
    const arrivedInp = document.getElementById(`arrived-${itemId}`);
    const card       = document.getElementById(`item-card-${itemId}`);

    const enabled = checkbox.checked;

    // Show/hide the body
    body.classList.toggle('hidden', !enabled);
    card.classList.toggle('border-indigo-400', enabled);
    card.classList.toggle('border-slate-200', !enabled);

    // Enable/disable all inputs inside the body so they're submitted only when included
    body.querySelectorAll('input, select').forEach(el => {
        el.disabled = !enabled;
    });
    if (hiddenId) hiddenId.disabled = !enabled;

    // Reset when toggled off
    if (!enabled) {
        arrivedInp.value = '';
        document.getElementById(`wh-feedback-${itemId}`).classList.add('hidden');
        document.getElementById(`arrived-status-${itemId}`).textContent = '';
    }
}

// ---------- Warehouse rows ----------
function addWarehouseRow(itemId, loopIdx) {
    if (!whCounters[itemId]) whCounters[itemId] = 1;
    const idx = whCounters[itemId]++;

    const container = document.getElementById(`wh-rows-${itemId}`);
    const div = document.createElement('div');
    div.className = 'wh-row flex items-center gap-3';
    div.dataset.item = itemId;

    const whOptions = WAREHOUSES.map(w => `<option value="${w.id}">${w.name}</option>`).join('');

    div.innerHTML = `
        <select name="items[${loopIdx}][warehouses][${idx}][warehouse_id]"
                class="flex-1 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Select warehouse...</option>
            ${whOptions}
        </select>
        <input type="number" name="items[${loopIdx}][warehouses][${idx}][quantity]"
               min="1" placeholder="Qty"
               oninput="checkWarehouseSum(${itemId})"
               class="w-28 border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 wh-qty-input">
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
    const status   = document.getElementById(`arrived-status-${itemId}`);

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

// ---------- Form submit validation ----------
document.getElementById('receive-form').addEventListener('submit', function(e) {
    // Collect only checked items
    const checkedToggles = document.querySelectorAll('.item-toggle:checked');

    if (checkedToggles.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'No Items Selected',
            text: 'Please toggle on at least one item to receive.',
            confirmButtonColor: '#4f46e5',
        });
        return;
    }

    let valid = true;

    checkedToggles.forEach(toggle => {
        const itemId = toggle.dataset.item;
        const arrivedInput = document.getElementById(`arrived-${itemId}`);
        const arrived = parseInt(arrivedInput?.value || 0);

        if (!arrived || arrived < 1) {
            valid = false;
            arrivedInput.classList.add('border-red-400');
            return;
        }

        arrivedInput.classList.remove('border-red-400');

        const card = document.getElementById(`item-card-${itemId}`);
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
