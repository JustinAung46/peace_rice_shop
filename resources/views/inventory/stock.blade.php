@extends('layouts.app')

@section('content')
@include('partials.alerts')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Add Stock</h1>
        <p class="text-slate-500 mt-1">Record new inventory into your warehouses</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold shadow-sm transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Inventory
    </a>
</div>

<form action="{{ route('inventory.stock.store') }}" method="POST" id="addStockForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Left Column - Product Selection --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Product Details
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Row 1: Category & Search --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Category Filter</label>
                            <select id="category-filter" class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Quick Search</label>
                            <div class="relative">
                                <input type="text" id="product-search" placeholder="Search product or variant"
                                    class="w-full pl-10 pr-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Product & Variant --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Product <span class="text-red-500">*</span></label>
                            <select name="product_id" id="product-select" onchange="loadVariants(this.value)"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                                <option value="">Select Product...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Variant <span class="text-red-500">*</span></label>
                            <select name="product_variant_id" id="variant-select"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                                <option value="">— Select product first —</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Stock & Storage --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
                <div class="bg-indigo-50/50 border-b border-indigo-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        Stock Information
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Warehouse --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Storage Warehouse <span class="text-red-500">*</span></label>
                        <select name="warehouse_id" id="warehouse-select"
                            class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                            <option value="">Choose a warehouse...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Quantity & Cost --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" id="quantity-input" min="1" step="1" required
                                class="w-full px-4 py-3 text-lg font-bold border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white placeholder-slate-400" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Cost Price <span class="text-xs text-slate-500">(Total MMK)</span> <span class="text-red-500">*</span></label>
                            <input type="number" name="cost_price" id="cost-input" min="0" step="1" required
                                class="w-full px-4 py-3 text-lg font-bold border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white placeholder-slate-400" placeholder="0">
                        </div>
                    </div>

                    {{-- Date & Batch --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Purchase Date <span class="text-red-500">*</span></label>
                            <input type="date" name="purchase_date" id="date-input" required value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Batch Code <span class="text-slate-400 text-xs">(Opt)</span></label>
                            <input type="text" name="batch_code" id="batch-input"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white placeholder-slate-400" placeholder="e.g. BATCH-01">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <button type="submit" id="submit-btn" class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all shadow-md active:bg-indigo-800 disabled:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add to Inventory
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const PRODUCTS = @json($productsJson);
let rawProducts = [];

// Try to parse JSON if it is stringified, else use directly
if (typeof PRODUCTS === 'string') {
    rawProducts = JSON.parse(PRODUCTS);
} else {
    rawProducts = PRODUCTS;
}

function populateProducts(categoryId = '', search = '') {
    const select = document.getElementById('product-select');
    const prevValue = select.value;
    select.innerHTML = '<option value="">Select Product...</option>';
    
    rawProducts.forEach(p => {
        const matchCat = !categoryId || String(p.category_id) === String(categoryId);
        const matchSearch = !search || p.name.toLowerCase().includes(search.toLowerCase());
        
        if (matchCat && matchSearch) {
            const opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.name;
            opt.dataset.variants = JSON.stringify(p.variants || []);
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
    const prefWarehouseId = {!! json_encode($prefWarehouseId ?? null) !!};
    
    if (prefProductId) {
        const selectedProduct = rawProducts.find(p => String(p.id) === String(prefProductId));
        populateProducts(selectedProduct ? selectedProduct.category_id : '', '');
        const productSelect = document.getElementById('product-select');
        productSelect.value = String(prefProductId);
        loadVariants(prefProductId);
        
        if (prefVariantId) {
            document.getElementById('variant-select').value = String(prefVariantId);
        }
    }
    
    if (prefWarehouseId) {
        document.getElementById('warehouse-select').value = String(prefWarehouseId);
    }
});

document.getElementById('addStockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const qty = Number(document.getElementById('quantity-input').value) || 0;
    const cost = Number(document.getElementById('cost-input').value) || 0;
    
    const warehouseSelect = document.getElementById('warehouse-select');
    const warehouseId = warehouseSelect.value;
    const warehouseName = warehouseSelect.options[warehouseSelect.selectedIndex]?.textContent || '';
    
    const variantSelect = document.getElementById('variant-select');
    const variantId = variantSelect.value;
    const variantRawText = variantSelect.options[variantSelect.selectedIndex]?.textContent || '';
    // Strip out the price suffix for a cleaner notification
    const variantText = variantRawText.split(' — ')[0];
    
    const productSelect = document.getElementById('product-select');
    const productId = productSelect.value;
    const productName = productSelect.options[productSelect.selectedIndex]?.textContent || '';

    if (!productId || !variantId || !warehouseId || qty <= 0 || cost < 0) {
        const btn = document.getElementById('submit-btn');
        btn.classList.add('animate-pulse', 'bg-red-600');
        setTimeout(() => {
            btn.classList.remove('animate-pulse', 'bg-red-600');
        }, 500);
        return;
    }

    const swalHtml = `
        <div class="text-left space-y-3 p-2">
            <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 mb-4">
                <p class="text-indigo-800 text-sm font-medium">Please review the stock details before saving.</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1 col-span-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Product Details</span>
                    <span class="text-base font-bold text-slate-800">${productName} - ${variantText}</span>
                </div>
                
                <hr class="border-slate-100 my-1 col-span-2">
                
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-slate-500 uppercase">Quantity</span>
                    <span class="text-base font-bold text-emerald-600 bg-emerald-50 p-2 rounded inline-block w-full">${qty}</span>
                </div>
                
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-slate-500 uppercase">Total Cost</span>
                    <span class="text-base font-bold text-slate-700 bg-slate-50 p-2 rounded inline-block w-full">${cost.toLocaleString()} MMK</span>
                </div>
                
                <div class="flex flex-col gap-1 col-span-2 mt-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Destination Warehouse</span>
                    <span class="text-base font-bold text-slate-800">${warehouseName}</span>
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: 'Confirm Adding Stock',
        html: swalHtml,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5', // indigo-600
        cancelButtonColor: '#94a3b8',  // slate-400
        confirmButtonText: 'Yes, Save to Inventory',
        cancelButtonText: 'Review Details',
        customClass: {
            container: 'font-sans',
            title: 'text-xl font-bold text-slate-800'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state on button
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...`;
            
            // Submit form natively
            document.getElementById('addStockForm').submit();
        }
    });
});
</script>
@endsection
