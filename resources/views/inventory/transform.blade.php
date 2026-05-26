@extends('layouts.app')

@section('content')
@include('partials.alerts')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Transform Bag Sizes</h1>
        <p class="text-slate-500 mt-1">Convert stock from one bag size variant to another</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold shadow-sm transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Inventory
    </a>
</div>

{{-- Global Warehouse Settings --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8 relative">
    <div class="absolute left-0 top-0 bottom-0 w-2 bg-amber-500"></div>
    <div class="p-6 md:p-8">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </div>
            <label class="block text-base font-bold text-slate-800">Warehouse <span class="text-red-500">*</span></label>
        </div>
        <select id="global-warehouse-select" onchange="handleGlobalWarehouseChange()"
            class="w-full px-5 py-4 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer shadow-sm" required>
            <option value="">Select Warehouse...</option>
            @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
        </select>
        <p class="text-sm text-slate-500 mt-2">All transformations will occur within this warehouse.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    {{-- Left Column - Add Transform --}}
    <div class="lg:col-span-5 relative">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add Transform
                </h2>
            </div>
            <div class="p-6 space-y-6">
                {{-- Category Filter --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Category Filter</label>
                    <select id="category-filter" onchange="filterProducts(this.value)"
                        class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Source Product --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Source Product <span class="text-red-500">*</span></label>
                    <select id="source-product-select" onchange="loadSourceVariants(this.value)"
                        class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                        <option value="">Select Source Product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Source Variant --}}
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-sm font-semibold text-slate-700">Source Variant <span class="text-red-500">*</span></label>
                        <div id="source-stock-badge" class="hidden items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all shadow-sm">
                            {{-- Source stock info injected via JS --}}
                        </div>
                    </div>
                    <select id="source-variant-select" onchange="checkStock()"
                        class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                        <option value="">— Select source product first —</option>
                    </select>
                </div>

                {{-- Target Product --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Target Product <span class="text-red-500">*</span></label>
                    <select id="target-product-select" onchange="loadTargetVariants(this.value)"
                        class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                        <option value="">Select Target Product...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Target Variant --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Target Variant <span class="text-red-500">*</span></label>
                    <select id="target-variant-select" onchange="checkStock()"
                        class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                        <option value="">— Select target product first —</option>
                    </select>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" id="quantity-input" min="1" step="1" required oninput="checkStock()"
                            class="w-full pl-5 pr-16 py-4 text-xl font-extrabold border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 placeholder-slate-300 bg-white" placeholder="0">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-5">
                            <span class="text-slate-400 font-medium" id="unit-label-display">Bags</span>
                        </div>
                    </div>
                    <p id="quantity-error" class="hidden mt-2 text-sm text-red-500 font-bold">Not enough stock available.</p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="button" id="add-to-list-btn" onclick="addToTransformList()" disabled class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-slate-800 text-white rounded-xl font-bold text-lg hover:bg-slate-900 focus:ring-4 focus:ring-slate-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add to List
                    </button>
                    <p id="global-missing-warn" class="text-xs text-orange-500 text-center mt-3 font-medium hidden">Please select a warehouse above.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column - Transform Pending List --}}
    <div class="lg:col-span-7 space-y-6">
        <form action="{{ route('inventory.transform.process') }}" method="POST" id="transformForm">
            @csrf
            <input type="hidden" name="warehouse_id" id="form-warehouse-id">
            <input type="hidden" name="transforms" id="transforms-data">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full min-h-[500px]">
                <div class="bg-amber-50/50 border-b border-amber-100 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-amber-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Pending Transforms
                    </h2>
                    <span id="transform-count-badge" class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full">0 items</span>
                </div>

                {{-- Transform List --}}
                <div class="flex-1 p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-semibold text-sm border-b border-slate-200">
                                <th class="py-3 px-6">Source → Target</th>
                                <th class="py-3 px-6 text-center">Qty</th>
                                <th class="py-3 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="transform-list-body" class="divide-y divide-slate-100">
                            {{-- Placeholder for empty state --}}
                            <tr id="empty-state">
                                <td colspan="3" class="py-16 text-center text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    <p class="font-bold text-lg text-slate-500">Cart is Empty</p>
                                    <p class="text-sm mt-1 text-slate-400">Transform items you add will appear here.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-200 mt-auto">
                    <button type="submit" id="submit-transforms-btn" disabled class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-amber-600 text-white rounded-xl font-bold text-xl hover:bg-amber-700 focus:ring-4 focus:ring-amber-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-600 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Confirm Batch Transform
                    </button>
                    <p class="text-center text-xs text-slate-500 mt-3 font-medium flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Double check the source and target variants before confirming.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const allProducts = {!! $productsJson !!};
let transformItems = [];

function filterProducts(categoryId) {
    const sourceProductSelect = document.getElementById('source-product-select');
    const targetProductSelect = document.getElementById('target-product-select');
    const currentSourceSelectedId = sourceProductSelect.value;
    const currentTargetSelectedId = targetProductSelect.value;

    sourceProductSelect.innerHTML = '<option value="">Select Source Product...</option>';
    targetProductSelect.innerHTML = '<option value="">Select Target Product...</option>';
    let stillHasSourceSelection = false;
    let stillHasTargetSelection = false;

    allProducts.forEach(product => {
        if (!categoryId || product.category_id == categoryId) {
            const sourceOpt = document.createElement('option');
            sourceOpt.value = product.id;
            sourceOpt.textContent = product.name;
            sourceOpt.dataset.categoryId = product.category_id;

            const targetOpt = document.createElement('option');
            targetOpt.value = product.id;
            targetOpt.textContent = product.name;
            targetOpt.dataset.categoryId = product.category_id;

            if (product.id == currentSourceSelectedId) {
                sourceOpt.selected = true;
                stillHasSourceSelection = true;
            }

            if (product.id == currentTargetSelectedId) {
                targetOpt.selected = true;
                stillHasTargetSelection = true;
            }

            sourceProductSelect.appendChild(sourceOpt);
            targetProductSelect.appendChild(targetOpt);
        }
    });

    if (currentSourceSelectedId && !stillHasSourceSelection) {
        sourceProductSelect.value = "";
        loadSourceVariants("");
    } else if (currentSourceSelectedId) {
        loadSourceVariants(currentSourceSelectedId, true);
    }

    if (currentTargetSelectedId && !stillHasTargetSelection) {
        targetProductSelect.value = "";
        loadTargetVariants("");
    } else if (currentTargetSelectedId) {
        loadTargetVariants(currentTargetSelectedId, true);
    }
}

function loadSourceVariants(productId, keepVariant = false) {
    const variantSelect = document.getElementById('source-variant-select');
    const currentVariantId = variantSelect.value;

    variantSelect.innerHTML = '<option value="">— Select source product first —</option>';
    document.getElementById('unit-label-display').textContent = 'Bags';

    if (!productId) {
        checkStock();
        return;
    }

    const product = allProducts.find(p => p.id == productId);
    if (!product || !product.variants) return;

    let variantFound = false;
    product.variants.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = v.pyi_per_bag ? `${v.name} (${v.pyi_per_bag} Pyi/Bag)` : v.name;

        opt.dataset.stockBatches = JSON.stringify(v.stock_batches || []);
        opt.dataset.unitLabel = v.unit_label;
        opt.dataset.pyiPerBag = v.pyi_per_bag || 1;
        opt.dataset.productName = product.name;
        opt.dataset.variantName = v.name;

        if (keepVariant && v.id == currentVariantId) {
            opt.selected = true;
            variantFound = true;
            document.getElementById('unit-label-display').textContent = v.unit_label;
        }

        variantSelect.appendChild(opt);
    });

    if (keepVariant && !variantFound) {
        variantSelect.value = "";
    }

    checkStock();
}

function loadTargetVariants(productId, keepVariant = false) {
    const variantSelect = document.getElementById('target-variant-select');
    const currentVariantId = variantSelect.value;

    variantSelect.innerHTML = '<option value="">— Select target product first —</option>';

    if (!productId) {
        checkStock();
        return;
    }

    const product = allProducts.find(p => p.id == productId);
    if (!product || !product.variants) return;

    let variantFound = false;
    product.variants.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = v.pyi_per_bag ? `${v.name} (${v.pyi_per_bag} Pyi/Bag)` : v.name;

        opt.dataset.pyiPerBag = v.pyi_per_bag || 1;
        opt.dataset.productName = product.name;
        opt.dataset.variantName = v.name;

        if (keepVariant && v.id == currentVariantId) {
            opt.selected = true;
            variantFound = true;
        }

        variantSelect.appendChild(opt);
    });

    if (keepVariant && !variantFound) {
        variantSelect.value = "";
    }

    checkStock();
}

function handleGlobalWarehouseChange() {
    const warehouseId = document.getElementById('global-warehouse-select').value;

    if (transformItems.length > 0) {
        transformItems = [];
        updateTransformListUI();

        Swal.fire({
            icon: 'warning',
            title: 'Cart Cleared',
            text: 'Changing the warehouse resets your pending transform list.',
            confirmButtonColor: '#f59e0b',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    }

    checkStock();
}

function getQuantityInList(variantId) {
    let total = 0;
    transformItems.forEach(item => {
        if (item.source_variant_id == variantId) {
            total += parseInt(item.quantity) || 0;
        }
    });
    return total;
}

function checkStock() {
    const warehouseId = document.getElementById('global-warehouse-select').value;
    const sourceVariantSelect = document.getElementById('source-variant-select');
    const targetVariantSelect = document.getElementById('target-variant-select');
    const quantityInput = document.getElementById('quantity-input');
    const addBtn = document.getElementById('add-to-list-btn');
    const quantityError = document.getElementById('quantity-error');
    const sourceStockBadge = document.getElementById('source-stock-badge');
    const globalWarn = document.getElementById('global-missing-warn');

    // Reset states
    quantityError.classList.add('hidden');
    sourceStockBadge.classList.add('hidden');
    addBtn.disabled = true;
    globalWarn.classList.add('hidden');

    if (!warehouseId) {
        globalWarn.classList.remove('hidden');
        return;
    }

    const sourceVariantId = sourceVariantSelect.value;
    const targetVariantId = targetVariantSelect.value;
    const qty = parseInt(quantityInput.value) || 0;

    let canAdd = true;

    // Check if source and target are different
    if (sourceVariantId && targetVariantId && sourceVariantId === targetVariantId) {
        quantityError.textContent = 'Source and target variants must be different.';
        quantityError.classList.remove('hidden');
        canAdd = false;
    }

    // Check stock availability for source variant
    if (sourceVariantId && warehouseId) {
        const selectedOption = sourceVariantSelect.options[sourceVariantSelect.selectedIndex];
        const stockBatches = JSON.parse(selectedOption.dataset.stockBatches || '[]');
        const availableStock = stockBatches.reduce((sum, batch) => {
            if (batch.warehouse_id == warehouseId) {
                return sum + Number(batch.remaining_quantity);
            }
            return sum;
        }, 0);

        const alreadyInList = getQuantityInList(sourceVariantId);
        const remainingStock = availableStock - alreadyInList;

        sourceStockBadge.classList.remove('hidden');
        if (remainingStock <= 0) {
            sourceStockBadge.innerHTML = '<span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg><span class="text-red-700">No stock available</span></span>';
            sourceStockBadge.classList.remove('bg-slate-100', 'text-slate-700');
            sourceStockBadge.classList.add('bg-red-100', 'text-red-800');
            canAdd = false;
        } else {
            sourceStockBadge.innerHTML = `<span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg><span class="text-green-700">${remainingStock} Available</span></span>`;
            sourceStockBadge.classList.remove('bg-red-100', 'text-red-800');
            sourceStockBadge.classList.add('bg-slate-100', 'text-slate-700');

            if (qty > remainingStock) {
                quantityError.textContent = `Only ${remainingStock} available.`;
                quantityError.classList.remove('hidden');
                canAdd = false;
            }
        }
    }

    // Check if all required fields are filled
    if (!sourceVariantId || !targetVariantId || qty <= 0) {
        canAdd = false;
    }

    addBtn.disabled = !canAdd;
}

function addToTransformList() {
    const warehouseId = document.getElementById('global-warehouse-select').value;
    const sourceProductSelect = document.getElementById('source-product-select');
    const sourceVariantSelect = document.getElementById('source-variant-select');
    const targetProductSelect = document.getElementById('target-product-select');
    const targetVariantSelect = document.getElementById('target-variant-select');
    const quantityInput = document.getElementById('quantity-input');

    const sourceProductId = sourceProductSelect.value;
    const sourceVariantId = sourceVariantSelect.value;
    const targetProductId = targetProductSelect.value;
    const targetVariantId = targetVariantSelect.value;
    const quantity = parseInt(quantityInput.value);

    const sourceProduct = allProducts.find(p => p.id == sourceProductId);
    const targetProduct = allProducts.find(p => p.id == targetProductId);
    const sourceVariant = sourceProduct.variants.find(v => v.id == sourceVariantId);
    const targetVariant = targetProduct.variants.find(v => v.id == targetVariantId);

    transformItems.push({
        warehouse_id: warehouseId,
        source_product_id: sourceProductId,
        source_variant_id: sourceVariantId,
        target_product_id: targetProductId,
        target_variant_id: targetVariantId,
        quantity: quantity,
        source_product_name: sourceProduct.name,
        source_variant_name: sourceVariant.name,
        target_product_name: targetProduct.name,
        target_variant_name: targetVariant.name,
        source_pyi_per_bag: sourceVariant.pyi_per_bag,
        target_pyi_per_bag: targetVariant.pyi_per_bag
    });

    updateTransformListUI();

    // Reset form for next item
    sourceProductSelect.value = '';
    sourceVariantSelect.innerHTML = '<option value="">— Select source product first —</option>';
    targetProductSelect.value = '';
    targetVariantSelect.innerHTML = '<option value="">— Select target product first —</option>';
    quantityInput.value = '';
    document.getElementById('source-stock-badge').classList.add('hidden');
    document.getElementById('unit-label-display').textContent = 'Bags';

    checkStock();

    // Success feedback
    Swal.fire({
        icon: 'success',
        title: 'Added to list!',
        text: `${sourceVariant.name} → ${targetVariant.name} (qty: ${quantity})`,
        toast: true,
        position: 'top-end',
        timer: 2500,
        showConfirmButton: false,
        confirmButtonColor: '#f59e0b',
    });
}

function updateTransformListUI() {
    const tbody          = document.getElementById('transform-list-body');
    const countBadge     = document.getElementById('transform-count-badge');
    const emptyState     = document.getElementById('empty-state');
    const formWarehouseId = document.getElementById('form-warehouse-id');
    const transformsData  = document.getElementById('transforms-data');
    const submitBtn       = document.getElementById('submit-transforms-btn');

    countBadge.textContent = `${transformItems.length} item${transformItems.length !== 1 ? 's' : ''}`;

    // Remove only data rows (keep empty-state row in DOM)
    tbody.querySelectorAll('tr.transform-data-row').forEach(r => r.remove());

    if (transformItems.length === 0) {
        emptyState.style.display = '';
        submitBtn.disabled = true;
        formWarehouseId.value = '';
        transformsData.value = '';
        return;
    }

    emptyState.style.display = 'none';
    submitBtn.disabled = false;

    transformItems.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.className = 'transform-data-row border-b border-slate-100 hover:bg-slate-50';
        tr.innerHTML = `
            <td class="py-4 px-6">
                <div class="font-medium text-slate-900">${item.source_product_name} — ${item.source_variant_name}</div>
                <div class="text-sm text-slate-500 flex items-center gap-1 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    ${item.target_product_name} — ${item.target_variant_name}
                </div>
            </td>
            <td class="py-4 px-6 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-semibold">
                    ${item.quantity}
                </span>
            </td>
            <td class="py-4 px-6 text-right">
                <button type="button" onclick="removeTransformItem(${index})" class="text-red-400 hover:text-red-600 p-1 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Update hidden form fields
    formWarehouseId.value = transformItems[0]?.warehouse_id || '';
    transformsData.value  = JSON.stringify(transformItems);
}

function removeTransformItem(index) {
    transformItems.splice(index, 1);
    updateTransformListUI();
    checkStock();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkStock();
});
</script>
@endsection
