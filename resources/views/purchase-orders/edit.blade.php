@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Purchase Order</h1>
            <p class="text-slate-500 text-sm mt-0.5">{{ $purchaseOrder->order_number }} — only pending orders can be edited</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4 text-sm font-medium">
        {{ session('error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}" id="po-form" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Order Header --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <h2 class="font-semibold text-slate-700 text-lg border-b border-slate-100 pb-3">Order Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Supplier <span class="text-red-500">*</span></label>
                    <select name="supplier_id" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('supplier_id') border-red-400 @enderror">
                        <option value="">Select Supplier...</option>
                        @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Order Date <span class="text-red-500">*</span></label>
                    <input type="date" name="order_date"
                           value="{{ old('order_date', $purchaseOrder->order_date->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('order_date') border-red-400 @enderror">
                    @error('order_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Expected Arrival Date</label>
                    <input type="date" name="expected_date"
                           value="{{ old('expected_date', optional($purchaseOrder->expected_date)->format('Y-m-d')) }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes', $purchaseOrder->notes) }}"
                           class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Optional notes...">
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="font-semibold text-slate-700 text-lg">Order Items</h2>
                <button type="button" onclick="addRow()"
                        class="inline-flex items-center gap-2 text-sm bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold px-4 py-2 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>

            <div id="items-container" class="space-y-3">
                {{-- existing items injected by JS below --}}
            </div>

            {{-- Validation error for items --}}
            @error('items') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

            {{-- Total --}}
            <div class="flex justify-end pt-3 border-t border-slate-100">
                <div class="text-right">
                    <p class="text-sm text-slate-500">Total Order Cost</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5" id="grand-total">0 Ks</p>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-7 py-3 rounded-xl shadow transition-colors">
                Save Changes
            </button>
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-7 py-3 rounded-xl transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>

{{-- Products + existing items JSON for JS --}}
<script>
const PRODUCTS = {!! $productsJson !!};
const EXISTING_ITEMS = {!! $existingItemsJson !!};

let rowCount = 0;

function addRow(existingItem) {
    const idx = rowCount++;
    const productOptions = PRODUCTS.map(p => `<option value="${p.id}">${p.name}</option>`).join('');

    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-3 items-start bg-slate-50 rounded-xl p-4 border border-slate-200';
    row.dataset.idx = idx;
    row.innerHTML = `
        <div class="col-span-12 sm:col-span-4">
            <label class="block text-xs font-medium text-slate-500 mb-1">Product</label>
            <select name="items[${idx}][product_id]" required onchange="updateVariants(this, ${idx})"
                    class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Select product...</option>
                ${productOptions}
            </select>
        </div>
        <div class="col-span-12 sm:col-span-3">
            <label class="block text-xs font-medium text-slate-500 mb-1">Variant</label>
            <select name="items[${idx}][product_variant_id]"
                    class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" id="variant-${idx}">
                <option value="">— select product first —</option>
            </select>
        </div>
        <div class="col-span-6 sm:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Qty Ordered</label>
            <input type="number" name="items[${idx}][quantity_ordered]" min="1" required
                   oninput="recalcTotal()" placeholder="0"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 row-qty">
        </div>
        <div class="col-span-6 sm:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Cost / Unit (Ks)</label>
            <input type="number" name="items[${idx}][cost_price]" min="0" required
                   oninput="recalcTotal()" placeholder="0"
                   class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 row-cost">
        </div>
        <div class="col-span-12 sm:col-span-1 flex sm:items-end sm:justify-center pt-5">
            <button type="button" onclick="removeRow(this)"
                    class="text-red-400 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    `;
    document.getElementById('items-container').appendChild(row);

    // If pre-filling with existing data
    if (existingItem) {
        const productSel = row.querySelector(`select[name="items[${idx}][product_id]"]`);
        productSel.value = existingItem.product_id;
        updateVariants(productSel, idx, existingItem.product_variant_id);

        row.querySelector('.row-qty').value = existingItem.quantity_ordered;
        row.querySelector('.row-cost').value = existingItem.cost_price;
    }

    recalcTotal();
}

function updateVariants(sel, idx, selectedVariantId) {
    const pid = parseInt(sel.value);
    const product = PRODUCTS.find(p => p.id === pid);
    const varSel = document.getElementById(`variant-${idx}`);
    varSel.innerHTML = '<option value="">Select variant...</option>';
    if (product) {
        product.variants.forEach(v => {
            const opt = document.createElement('option');
            opt.value = v.id;
            opt.textContent = `${v.name} (${v.unit_label})`;
            if (selectedVariantId && v.id === selectedVariantId) opt.selected = true;
            varSel.appendChild(opt);
        });
        if (!selectedVariantId && product.variants.length === 1) varSel.value = product.variants[0].id;
    }
}

function removeRow(btn) {
    btn.closest('.item-row').remove();
    recalcTotal();
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty  = parseFloat(row.querySelector('.row-qty')?.value || 0);
        const cost = parseFloat(row.querySelector('.row-cost')?.value || 0);
        total += qty * cost;
    });
    document.getElementById('grand-total').textContent = total.toLocaleString() + ' Ks';
}

// Pre-fill rows from existing order items
EXISTING_ITEMS.forEach(item => addRow(item));

// Ensure at least one row
if (EXISTING_ITEMS.length === 0) addRow();
</script>
@endsection
