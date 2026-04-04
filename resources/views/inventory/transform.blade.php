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

<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800 shadow-sm flex items-start gap-3">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    <div>
        <strong class="font-bold text-amber-900 block mb-1">How it works:</strong>
        Use this to convert stock from one bag size variant to another in the same warehouse (e.g. 12 Pyi bags → 6 Pyi bags). Both source and target variants must have <em>Pyi per Bag</em> configured.
    </div>
</div>

<form action="{{ route('inventory.transform.process') }}" method="POST" id="transformForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Left Column - Source Details --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-red-50 border-b border-red-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-red-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Source Details
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Row 1: Warehouse & Category --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" id="warehouse-select" onchange="checkStock()"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                                <option value="">Select Warehouse...</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Category Filter</label>
                            <select id="category-filter" onchange="filterProducts(this.value)"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Source Product & Variant --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Source Product <span class="text-red-500">*</span></label>
                            <select id="source-product-select" onchange="loadVariants('source', this.value)"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <label class="block text-sm font-semibold text-slate-700">Source Variant <span class="text-red-500">*</span></label>
                                <div id="stock-badge" class="hidden items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all shadow-sm"></div>
                            </div>
                            <select name="original_variant_id" id="source-variant-select" onchange="checkStock()"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                                <option value="">— Select product first —</option>
                            </select>
                            <p id="variant-error" class="hidden mt-2 text-sm text-red-500 font-medium">Source and Target variants must be different.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Target Details --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
                <div class="bg-emerald-50 border-b border-emerald-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-emerald-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        Target Configuration
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Target Product --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Target Product <span class="text-red-500">*</span></label>
                        <select id="target-product-select" onchange="loadVariants('target', this.value)"
                            class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                            <option value="">Select Product...</option>
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
                        <select name="target_variant_id" id="target-variant-select" onchange="checkStock()"
                            class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                            <option value="">— Select product first —</option>
                        </select>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity to Convert <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="quantity" id="quantity-input" min="1" step="1" required oninput="checkStock()"
                                class="w-full pl-5 pr-16 py-4 text-lg font-bold border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 placeholder-slate-400 bg-white" placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-5">
                                <span class="text-slate-400 font-medium" id="unit-label-display">Bags</span>
                            </div>
                        </div>
                        <p id="quantity-error" class="hidden mt-2 text-sm text-red-500 font-medium">Not enough stock available to convert.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <button type="submit" id="submit-btn" class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-amber-500 text-white rounded-xl font-bold text-lg hover:bg-amber-600 focus:ring-4 focus:ring-amber-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-500 shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Confirm Transformation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
const allProducts = {!! $productsJson !!};
let currentAvailableStock = 0;

function filterProducts(categoryId) {
    const prefixes = ['source', 'target'];
    
    prefixes.forEach(prefix => {
        const productSelect = document.getElementById(`${prefix}-product-select`);
        const currentSelectedId = productSelect.value;
        
        productSelect.innerHTML = '<option value="">Select Product...</option>';
        let stillHasCurrentSelection = false;
        
        allProducts.forEach(product => {
            if (!categoryId || product.category_id == categoryId) {
                const opt = document.createElement('option');
                opt.value = product.id;
                opt.textContent = product.name;
                opt.dataset.categoryId = product.category_id;
                
                if (product.id == currentSelectedId) {
                    opt.selected = true;
                    stillHasCurrentSelection = true;
                }
                
                productSelect.appendChild(opt);
            }
        });

        if (currentSelectedId && !stillHasCurrentSelection) {
            productSelect.value = "";
            loadVariants(prefix, "");
        } else if (currentSelectedId) {
            loadVariants(prefix, currentSelectedId, true);
        }
    });
}

function loadVariants(prefix, productId, keepVariant = false) {
    const select = document.getElementById(`${prefix}-variant-select`);
    const currentVariantId = select.value;
    
    select.innerHTML = '<option value="">— select variant —</option>';
    if (prefix === 'source') {
        document.getElementById('unit-label-display').textContent = 'Bags';
    }
    
    if (!productId) {
        checkStock();
        return;
    }
    
    const product = allProducts.find(p => p.id == productId);
    if (!product || !product.variants) return;
    
    let variantFound = false;
    product.variants.forEach(v => {
        const o = document.createElement('option');
        o.value = v.id;
        o.textContent = v.pyi_per_bag ? `${v.name} (${v.pyi_per_bag} Pyi/Bag)` : v.name;
        
        // Store batches to calculate stock
        o.dataset.stockBatches = JSON.stringify(v.stock_batches || []);
        o.dataset.unitLabel = v.unit_label;
        o.dataset.pyiPerBag = v.pyi_per_bag || 1;
        
        if (keepVariant && v.id == currentVariantId) {
            o.selected = true;
            variantFound = true;
            if (prefix === 'source') {
                document.getElementById('unit-label-display').textContent = v.unit_label || 'Bags';
            }
        }
        select.appendChild(o);
    });
    
    if (keepVariant && !variantFound) {
        select.value = "";
    }
    
    checkStock();
}

function checkStock() {
    const sourceSelect = document.getElementById('source-variant-select');
    const targetSelect = document.getElementById('target-variant-select');
    const warehouseSelect = document.getElementById('warehouse-select');
    
    const selectedSourceOpt = sourceSelect.options[sourceSelect.selectedIndex];
    const targetVariantId = targetSelect.value;
    const warehouseId = warehouseSelect.value;
    
    const stockBadge = document.getElementById('stock-badge');
    const quantityInput = document.getElementById('quantity-input');
    const submitBtn = document.getElementById('submit-btn');
    const qtyError = document.getElementById('quantity-error');
    const variantError = document.getElementById('variant-error');
    const unitLabelDisplay = document.getElementById('unit-label-display');
    
    currentAvailableStock = 0;
    qtyError.classList.add('hidden');
    variantError.classList.add('hidden');
    let hasSourceAndWarehouse = false;
    let isValid = true;
    
    // Check variant conflict
    if (selectedSourceOpt && selectedSourceOpt.value && targetVariantId && selectedSourceOpt.value === targetVariantId) {
        variantError.classList.remove('hidden');
        isValid = false;
    }
    
    if (selectedSourceOpt && selectedSourceOpt.value) {
        unitLabelDisplay.textContent = selectedSourceOpt.dataset.unitLabel || 'Bags';
        
        if (warehouseId) {
            hasSourceAndWarehouse = true;
            const stockBatches = JSON.parse(selectedSourceOpt.dataset.stockBatches || '[]');
            
            currentAvailableStock = stockBatches.reduce((total, batch) => {
                if (batch.warehouse_id == warehouseId) {
                    return total + Number(batch.remaining_quantity);
                }
                return total;
            }, 0);
            
            stockBadge.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-800');
            stockBadge.innerHTML = '';
            
            let iconSvg = '';
            if (currentAvailableStock > 0) {
                stockBadge.classList.add('flex', 'bg-green-100', 'text-green-800');
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>`;
            } else {
                stockBadge.classList.add('flex', 'bg-red-100', 'text-red-800');
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>`;
            }
            
            stockBadge.innerHTML = `${iconSvg} ${currentAvailableStock} Available`;
        } else {
            stockBadge.classList.remove('flex');
            stockBadge.classList.add('hidden');
        }
    } else {
        stockBadge.classList.remove('flex');
        stockBadge.classList.add('hidden');
        unitLabelDisplay.textContent = 'Bags';
    }

    const qty = Number(quantityInput.value) || 0;
    
    if (hasSourceAndWarehouse) {
        if (qty > currentAvailableStock && qty > 0) {
            qtyError.classList.remove('hidden');
            isValid = false;
        } else if (qty > 0 && currentAvailableStock === 0) {
            qtyError.classList.remove('hidden');
            qtyError.textContent = "Out of stock in selected warehouse.";
            isValid = false;
        } else {
            qtyError.classList.add('hidden');
            qtyError.textContent = "Not enough stock available to convert.";
        }
    } else if (qty > 0) {
        // Can't have quantity if source/warehouse not selected
        isValid = false;
    }
    
    submitBtn.disabled = !isValid;
}

document.getElementById('transformForm').addEventListener('submit', function(e) {
    e.preventDefault();
    checkStock();
    
    const qty = Number(document.getElementById('quantity-input').value) || 0;
    const warehouseSelect = document.getElementById('warehouse-select');
    const warehouseId = warehouseSelect.value;
    const warehouseName = warehouseSelect.options[warehouseSelect.selectedIndex]?.textContent || '';
    
    const sourceSelect = document.getElementById('source-variant-select');
    const sourceVarId = sourceSelect.value;
    const sourceSelectedOpt = sourceSelect.options[sourceSelect.selectedIndex];
    const sourceVarText = sourceSelectedOpt?.textContent || '';
    const sourcePyi = Number(sourceSelectedOpt?.dataset.pyiPerBag) || 0;
    
    const targetSelect = document.getElementById('target-variant-select');
    const targetVarId = targetSelect.value;
    const targetSelectedOpt = targetSelect.options[targetSelect.selectedIndex];
    const targetVarText = targetSelectedOpt?.textContent || '';
    const targetPyi = Number(targetSelectedOpt?.dataset.pyiPerBag) || 0;
    
    // Final check for invalid state (in case user forced submit button)
    if (!sourceVarId || !targetVarId || !warehouseId || sourceVarId === targetVarId || qty <= 0 || qty > currentAvailableStock) {
        const btn = document.getElementById('submit-btn');
        btn.classList.add('animate-pulse', 'bg-red-600');
        setTimeout(() => {
            btn.classList.remove('animate-pulse', 'bg-red-600');
        }, 500);
        return;
    }

    let outputQtyDisplay = '?';
    if (sourcePyi > 0 && targetPyi > 0) {
        const expectedQty = (sourcePyi / targetPyi) * qty;
        outputQtyDisplay = Number.isInteger(expectedQty) ? expectedQty : expectedQty.toFixed(1);
    }

    const swalHtml = `
        <div class="text-left space-y-3 p-2">
            <div class="bg-amber-50 p-3 rounded-lg border border-amber-100 mb-4">
                <p class="text-amber-800 text-sm font-medium">Please review these transformation details carefully.</p>
            </div>
            
            <div class="flex flex-col gap-1">
                <span class="text-xs font-bold text-slate-500 uppercase">Warehouse</span>
                <span class="text-base font-bold text-slate-800">${warehouseName}</span>
            </div>
            
            <hr class="border-slate-100 my-2">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-slate-500 uppercase">Input Stock</span>
                    <span class="text-base font-bold text-red-600 bg-red-50 p-2 rounded">${qty} x ${sourceVarText}</span>
                </div>
                
                <div class="flex flex-col justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
                
                <div class="flex flex-col gap-1 col-span-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Expected Output Stock</span>
                    <span class="text-base font-bold text-emerald-600 bg-emerald-50 p-2 rounded animate-pulse shadow-sm">${outputQtyDisplay} x ${targetVarText}</span>
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: 'Confirm Transformation',
        html: swalHtml,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b', // amber-500
        cancelButtonColor: '#94a3b8',  // slate-400
        confirmButtonText: 'Yes, Transform It!',
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
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...`;
            
            // Submit form natively
            document.getElementById('transformForm').submit();
        }
    });
});

window.addEventListener('DOMContentLoaded', () => {
    const catFilter = document.getElementById('category-filter');
    if (catFilter.value) {
        filterProducts(catFilter.value);
    }
    checkStock();
});
</script>
@endsection
