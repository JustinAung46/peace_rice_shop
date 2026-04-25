@extends('layouts.app')

@section('content')
@include('partials.alerts')

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Transfer Stock</h1>
        <p class="text-slate-500 mt-1">Move multiple products securely between warehouses</p>
    </div>
    <a href="{{ route('inventory.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 font-semibold shadow-sm transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Back to Inventory
    </a>
</div>

{{-- Global Warehouse Settings --}}
<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8 relative">
    <div class="absolute left-0 top-0 bottom-0 w-2 bg-indigo-500"></div>
    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start relative pb-8">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                </div>
                <label class="block text-base font-bold text-slate-800">Transfer From (Origin) <span class="text-red-500">*</span></label>
            </div>
            <select id="global-from-warehouse" onchange="handleGlobalWarehouseChange()"
                class="w-full px-5 py-4 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer shadow-sm" required>
                <option value="">Select Origin Warehouse...</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                </div>
                <label class="block text-base font-bold text-slate-800">Transfer To (Destination) <span class="text-red-500">*</span></label>
            </div>
            <select id="global-to-warehouse" onchange="handleGlobalWarehouseChange()"
                class="w-full px-5 py-4 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer shadow-sm" required>
                <option value="">Select Destination Warehouse...</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
            <p id="global-warehouse-error" class="hidden mt-2 text-sm text-red-500 font-bold">Destination must be different from Origin.</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    {{-- Left Column - Add to Transfer List --}}
    <div class="lg:col-span-5 relative">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden sticky top-6">
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add Product
                </h2>
            </div>
            <div class="p-6 space-y-6">
                {{-- Category --}}
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

                {{-- Product --}}
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

                {{-- Variant --}}
                <div>
                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-sm font-semibold text-slate-700">Variant <span class="text-red-500">*</span></label>
                        {{-- Stock Badge --}}
                        <div id="stock-badge" class="hidden items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all shadow-sm">
                            {{-- Stock info injected via JS --}}
                        </div>
                    </div>
                    <select id="variant-select" onchange="checkStock()"
                        class="w-full px-4 py-3 text-base border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-slate-50 hover:bg-white transition-colors cursor-pointer" required>
                        <option value="">— Select product first —</option>
                    </select>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="number" id="quantity-input" min="1" step="1" required oninput="checkStock()"
                            class="w-full pl-5 pr-16 py-4 text-xl font-extrabold border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 placeholder-slate-300 bg-white" placeholder="0">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-5">
                            <span class="text-slate-400 font-medium" id="unit-label-display">Qty</span>
                        </div>
                    </div>
                    <p id="quantity-error" class="hidden mt-2 text-sm text-red-500 font-bold">Not enough stock available.</p>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="button" id="add-to-list-btn" onclick="addToTransferList()" disabled class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-slate-800 text-white rounded-xl font-bold text-lg hover:bg-slate-900 focus:ring-4 focus:ring-slate-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add to List
                    </button>
                    <p id="global-missing-warn" class="text-xs text-orange-500 text-center mt-3 font-medium hidden">Please select Origin and Destination warehouses above.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column - Transfer Pending List --}}
    <div class="lg:col-span-7 space-y-6">
        <form action="{{ route('inventory.transfer.store') }}" method="POST" id="transferForm">
            @csrf
            <input type="hidden" name="transfers" id="transfers-data">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col h-full min-h-[500px]">
                <div class="bg-indigo-50/50 border-b border-indigo-100 px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-indigo-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                        Pending Items to Transfer
                    </h2>
                    <span id="transfer-count-badge" class="bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1.5 rounded-full">0 items</span>
                </div>
                
                {{-- Transfer List --}}
                <div class="flex-1 p-0 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 font-semibold text-sm border-b border-slate-200">
                                <th class="py-3 px-6">Product</th>
                                <th class="py-3 px-6 text-center">Qty to Transfer</th>
                                <th class="py-3 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="transfer-list-body" class="divide-y divide-slate-100">
                            {{-- Placeholder for empty state --}}
                            <tr id="empty-state">
                                <td colspan="3" class="py-16 text-center text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto mb-4 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    <p class="font-bold text-lg text-slate-500">Cart is Empty</p>
                                    <p class="text-sm mt-1 text-slate-400">Products you add will appear here.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-200 mt-auto">
                    <button type="submit" id="submit-transfers-btn" disabled class="w-full flex justify-center items-center gap-2 px-6 py-4 bg-indigo-600 text-white rounded-xl font-bold text-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Confirm Batch Transfer
                    </button>
                    <p class="text-center text-xs text-slate-500 mt-3 font-medium flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Double check the origin and destination warehouses before confirming.
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const allProducts = {!! $productsJson !!};
let currentAvailableStock = 0;
let transferItems = []; 

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
        
        opt.dataset.stockBatches = JSON.stringify(v.stock_batches || []);
        opt.dataset.unitLabel = v.unit_label;
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

function handleGlobalWarehouseChange() {
    const fromW = document.getElementById('global-from-warehouse').value;
    const toW = document.getElementById('global-to-warehouse').value;
    const err = document.getElementById('global-warehouse-error');
    
    if (fromW && toW && fromW === toW) {
        err.classList.remove('hidden');
    } else {
        err.classList.add('hidden');
    }
    
    if (transferItems.length > 0) {
        transferItems = [];
        updateTransferListUI();
        
        Swal.fire({
            icon: 'warning',
            title: 'Cart Cleared',
            text: 'Changing the global Origin or Destination warehouse resets your pending transfer list.',
            confirmButtonColor: '#4f46e5',
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
    transferItems.forEach(item => {
        if (item.product_variant_id == variantId) {
            total += parseInt(item.quantity) || 0;
        }
    });
    return total;
}

function checkStock() {
    const variantSelect = document.getElementById('variant-select');
    const selectedVariantOpt = variantSelect.options[variantSelect.selectedIndex];
    const fromWarehouseId = document.getElementById('global-from-warehouse').value;
    const toWarehouseId = document.getElementById('global-to-warehouse').value;
    
    const stockBadge = document.getElementById('stock-badge');
    const quantityInput = document.getElementById('quantity-input');
    const addBtn = document.getElementById('add-to-list-btn');
    const qtyError = document.getElementById('quantity-error');
    const unitLabelDisplay = document.getElementById('unit-label-display');
    const missingWarn = document.getElementById('global-missing-warn');
    
    currentAvailableStock = 0;
    qtyError.classList.add('hidden');
    let hasVariantAndWarehouse = false;
    let qtyAlreadyInList = 0;
    
    // Check if warehouses are set
    if (!fromWarehouseId || !toWarehouseId || fromWarehouseId === toWarehouseId) {
        missingWarn.classList.remove('hidden');
        addBtn.disabled = true;
    } else {
        missingWarn.classList.add('hidden');
    }
    
    if (selectedVariantOpt && selectedVariantOpt.value) {
        unitLabelDisplay.textContent = selectedVariantOpt.dataset.unitLabel || 'Qty';
        
        if (fromWarehouseId) {
            hasVariantAndWarehouse = true;
            const stockBatches = JSON.parse(selectedVariantOpt.dataset.stockBatches || '[]');
            
            let totalPhysicalStock = stockBatches.reduce((total, batch) => {
                if (batch.warehouse_id == fromWarehouseId) {
                    return total + Number(batch.remaining_quantity);
                }
                return total;
            }, 0);
            
            qtyAlreadyInList = getQuantityInList(selectedVariantOpt.value);
            currentAvailableStock = totalPhysicalStock - qtyAlreadyInList;
            
            stockBadge.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-800');
            stockBadge.innerHTML = '';
            
            let iconSvg = '';
            if (currentAvailableStock > 0) {
                stockBadge.classList.add('flex', 'bg-emerald-100', 'text-emerald-800');
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>`;
                stockBadge.innerHTML = `${iconSvg} ${currentAvailableStock} Available`;
                if(qtyAlreadyInList > 0) {
                    stockBadge.innerHTML += ` <span class="font-normal ms-1 opacity-75">(${qtyAlreadyInList} in cart)</span>`;
                }
            } else {
                stockBadge.classList.add('flex', 'bg-red-100', 'text-red-800');
                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>`;
                stockBadge.innerHTML = `${iconSvg} 0 Available`;
                if(qtyAlreadyInList > 0) {
                    stockBadge.innerHTML += ` <span class="font-normal ms-1 opacity-75">(${qtyAlreadyInList} in cart)</span>`;
                }
            }
        } else {
            stockBadge.classList.remove('flex');
            stockBadge.classList.add('hidden');
        }
    } else {
        stockBadge.classList.remove('flex');
        stockBadge.classList.add('hidden');
        unitLabelDisplay.textContent = 'Qty';
    }

    const qty = Number(quantityInput.value) || 0;
    let isValid = true;
    
    if (!fromWarehouseId || !toWarehouseId || fromWarehouseId === toWarehouseId) {
        isValid = false;
    }
    
    if (hasVariantAndWarehouse) {
        if (qty > currentAvailableStock && qty > 0) {
            qtyError.classList.remove('hidden');
            isValid = false;
        } else if (qty > 0 && currentAvailableStock === 0) {
            qtyError.classList.remove('hidden');
            qtyError.textContent = "Out of stock.";
            isValid = false;
        } else {
            qtyError.classList.add('hidden');
            qtyError.textContent = "Not enough stock available.";
        }
    } else {
        isValid = false;
    }
    
    if (qty <= 0) isValid = false;

    addBtn.disabled = !isValid;
}

function addToTransferList() {
    const variantSelect = document.getElementById('variant-select');
    const selectedVariantOpt = variantSelect.options[variantSelect.selectedIndex];
    const fromWarehouseSelect = document.getElementById('global-from-warehouse');
    const toWarehouseSelect = document.getElementById('global-to-warehouse');
    const quantityInput = document.getElementById('quantity-input');
    
    if (document.getElementById('add-to-list-btn').disabled) return;

    // Check if item already exists
    let existingItemIndex = transferItems.findIndex(item => item.product_variant_id === variantSelect.value);

    if (existingItemIndex !== -1) {
        // Update existing quantity
        let currentTotal = parseInt(transferItems[existingItemIndex].quantity);
        transferItems[existingItemIndex].quantity = currentTotal + parseInt(quantityInput.value);
    } else {
        // Add new item
        const item = {
            id: Date.now().toString(),
            product_variant_id: variantSelect.value,
            productName: selectedVariantOpt.dataset.productName,
            variantName: selectedVariantOpt.dataset.variantName,
            unitLabel: selectedVariantOpt.dataset.unitLabel,
            from_warehouse_id: fromWarehouseSelect.value,
            fromWarehouseName: fromWarehouseSelect.options[fromWarehouseSelect.selectedIndex].textContent,
            to_warehouse_id: toWarehouseSelect.value,
            toWarehouseName: toWarehouseSelect.options[toWarehouseSelect.selectedIndex].textContent,
            quantity: quantityInput.value
        };
        transferItems.push(item);
    }
    
    quantityInput.value = '';
    
    updateTransferListUI();
    checkStock(); 
}

function removeFromTransferList(id) {
    transferItems = transferItems.filter(item => item.id !== id);
    updateTransferListUI();
    checkStock();
}

function updateTransferListUI() {
    const listBody = document.getElementById('transfer-list-body');
    const emptyState = document.getElementById('empty-state');
    const submitBtn = document.getElementById('submit-transfers-btn');
    const countBadge = document.getElementById('transfer-count-badge');
    const transfersDataInput = document.getElementById('transfers-data');
    
    Array.from(listBody.children).forEach(child => {
        if (child.id !== 'empty-state') {
            listBody.removeChild(child);
        }
    });

    if (transferItems.length === 0) {
        emptyState.style.display = 'table-row';
        submitBtn.disabled = true;
        countBadge.textContent = '0 items';
        transfersDataInput.value = '';
    } else {
        emptyState.style.display = 'none';
        submitBtn.disabled = false;
        countBadge.textContent = `${transferItems.length} item${transferItems.length > 1 ? 's' : ''}`;
        transfersDataInput.value = JSON.stringify(transferItems);
        
        transferItems.forEach(item => {
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-50 transition-colors group";
            tr.innerHTML = `
                <td class="py-4 px-6 border-t border-slate-100">
                    <div class="font-bold text-slate-800 text-base">${item.productName}</div>
                    <div class="text-sm font-medium text-indigo-600 bg-indigo-50 inline-block px-2 py-0.5 mt-1 rounded">${item.variantName}</div>
                </td>
                <td class="py-4 px-6 text-center border-t border-slate-100">
                    <span class="font-extrabold text-slate-800 text-lg">${item.quantity}</span>
                    <span class="text-xs font-bold uppercase text-slate-400 ml-1 bg-slate-100 px-1.5 py-0.5 rounded">${item.unitLabel}</span>
                </td>
                <td class="py-4 px-6 text-right border-t border-slate-100">
                    <button type="button" onclick="removeFromTransferList('${item.id}')" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-full transition-colors focus:ring-2 focus:ring-red-200 outline-none" title="Remove item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                    </button>
                </td>
            `;
            listBody.appendChild(tr);
        });
    }
}

document.getElementById('transferForm').addEventListener('submit', function(e) {
    if (transferItems.length === 0) {
        e.preventDefault();
        return;
    }
    
    e.preventDefault(); 
    
    const fromWarehouseSelect = document.getElementById('global-from-warehouse');
    const toWarehouseSelect = document.getElementById('global-to-warehouse');
    const globalFrom = fromWarehouseSelect.options[fromWarehouseSelect.selectedIndex]?.textContent;
    const globalTo = toWarehouseSelect.options[toWarehouseSelect.selectedIndex]?.textContent;

    let itemsHtml = ``;
    transferItems.forEach(item => {
        itemsHtml += `
            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-lg border border-slate-100">
                <div class="text-left">
                    <div class="text-base font-bold text-slate-800">${item.productName}</div>
                    <div class="text-xs text-indigo-600 font-medium">${item.variantName}</div>
                </div>
                <div class="font-extrabold text-slate-800 text-base whitespace-nowrap bg-white px-3 py-1 rounded shadow-sm border border-slate-100">${item.quantity} ${item.unitLabel}</div>
            </div>
        `;
    });

    const swalHtml = `
        <div class="text-left space-y-4">
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 mb-2">
                <div class="flex items-center gap-2 mb-2">
                     <span class="text-xs font-bold uppercase text-indigo-400">Batch Route</span>
                </div>
                <div class="flex items-center justify-between text-sm font-bold">
                    <span class="text-red-600 bg-white px-2 py-1 rounded shadow-sm">${globalFrom}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    <span class="text-emerald-600 bg-white px-2 py-1 rounded shadow-sm">${globalTo}</span>
                </div>
            </div>
            <p class="text-slate-500 text-sm font-medium">You are transferring ${transferItems.length} product${transferItems.length > 1 ? 's' : ''}.</p>
            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                ${itemsHtml}
            </div>
        </div>
    `;

    Swal.fire({
        title: 'Confirm Batch Transfer',
        html: swalHtml,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Yes, Confirm Transfer',
        cancelButtonText: 'Review Items',
        customClass: {
            container: 'font-sans',
            title: 'text-2xl font-extrabold text-slate-800'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('submit-transfers-btn');
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...`;
            
            document.getElementById('transferForm').submit();
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
