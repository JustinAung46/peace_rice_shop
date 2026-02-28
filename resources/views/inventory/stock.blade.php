@extends('layouts.app')

@section('content')
@include('partials.alerts')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Add Stock</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300 font-semibold">Back to Inventory</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('inventory.stock.store') }}" method="POST">
        @csrf
        <div class="space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Category</label>
                    <select id="category-filter" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Quick Search</label>
                    <input type="text" id="product-search" placeholder="Search product or variant"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>
            </div>

            {{-- Product --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Product <span class="text-red-500">*</span></label>
                <select name="product_id" id="product-select" onchange="loadVariants(this.value)"
                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">Select Product</option>
                    
                </select>
            </div>

            {{-- Variant --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Variant <span class="text-red-500">*</span></label>
                <select name="product_variant_id" id="variant-select"
                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                    <option value="">— Select product first —</option>
                </select>
            </div>

            {{-- Warehouse --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                <select name="warehouse_id" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Quantity --}}
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" min="1" step="1" required
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 100">
                </div>

                {{-- Cost Price --}}
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Cost Price (MMK) <span class="text-red-500">*</span></label>
                    <input type="number" name="cost_price" min="0" step="1" required
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 12000">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Purchase Date --}}
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Purchase Date <span class="text-red-500">*</span></label>
                    <input type="date" name="purchase_date" required value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                {{-- Batch Code --}}
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Batch Code <span class="text-slate-400 text-xs">(Optional)</span></label>
                    <input type="text" name="batch_code"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="e.g. BATCH-001">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg shadow-md hover:bg-indigo-700 transition-all">
                    Add Stock
                </button>
            </div>
        </div>
    </form>
</div>

<script>
const PRODUCTS = @json($productsJson);

function populateProducts(categoryId = '', search = '') {
    const select = document.getElementById('product-select');
    const prevValue = select.value;
    select.innerHTML = '<option value="">Select Product</option>';
    PRODUCTS.forEach(p => {
        const matchCat = !categoryId || String(p.category_id) === String(categoryId);
        const matchSearch = !search || p.name.toLowerCase().includes(search.toLowerCase());
        if (matchCat && matchSearch) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name;
            opt.dataset.variants = JSON.stringify(p.variants);
            select.appendChild(opt);
        }
    });
    if ([...select.options].some(o => o.value === prevValue)) {
        select.value = prevValue;
    }
}

function loadVariants(productId) {
    const variantSelect = document.getElementById('variant-select');
    variantSelect.innerHTML = '<option value="">— Select variant —</option>';

    if (!productId) return;

    const option = document.querySelector(`#product-select option[value="${productId}"]`);
    if (!option) return;

    const variants = JSON.parse(option.dataset.variants || '[]');
    variants.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = `${v.name} — ${Number(v.selling_price).toLocaleString()} MMK / ${v.unit_label}`;
        variantSelect.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    populateProducts();
    const category = document.getElementById('category-filter');
    const search = document.getElementById('product-search');
    category.addEventListener('change', function() {
        populateProducts(this.value, search.value);
        document.getElementById('variant-select').innerHTML = '<option value="">— Select product first —</option>';
    });
    search.addEventListener('input', function() {
        populateProducts(category.value, this.value);
        document.getElementById('variant-select').innerHTML = '<option value="">— Select product first —</option>';
    });

    const prefProductId = {!! json_encode($prefProductId ?? null) !!};
    const prefVariantId = {!! json_encode($prefVariantId ?? null) !!};
    if (prefProductId) {
        const selectedProduct = PRODUCTS.find(p => String(p.id) === String(prefProductId));
        populateProducts(selectedProduct ? selectedProduct.category_id : '', '');
        const productSelect = document.getElementById('product-select');
        productSelect.value = String(prefProductId);
        loadVariants(prefProductId);
        if (prefVariantId) {
            const variantSelect = document.getElementById('variant-select');
            variantSelect.value = String(prefVariantId);
        }
    }
});
</script>
@endsection
