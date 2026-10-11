@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('purchase-orders.index') }}"
           class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white
                  text-slate-400 hover:text-slate-700 hover:border-slate-300 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">New Purchase Order</h1>
            <p class="text-slate-400 text-xs mt-0.5">Order products from a supplier</p>
        </div>
    </div>

    <form method="POST" action="{{ route('purchase-orders.store') }}" id="po-form" class="space-y-5">
        @csrf

        {{-- Order Details --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">Order Details</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Supplier --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        Supplier <span class="text-red-400">*</span>
                    </label>
                    <select name="supplier_id" required
                            class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                                   transition @error('supplier_id') border-red-300 @enderror">
                        <option value="">Select supplier…</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <a href="{{ route('suppliers.create') }}"
                       class="text-indigo-500 hover:text-indigo-700 text-xs mt-1 inline-block transition">
                        + Add new supplier
                    </a>
                </div>

                {{-- Order Date --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">
                        Order Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" name="order_date"
                           value="{{ old('order_date', now()->format('Y-m-d')) }}" required
                           class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                </div>

                {{-- Expected Date --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Expected Arrival</label>
                    <input type="date" name="expected_date" value="{{ old('expected_date') }}"
                           class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition">
                </div>

                {{-- Notes --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}"
                           placeholder="Optional notes…"
                           class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                                  transition placeholder-slate-300">
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-700">Order Items</h2>
                <button type="button" onclick="addRow()"
                        class="inline-flex items-center gap-1.5 text-sm font-medium px-3.5 py-1.5
                               bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Item
                </button>
            </div>

            {{-- Column headers (hidden on mobile) --}}
            <div id="col-headers"
                 class="hidden sm:grid grid-cols-12 gap-3 px-5 pt-4 pb-1 text-xs font-medium text-slate-400 uppercase tracking-wider">
                <div class="col-span-2">Category</div>
                <div class="col-span-3">Product</div>
                <div class="col-span-2">Variant</div>
                <div class="col-span-1">Qty</div>
                <div class="col-span-3">Cost / Unit</div>
                <div class="col-span-1"></div>
            </div>

            <div id="items-container" class="divide-y divide-slate-50 px-5 pb-2"></div>

            {{-- Empty placeholder --}}
            <div id="empty-hint" class="py-10 text-center text-slate-300 text-sm">
                No items yet — click <span class="font-medium text-slate-400">Add Item</span> to begin
            </div>

            {{-- Grand Total --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-sm text-slate-400">Total Order Cost</span>
                <span class="text-xl font-bold text-slate-800" id="grand-total">0 Ks</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pb-4">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold
                           px-6 py-2.5 rounded-xl shadow-sm transition">
                Create Purchase Order
            </button>
            <a href="{{ route('purchase-orders.index') }}"
               class="text-sm font-medium text-slate-500 hover:text-slate-700 px-4 py-2.5 rounded-xl
                      hover:bg-slate-100 transition">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
const PRODUCTS   = {!! $productsJson !!};
const CATEGORIES = {!! json_encode($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()) !!};
let rowCount = 0;

function buildProductOptions(categoryId) {
    const list = categoryId ? PRODUCTS.filter(p => p.category_id == categoryId) : PRODUCTS;
    return list.map(p => `<option value="${p.id}">${p.name}</option>`).join('');
}

function filterProducts(catSel, idx) {
    const prodSel  = document.getElementById(`product-${idx}`);
    const prev     = prodSel.value;
    prodSel.innerHTML = '<option value="">Select product…</option>' + buildProductOptions(catSel.value);
    if ([...prodSel.options].some(o => o.value === prev)) prodSel.value = prev;
    else updateVariants(prodSel, idx);
}

function updateVariants(sel, idx) {
    const product = PRODUCTS.find(p => p.id === parseInt(sel.value));
    const varSel  = document.getElementById(`variant-${idx}`);
    varSel.innerHTML = '<option value="">Select variant…</option>';
    if (product) {
        product.variants.forEach(v => {
            varSel.innerHTML += `<option value="${v.id}">${v.name} (${v.unit_label})</option>`;
        });
        if (product.variants.length === 1) varSel.value = product.variants[0].id;
    }
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        total += (parseFloat(row.querySelector('.row-qty')?.value  || 0))
               * (parseFloat(row.querySelector('.row-cost')?.value || 0));
    });
    document.getElementById('grand-total').textContent = total.toLocaleString() + ' Ks';
}

function toggleEmpty() {
    const has = document.querySelectorAll('.item-row').length > 0;
    document.getElementById('empty-hint').classList.toggle('hidden', has);
    document.getElementById('col-headers').classList.toggle('hidden', !has);
}

function addRow() {
    const idx     = rowCount++;
    const catOpts = CATEGORIES.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    const prOpts  = buildProductOptions(null);

    const baseSelect = `border border-slate-200 rounded-xl px-3 py-2 text-sm bg-white
                        focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                        transition w-full`;

    const row = document.createElement('div');
    row.className   = 'item-row grid grid-cols-12 gap-3 py-3 items-center';
    row.dataset.idx = idx;
    row.innerHTML = `
        <div class="col-span-12 sm:col-span-2">
            <label class="block text-xs font-medium text-slate-400 mb-1 sm:hidden">Category</label>
            <select onchange="filterProducts(this,${idx})" class="${baseSelect}">
                <option value="">All</option>
                ${catOpts}
            </select>
        </div>
        <div class="col-span-12 sm:col-span-3">
            <label class="block text-xs font-medium text-slate-400 mb-1 sm:hidden">Product</label>
            <select name="items[${idx}][product_id]" id="product-${idx}" required
                    onchange="updateVariants(this,${idx})" class="${baseSelect}">
                <option value="">Select…</option>
                ${prOpts}
            </select>
        </div>
        <div class="col-span-12 sm:col-span-2">
            <label class="block text-xs font-medium text-slate-400 mb-1 sm:hidden">Variant</label>
            <select name="items[${idx}][product_variant_id]" id="variant-${idx}" class="${baseSelect}">
                <option value="">— pick product —</option>
            </select>
        </div>
        <div class="col-span-6 sm:col-span-1">
            <label class="block text-xs font-medium text-slate-400 mb-1 sm:hidden">Qty</label>
            <input type="number" name="items[${idx}][quantity_ordered]" min="1" required
                   oninput="recalcTotal()" placeholder="0"
                   class="${baseSelect} row-qty">
        </div>
        <div class="col-span-5 sm:col-span-2">
            <label class="block text-xs font-medium text-slate-400 mb-1 sm:hidden">Cost (Ks)</label>
            <input type="number" name="items[${idx}][cost_price]" min="0" required
                   oninput="recalcTotal()" placeholder="0"
                   class="${baseSelect} row-cost">
        </div>
        <div class="col-span-1 flex justify-end sm:justify-center">
            <button type="button" onclick="removeRow(this)"
                    class="text-slate-300 hover:text-red-400 transition p-1 rounded-lg hover:bg-red-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    document.getElementById('items-container').appendChild(row);
    toggleEmpty();
    recalcTotal();
}

function removeRow(btn) {
    btn.closest('.item-row').remove();
    toggleEmpty();
    recalcTotal();
}

addRow();
</script>
@endsection
