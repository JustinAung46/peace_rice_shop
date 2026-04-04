@extends('layouts.app')

@section('content')
@include('partials.alerts')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Transfer Stock</h1>
        <p class="text-slate-500 mt-1">Move inventory securely between your warehouses</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold shadow-sm transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Inventory
    </a>
</div>

<form action="{{ route('inventory.transfer.store') }}" method="POST" id="transferForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Left Column - What to Transfer --}}
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                    <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        Product Details
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Row 1: Category & Product --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Product <span class="text-red-500">*</span></label>
                            <select id="product-select" onchange="loadVariants(this.value)"
                                class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                                <option value="">Select Product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-category-id="{{ $product->category_id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Variant --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Variant <span class="text-red-500">*</span></label>
                        <select name="product_variant_id" id="variant-select" onchange="checkStock()"
                            class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                            <option value="">— Select product first —</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column - Where to Transfer --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden relative">
                <div class="bg-indigo-50/50 border-b border-indigo-100 px-6 py-4">
                    <h2 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        Transfer Settings
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    {{-- From Warehouse --}}
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <label class="block text-sm font-semibold text-slate-700">From Warehouse <span class="text-red-500">*</span></label>
                            <div id="stock-badge" class="hidden items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all shadow-sm">
                                {{-- Stock info injected via JS --}}
                            </div>
                        </div>
                        <select name="from_warehouse_id" id="from-warehouse-select" onchange="checkStock()"
                            class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                            <option value="">Select Origin...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- To Warehouse --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">To Warehouse <span class="text-red-500">*</span></label>
                        <select name="to_warehouse_id" id="to-warehouse-select" onchange="validateWarehouses()"
                            class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                            <option value="">Select Destination...</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        <p id="warehouse-error" class="hidden mt-2 text-sm text-red-500 font-medium">Destination must be different from Origin.</p>
                    </div>

                    {{-- Quantity --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="quantity" id="quantity-input" min="1" step="1" required oninput="checkStock()"
                                class="w-full pl-5 pr-16 py-4 text-lg font-bold border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-400 bg-white" placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-5">
                                <span class="text-slate-400 font-medium" id="unit-label-display">Qty</span>
                            </div>
                        </div>
                        <p id="quantity-error" class="hidden mt-2 text-sm text-red-500 font-medium">Not enough stock available.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <button type="submit" id="submit-btn" class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            Confirm Transfer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// We use the full productsJson generated in the controller which includes stock_batches per variant
const allProducts = {!! $productsJson !!};
let currentAvailableStock = 0;

function filterProducts(categoryId) {
    const productSelect = document.getElementById('product-select');
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
        loadVariants("");
    } else if (currentSelectedId) {
        // Reload variants to keep it in sync mentally
        loadVariants(currentSelectedId, true);
    }
}

function loadVariants(productId, keepVariant = false) {
    const variantSelect = document.getElementById('variant-select');
    const currentVariantId = variantSelect.value;
    
    variantSelect.innerHTML = '<option value="">— Select variant —</option>';
    document.getElementById('unit-label-display').textContent = 'Qty';
    
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
        opt.textContent = `${v.name} — ${Number(v.selling_price).toLocaleString()} MMK / ${v.unit_label}`;
        
        // Store variants data as JSON string directly on the option for easy retrieval
        opt.dataset.stockBatches = JSON.stringify(v.stock_batches || []);
        opt.dataset.unitLabel = v.unit_label;
        
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

function validateWarehouses() {
    const fromW = document.getElementById('from-warehouse-select').value;
    const toW = document.getElementById('to-warehouse-select').value;
    const err = document.getElementById('warehouse-error');
    
    if (fromW && toW && fromW === toW) {
        err.classList.remove('hidden');
        checkStock(); // triggers disable logic
        return false;
    } else {
        err.classList.add('hidden');
        checkStock(); // triggers enable logic
        return true;
    }
}

function checkStock() {
    const variantSelect = document.getElementById('variant-select');
    const selectedVariantOpt = variantSelect.options[variantSelect.selectedIndex];
    const fromWarehouseId = document.getElementById('from-warehouse-select').value;
    const toWarehouseId = document.getElementById('to-warehouse-select').value;
    
    const stockBadge = document.getElementById('stock-badge');
    const quantityInput = document.getElementById('quantity-input');
    const submitBtn = document.getElementById('submit-btn');
    const qtyError = document.getElementById('quantity-error');
    const unitLabelDisplay = document.getElementById('unit-label-display');
    
    // Reset state
    currentAvailableStock = 0;
    qtyError.classList.add('hidden');
    let hasVariantAndWarehouse = false;
    
    if (selectedVariantOpt && selectedVariantOpt.value) {
        unitLabelDisplay.textContent = selectedVariantOpt.dataset.unitLabel || 'Qty';
        
        if (fromWarehouseId) {
            hasVariantAndWarehouse = true;
            const stockBatches = JSON.parse(selectedVariantOpt.dataset.stockBatches || '[]');
            
            // Calculate total available stock for this warehouse
            currentAvailableStock = stockBatches.reduce((total, batch) => {
                if (batch.warehouse_id == fromWarehouseId) {
                    return total + Number(batch.remaining_quantity);
                }
                return total;
            }, 0);
            
            // Update badge UI
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
        unitLabelDisplay.textContent = 'Qty';
    }

    // Validation Check
    const qty = Number(quantityInput.value) || 0;
    let isValid = true;
    
    // Check if warehouse selection is valid
    if (fromWarehouseId && toWarehouseId && fromWarehouseId === toWarehouseId) {
        isValid = false;
    }
    
    if (hasVariantAndWarehouse) {
        if (qty > currentAvailableStock && qty > 0) {
            qtyError.classList.remove('hidden');
            isValid = false;
        } else if (qty > 0 && currentAvailableStock === 0) {
            qtyError.classList.remove('hidden');
            qtyError.textContent = "Out of stock in selected warehouse.";
            isValid = false;
        } else {
            qtyError.classList.add('hidden');
            qtyError.textContent = "Not enough stock available.";
        }
    }
    
    submitBtn.disabled = !isValid;
}

// Attach event listener to form to prevent submission if invalid
document.getElementById('transferForm').addEventListener('submit', function(e) {
    e.preventDefault();
    checkStock();
    
    const qty = Number(document.getElementById('quantity-input').value) || 0;
    const fromWarehouseSelect = document.getElementById('from-warehouse-select');
    const fromWarehouseId = fromWarehouseSelect.value;
    const fromWarehouseName = fromWarehouseSelect.options[fromWarehouseSelect.selectedIndex]?.textContent || '';
    
    const toWarehouseSelect = document.getElementById('to-warehouse-select');
    const toWarehouseId = toWarehouseSelect.value;
    const toWarehouseName = toWarehouseSelect.options[toWarehouseSelect.selectedIndex]?.textContent || '';
    
    const variantSelect = document.getElementById('variant-select');
    const variantId = variantSelect.value;
    const variantText = variantSelect.options[variantSelect.selectedIndex]?.textContent || '';
    
    if (!variantId || !fromWarehouseId || !toWarehouseId || qty <= 0 || qty > currentAvailableStock || fromWarehouseId === toWarehouseId) {
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
                <p class="text-indigo-800 text-sm font-medium">Please review these transfer details carefully.</p>
            </div>
            
            <div class="flex flex-col gap-1 col-span-2 mb-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Product Variant</span>
                <span class="text-base font-bold text-slate-800">${qty} x ${variantText}</span>
            </div>
            
            <hr class="border-slate-100 my-2">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-bold text-slate-500 uppercase">From</span>
                    <span class="text-base font-bold text-red-600 bg-red-50 p-2 rounded">${fromWarehouseName}</span>
                </div>
                
                <div class="flex flex-col justify-center items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
                
                <div class="flex flex-col gap-1 col-span-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">To</span>
                    <span class="text-base font-bold text-emerald-600 bg-emerald-50 p-2 rounded">${toWarehouseName}</span>
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: 'Confirm Transfer',
        html: swalHtml,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5', // indigo-600
        cancelButtonColor: '#94a3b8',  // slate-400
        confirmButtonText: 'Yes, Transfer Stock',
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
            document.getElementById('transferForm').submit();
        }
    });
});

window.addEventListener('DOMContentLoaded', () => {
    const catFilter = document.getElementById('category-filter');
    if (catFilter.value) {
        filterProducts(catFilter.value);
    }
    // initialize validation
    checkStock();
});
</script>
@endsection
