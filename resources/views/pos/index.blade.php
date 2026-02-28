@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-8rem)] gap-6">
    <!-- Categories Sidebar (Left) -->
    <div class="w-40 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-100">
            <h3 class="font-bold text-slate-700">Categories</h3>
        </div>
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            <button onclick="filterCategory('all')" class="w-full text-left px-4 py-3 rounded-xl text-base font-bold transition-colors bg-indigo-50 text-indigo-700 hover:bg-indigo-100 shadow-sm border border-indigo-100 category-btn" data-id="all">
                All Products
            </button>
            @foreach($categories as $category)
            <button onclick="filterCategory({{ $category->id }})" class="w-full text-left px-4 py-3 rounded-xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent hover:border-slate-100 transition-colors category-btn" data-id="{{ $category->id }}">
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Product Grid (Middle) -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Search & Customer Select -->
        <div class="mb-4 flex flex-col sm:flex-row gap-3 items-stretch">
            <div class="flex-1 relative">
                <input type="text" id="search" placeholder="Search products..." class="w-full h-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <div class="w-full sm:w-1/3">
                <button onclick="openCustomerModal()" id="customer-select-btn" class="w-full h-full px-4 py-2 rounded-xl border border-slate-200 bg-white shadow-sm flex justify-between items-center hover:border-indigo-500 hover:ring-2 hover:ring-indigo-200 transition-all group">
                    <div class="flex flex-col items-start leading-tight">
                        <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Customer</span>
                        <span id="selected-customer-name" class="font-bold text-slate-800 truncate max-w-[120px]">Walk-in Customer</span>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <input type="hidden" id="customer-select" value="">
            </div>
        </div>

        <div class="flex-1 overflow-y-auto pr-2">
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 lg:gap-4" id="product-grid">
                @foreach($products as $product)
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 cursor-pointer hover:border-indigo-500 hover:shadow-md transition-all product-card group transform active:scale-95"
                     data-category="{{ $product->category_id ?? 'uncategorized' }}"
                     data-name="{{ strtolower($product->name) }}"
                     onclick="openVariantPicker({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->variants->toJson() }}, '{{ $product->image_path ? asset('storage/' . $product->image_path) : '' }}')">

                    <div class="h-28 bg-slate-50 rounded-xl mb-3 flex items-center justify-center text-slate-300 group-hover:bg-slate-100 transition-colors overflow-hidden">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" class="h-full w-full object-cover">
                        @else
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        @endif
                    </div>

                    <h3 class="font-bold text-slate-800 text-base mb-2">{{ $product->name }}</h3>
                    @if($product->category)
                    <p class="text-xs text-indigo-600 font-semibold mb-2">{{ $product->category->name }}</p>
                    @endif

                    <div class="space-y-1">
                        @foreach($product->variants->take(3) as $variant)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-600 truncate">{{ $variant->name }}</span>
                            <span class="{{ $variant->stock_count > 0 ? 'text-emerald-600' : 'text-red-500' }} font-bold ml-2 shrink-0">
                                {{ $variant->stock_count }} {{ $variant->unit_label }}
                            </span>
                        </div>
                        @endforeach
                        @if($product->variants->count() > 3)
                        <p class="text-xs text-slate-400">+{{ $product->variants->count() - 3 }} more variants...</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div id="no-products" class="hidden text-center py-10 text-slate-500">No products found</div>
        </div>
    </div>

    <!-- Cart (Right) -->
    <div class="w-80 bg-white rounded-xl shadow-lg border border-slate-200 flex flex-col overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Current Order
            </h2>
            <button onclick="clearCart()" class="text-xs text-red-500 hover:text-red-700 font-medium">Clear</button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="cart-items">
            <div id="empty-cart-msg" class="text-center text-slate-400 py-10 flex flex-col items-center">
                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span>Cart is empty</span>
            </div>
        </div>

        <div class="p-3 bg-slate-50 border-t border-slate-200">
            <div class="space-y-1 mb-3 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span id="cart-subtotal" class="font-medium">0 MMK</span>
                </div>
                <div class="flex justify-between text-teal-600">
                    <span>Discount</span>
                    <span id="cart-discount" class="font-medium">-0 MMK</span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-800 pt-1.5 border-t border-slate-200">
                    <span>Total</span>
                    <span id="cart-total">0 MMK</span>
                </div>
            </div>

            <!-- Payment Methods Selection -->
            <div class="grid grid-cols-3 gap-2 mb-3">
                <button onclick="addPayment('Cash')" id="btn-cash" class="py-2.5 px-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 active:scale-95 transition-all shadow-sm flex flex-col items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Cash
                </button>
                <button onclick="addPayment('Kpay')" id="btn-kpay" class="py-2.5 px-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 active:scale-95 transition-all shadow-sm flex flex-col items-center gap-1">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    KPay
                </button>
                <button onclick="addPayment('Credit')" id="btn-credit" class="py-2.5 px-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-400 bg-slate-50 cursor-not-allowed flex flex-col items-center gap-1 opacity-60" disabled title="Select a customer first">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Credit
                </button>
            </div>

            <!-- Active Payments List -->
            <div id="active-payments" class="space-y-1.5 mb-3 max-h-24 overflow-y-auto">
                <!-- Payment rows will be added here -->
            </div>

            <div class="flex justify-between text-[11px] mb-3 font-medium" id="payment-status-row">
                <span class="text-slate-500">Paid: <span id="paid-total" class="text-slate-800">0</span></span>
                <span id="remaining-label" class="text-slate-500">Left: <span id="remaining-amount" class="text-red-500">0</span></span>
            </div>

            <button onclick="processCheckout()" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" id="checkout-btn" disabled>
                Checkout
            </button>
        </div>
    </div>
</div>

<!-- ═══ Variant Picker Modal ═══════════════════════════════════════════════ -->
<div id="variant-picker-modal" class="fixed inset-0 bg-slate-900/60 z-[80] hidden items-center justify-center p-0 md:p-6 transition-all duration-300 backdrop-blur-sm">
    <div class="bg-white rounded-t-[2.5rem] md:rounded-3xl shadow-2xl w-full max-w-3xl lg:max-w-4xl flex flex-col overflow-hidden animate-in slide-in-from-bottom-10 md:zoom-in duration-300 max-h-[85vh] md:max-h-[85vh] mt-auto md:mt-0">

        <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto my-3 md:hidden"></div>

        <div class="flex items-center justify-between px-6 py-4 md:px-7 md:py-5 border-b border-slate-200 bg-slate-50/50 flex-shrink-0">
            <div>
                <h3 id="variant-picker-title" class="text-xl md:text-2xl font-black text-slate-900 leading-tight">Product Name</h3>
            </div>
            <button onclick="closeVariantPicker()" class="group w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 transition-all shadow-sm">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                <div class="lg:w-72 flex-shrink-0">
                    <div class="aspect-square w-50 m-auto lg:w-full rounded-3xl overflow-hidden bg-slate-50 border border-slate-100 flex items-center justify-center group relative">
                        <img id="picker-img" src="" alt="" class="w-full h-full object-cover hidden">
                        <div id="picker-img-placeholder" class="text-slate-200">
                             <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="flex-1 space-y-8">
                    <section>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Price Mode</h4>
                        <div class="grid grid-cols-2 gap-3 p-1 bg-slate-100 rounded-2xl">
                            <button id="price-mode-wholesale" onclick="setPriceMode('wholesale')"
                                class="py-3 rounded-xl text-sm font-bold transition-all bg-white text-indigo-600 shadow-sm border border-indigo-100">
                                Wholesale
                            </button>
                            <button id="price-mode-retail" onclick="setPriceMode('retail')"
                                class="py-3 rounded-xl text-sm font-bold transition-all text-slate-500 hover:bg-white/50">
                                Retail
                            </button>
                        </div>
                    </section>

                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Variants</h4>
                            <span id="picker-stock-label" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100 hidden">IN STOCK</span>
                        </div>
                        <div id="variant-picker-pills" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 md:px-7 md:py-5 border-t border-slate-200 bg-slate-50/80 backdrop-blur-md">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-6">
                <div class="flex items-center justify-between sm:justify-start gap-4 bg-white border border-slate-200 p-1.5 rounded-2xl">
                    <button onclick="pickerChangeQty(-1)" class="w-10 h-10 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-slate-50 transition-all flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M20 12H4"/></svg>
                    </button>
                    <span id="picker-qty-display" class="w-10 text-center text-lg font-black text-slate-800">1</span>
                    <button onclick="pickerChangeQty(1)" class="w-10 h-10 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                    </button>
                </div>

                <div class="flex-1 flex items-center justify-between sm:justify-end gap-8">
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Total</p>
                        <p id="picker-total-display" class="text-2xl font-black text-slate-900 leading-none">0 <span class="text-sm font-bold text-slate-400">MMK</span></p>
                    </div>
                    <button id="picker-add-btn" onclick="pickerAddToCart()" disabled
                        class="flex-1 sm:flex-none px-10 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg flex items-center justify-center gap-3 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-30">
                        Add to Cart
                    </button>
                </div>
            </div>

            <p id="picker-validation-msg" class="text-center text-rose-500 text-[10px] font-bold mt-4 hidden opacity-0 transition-opacity uppercase tracking-widest">
                Please select a variant
            </p>
        </div>
    </div>
</div>

<!-- Template for variant pill (to be used by JavaScript) -->
<template id="variant-pill-template">
    <button class="variant-pill relative px-3 py-2.5 text-xs font-medium rounded-xl border transition-all duration-200 text-left">
        <span class="variant-name block truncate"></span>
        <span class="variant-price text-[10px] opacity-75"></span>
    </button>
</template>

<!-- Edit Item Modal with Numpad -->
<div id="edit-modal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh] overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800" id="modal-item-name">Edit Item</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <input type="hidden" id="modal-item-id">

        <div class="flex-1 flex flex-col md:flex-row bg-slate-50 relative">
            <!-- Left side: Inputs -->
            <div class="p-5 space-y-4 flex-1 border-b md:border-b-0 md:border-r border-slate-200 bg-white">
                <!-- Unit Price -->
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Unit Price (MMK)</label>
                    <div id="input-btn-price" onclick="setActiveInput('price')" class="w-full px-4 py-4 border-2 border-indigo-500 bg-indigo-50 rounded-xl text-right font-black text-2xl text-indigo-900 cursor-pointer transition-colors shadow-inner">0</div>
                    <input type="hidden" id="modal-price">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Quantity</label>
                        <div id="input-btn-qty" onclick="setActiveInput('qty')" class="w-full px-4 py-4 border-2 border-slate-200 bg-white rounded-xl text-center font-bold text-xl text-slate-800 cursor-pointer transition-colors hover:border-indigo-300">0</div>
                        <input type="hidden" id="modal-qty">
                    </div>
                    <!-- Discount -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Discount</label>
                        <div id="input-btn-discount" onclick="setActiveInput('discount')" class="w-full px-4 py-4 border-2 border-slate-200 bg-white rounded-xl text-right font-bold text-xl text-slate-800 cursor-pointer transition-colors hover:border-indigo-300">0</div>
                        <input type="hidden" id="modal-discount">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-sm font-semibold text-slate-600 mb-2 uppercase tracking-wide">Warehouse</label>
                    <select id="modal-warehouse" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-slate-50 font-bold text-slate-700 shadow-sm cursor-pointer border-r-8 border-transparent">
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Right side: Numpad -->
            <div class="p-5 flex-[0.8] flex flex-col justify-center">
                <div class="grid grid-cols-3 gap-3">
                    @foreach([1,2,3,4,5,6,7,8,9] as $num)
                        <button onclick="appendEditNumpad('{{ $num }}')" class="py-5 bg-white border border-slate-200 rounded-xl text-2xl font-black text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 active:bg-indigo-100 shadow-sm transition-all">{{ $num }}</button>
                    @endforeach
                    <button onclick="appendEditNumpad('00')" class="py-5 bg-white border border-slate-200 rounded-xl text-2xl font-black text-slate-700 hover:bg-slate-100 active:bg-slate-200 shadow-sm transition-all leading-none pt-2">00</button>
                    <button onclick="appendEditNumpad('0')" class="py-5 bg-white border border-slate-200 rounded-xl text-2xl font-black text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 active:bg-indigo-100 shadow-sm transition-all">0</button>
                    <button onclick="popEditNumpad()" class="py-5 bg-red-50 border border-red-100 rounded-xl text-lg font-bold text-red-600 hover:bg-red-100 active:bg-red-200 shadow-sm transition-all flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" /></svg>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <button onclick="clearEditNumpad()" class="py-4 bg-slate-200 border border-slate-300 rounded-xl text-base font-bold text-slate-700 hover:bg-slate-300 active:bg-slate-400 transition-all shadow-sm">Clear</button>
                    <button onclick="appendEditNumpad('000')" class="py-4 bg-white border border-slate-200 rounded-xl text-lg font-black text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 active:bg-indigo-100 shadow-sm transition-all">000</button>
                </div>
            </div>
        </div>

        <div class="p-4 bg-white border-t border-slate-100 flex gap-3">
            <button onclick="closeModal()" class="flex-1 py-4 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 active:scale-95 transition-all shadow-sm text-lg">Cancel</button>
            <button onclick="saveModal()" class="flex-[2] py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 active:scale-95 transition-all text-lg">Save Changes</button>
        </div>
    </div>
</div>

<!-- Touch-Friendly Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black/60 z-[60] hidden flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800" id="payment-modal-title">Payment Amount</h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="p-4 flex-1">
            <!-- Amount Display -->
            <div class="mb-4">
                <div class="bg-slate-100 p-4 rounded-xl text-center flex items-baseline justify-center gap-2">
                    <span id="payment-numpad-display" class="text-3xl font-black text-indigo-700">0</span>
                    <span class="text-sm font-bold text-slate-400">MMK</span>
                </div>
            </div>

            <!-- Quick Amounts -->
            <div class="grid grid-cols-4 gap-2 mb-4">
                <button onclick="setNumpadValue('full_remain')" id="btn-quick-full" class="col-span-2 py-3 bg-indigo-50 text-indigo-700 rounded-lg font-bold hover:bg-indigo-100 text-xs border border-indigo-100 transition-colors">Full Amount</button>
                <button onclick="appendNumpadQuick(1000)" class="py-3 bg-slate-50 text-slate-700 rounded-lg font-bold hover:bg-slate-100 text-xs border border-slate-200 transition-colors">1k</button>
                <button onclick="appendNumpadQuick(5000)" class="py-3 bg-slate-50 text-slate-700 rounded-lg font-bold hover:bg-slate-100 text-xs border border-slate-200 transition-colors">5k</button>
                <button onclick="appendNumpadQuick(10000)" class="py-3 bg-slate-50 text-slate-700 rounded-lg font-bold hover:bg-slate-100 text-xs border border-slate-200 transition-colors">10k</button>
                <button onclick="appendNumpadQuick(20000)" class="py-3 bg-slate-50 text-slate-700 rounded-lg font-bold hover:bg-slate-100 text-xs border border-slate-200 transition-colors">20k</button>
                <button onclick="appendNumpadQuick(50000)" class="py-3 bg-slate-50 text-slate-700 rounded-lg font-bold hover:bg-slate-100 text-xs border border-slate-200 transition-colors">50k</button>
                <button onclick="appendNumpadQuick(100000)" class="py-3 bg-slate-50 text-slate-700 rounded-lg font-bold hover:bg-slate-100 text-xs border border-slate-200 transition-colors">100k</button>
            </div>

            <!-- Number Pad -->
            <div class="grid grid-cols-3 gap-2">
                @foreach([1,2,3,4,5,6,7,8,9] as $num)
                    <button onclick="appendNumpad({{ $num }})" class="py-3 bg-white border border-slate-200 rounded-lg text-xl font-bold text-slate-700 hover:bg-slate-50 active:bg-slate-100 shadow-sm transition-colors">{{ $num }}</button>
                @endforeach
                <button onclick="clearNumpad()" class="py-3 bg-red-50 border border-red-100 rounded-lg text-lg font-bold text-red-600 hover:bg-red-100 active:bg-red-200 shadow-sm transition-colors">C</button>
                <button onclick="appendNumpad(0)" class="py-3 bg-white border border-slate-200 rounded-lg text-xl font-bold text-slate-700 hover:bg-slate-50 active:bg-slate-100 shadow-sm transition-colors">0</button>
                <button onclick="popNumpad()" class="py-3 bg-slate-50 border border-slate-200 rounded-lg text-lg font-bold text-slate-700 hover:bg-slate-100 active:bg-slate-200 shadow-sm transition-colors">
                    <svg class="w-6 h-6 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" /></svg>
                </button>
            </div>
        </div>

        <div class="p-4 bg-slate-50 border-t border-slate-100 flex gap-3">
            <button onclick="closePaymentModal()" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 active:scale-95 transition-all shadow-sm">Cancel</button>
            <button onclick="confirmPaymentModal()" class="flex-[2] py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-100 active:scale-95 transition-all">Confirm</button>
        </div>
    </div>
    </div>
</div>

<!-- Customer Selection Modal -->
<div id="customer-modal" class="fixed inset-0 bg-black/60 z-[70] hidden flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[80vh]">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Select Customer</h3>
            <button onclick="closeCustomerModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="p-4 border-b border-slate-100 bg-white sticky top-0 z-10">
            <div class="relative">
                <input type="text" id="customer-search" placeholder="Search customers..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm" oninput="filterCustomers()">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-2 space-y-1" id="customer-list">
            <button onclick="selectCustomer('', 'Walk-in Customer', 0)" class="w-full text-left px-4 py-3 rounded-xl hover:bg-slate-50 flex items-center justify-between group transition-colors customer-item">
                <span class="font-bold text-slate-800">Walk-in Customer</span>
                <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-lg group-hover:bg-white">Default</span>
            </button>
            @foreach($customers as $customer)
            <button onclick="selectCustomer('{{ $customer->id }}', '{{ addslashes($customer->name) }}', {{ $customer->credit_balance }})" class="w-full text-left px-4 py-3 rounded-xl hover:bg-slate-50 flex items-center justify-between group transition-colors customer-item" data-name="{{ strtolower($customer->name) }}">
                <div class="flex flex-col">
                    <span class="font-bold text-slate-800 group-hover:text-indigo-700 transition-colors">{{ $customer->name }}</span>
                    <span class="text-xs text-slate-500">Credit Balance: {{ number_format($customer->credit_balance) }} MMK</span>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </button>
            @endforeach
            <div id="no-customers" class="hidden text-center py-8 text-slate-400">No customers found</div>
        </div>
    </div>
</div>

<script>
    let cart = {};
    let payments = []; // Array of {method, amount}
    let currentEditingPayment = null; // {index, method} or null
    let numpadValue = '0';
    const emptyMsgTemplate = document.getElementById('empty-cart-msg').cloneNode(true);
    const warehouses = {
        @foreach($warehouses as $w)
            {{ $w->id }}: '{{ addslashes($w->name) }}',
        @endforeach
    };

    // --- Product Filtering ---
    const searchInput = document.getElementById('search');
    const productGrid = document.getElementById('product-grid');
    const productCards = document.querySelectorAll('.product-card');
    const noProductsMsg = document.getElementById('no-products');
    const categoryBtns = document.querySelectorAll('.category-btn');
    let activeCategory = 'all';

    function filterProducts() {
        const term = searchInput.value.toLowerCase();
        let visibleCount = 0;

        productCards.forEach(card => {
            const name = card.dataset.name;
            const category = card.dataset.category;
            const matchesSearch = name.includes(term);
            const matchesCategory = activeCategory === 'all' || category == activeCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noProductsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        productGrid.style.display = visibleCount === 0 ? 'none' : 'grid';
    }

    searchInput.addEventListener('input', filterProducts);

    function filterCategory(id) {
        activeCategory = id;

        // Update styling
        categoryBtns.forEach(btn => {
            if(btn.dataset.id == id) {
                btn.className = 'w-full text-left px-4 py-3 rounded-xl text-base font-bold transition-colors bg-indigo-50 text-indigo-700 hover:bg-indigo-100 shadow-sm border border-indigo-100 category-btn';
            } else {
                btn.className = 'w-full text-left px-4 py-3 rounded-xl text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent hover:border-slate-100 transition-colors category-btn';
            }
        });

        filterProducts();
    }

    // ─── Variant Picker State ─────────────────────────────────────────────────
    let pickerProductId   = null;
    let pickerProductName = null;
    let pickerVariants    = [];
    let pickerSelectedId  = null;
    let pickerQty         = 1;
    let pickerPriceMode   = 'wholesale'; // 'wholesale' or 'retail'

    function setPriceMode(mode) {
        pickerPriceMode = mode;

        // Reset selection when switching modes
        pickerSelectedId = null;
        pickerQty = 1;

        // Update UI
        const wholesaleBtn = document.getElementById('price-mode-wholesale');
        const retailBtn = document.getElementById('price-mode-retail');

        if (mode === 'wholesale') {
            wholesaleBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all bg-white text-indigo-700 shadow-sm border border-indigo-100';
            retailBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-slate-600 hover:bg-slate-50';
        } else {
            retailBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all bg-white text-indigo-700 shadow-sm border border-indigo-100';
            wholesaleBtn.className = 'flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-slate-600 hover:bg-slate-50';
        }

        // Reset Picker UI for cleared selection
        document.getElementById('picker-qty-display').innerText = '1';
        document.getElementById('picker-stock-label').classList.add('hidden');
        document.getElementById('picker-add-btn').disabled = true;
        document.getElementById('picker-add-btn').innerHTML = `
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add to Cart
        `;
        document.getElementById('picker-validation-msg').style.opacity = '0';
        pickerUpdateTotal();

        // Re-render variants to update list and prices
        renderPickerVariants();
    }

    function renderPickerVariants() {
        const pillsContainer = document.getElementById('variant-picker-pills');
        pillsContainer.innerHTML = '';

        pickerVariants.slice(0, 10).forEach(v => {
            // Calculate price based on mode
            let displayPrice = 0;
            let isRetailPossible = false;

            if (pickerPriceMode === 'wholesale') {
                displayPrice = v.selling_price;
            } else {
                if (v.price_per_pyi && v.pyi_per_bag) {
                    displayPrice = parseInt(v.price_per_pyi) * parseInt(v.pyi_per_bag);
                    isRetailPossible = true;
                } else {
                    // Hide if retail is selected but not possible for this variant
                    return;
                }
            }

            const card = document.createElement('button');
            card.type = 'button';
            card.dataset.variantId = v.id;
            const outOfStock = v.stock_count <= 0;

            let cardClasses = 'variant-pill relative w-full px-3 py-3 text-xs md:text-sm font-medium rounded-xl border transition-all duration-200 text-left ';

            if (outOfStock) {
                cardClasses += 'border-slate-200 bg-slate-50 opacity-50 cursor-not-allowed';
            } else {
                cardClasses += 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer';
                if (parseInt(pickerSelectedId) === parseInt(v.id)) {
                    cardClasses = 'variant-pill relative w-full px-3 py-3 text-xs md:text-sm font-medium rounded-xl border-indigo-600 bg-indigo-50 ring-2 ring-indigo-600/20 shadow-md text-left';
                }
            }

            card.className = cardClasses;
            card.disabled = outOfStock;

            let priceText = parseInt(displayPrice).toLocaleString() + ' MMK';

            card.innerHTML = `
                <div class="flex items-center justify-between mb-1.5">
                    <span class="variant-name font-semibold text-slate-800 text-sm md:text-base">${v.name}</span>
                    ${outOfStock ? '<span class="text-[10px] font-medium text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded-full">Sold Out</span>' : ''}
                </div>
                <div class="flex items-center justify-between">
                    <span class="variant-price text-xs md:text-sm font-bold text-indigo-600">${priceText}</span>
                    <span class="select-check w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center ${parseInt(pickerSelectedId) === parseInt(v.id) ? '' : 'scale-0'} transition-transform duration-200">
                        <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                </div>
            `;

            if (!outOfStock) {
                card.onclick = () => pickerSelectVariant(v.id);
            }
            pillsContainer.appendChild(card);
        });

        // Add "more variants" indicator if needed
        if (pickerVariants.length > 10) {
            const moreIndicator = document.createElement('div');
            moreIndicator.className = 'col-span-full text-center py-2 text-xs md:text-sm text-slate-400 border-t border-slate-100 mt-2';
            moreIndicator.textContent = `+${pickerVariants.length - 10} more variants available`;
            pillsContainer.appendChild(moreIndicator);
        }
    }


    function openVariantPicker(productId, productName, variants, imageUrl) {
        pickerProductId   = productId;
        pickerProductName = productName;
        pickerVariants    = variants;
        pickerSelectedId  = null;
        pickerQty         = 1;
        pickerPriceMode   = 'wholesale'; // Reset to default

        // Title
        document.getElementById('variant-picker-title').innerText = productName;

        // Image
        const img = document.getElementById('picker-img');
        const placeholder = document.getElementById('picker-img-placeholder');
        if (imageUrl) {
            img.src = imageUrl;
            img.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            img.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }

        // Reset Mode UI
        setPriceMode('wholesale');

        // Reset UI
        document.getElementById('picker-qty-display').innerText = '1';
        document.getElementById('picker-total-display').innerText = '0 MMK';
        document.getElementById('picker-stock-label').classList.add('hidden');
        document.getElementById('picker-add-btn').disabled = true;
        document.getElementById('picker-add-btn').innerHTML = `
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add to Cart
        `;
        document.getElementById('picker-validation-msg').style.opacity = '0';

        const modal = document.getElementById('variant-picker-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }


    function pickerSelectVariant(variantId) {
        pickerSelectedId = variantId;
        pickerQty        = 1;

        // Re-render variants to update selection styling
        renderPickerVariants();

        const v = pickerVariants.find(x => x.id === variantId);
        if (!v) return;

        // Show stock info with balanced text
        const stockLabel = document.getElementById('picker-stock-label');
        stockLabel.innerHTML = `
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                ${v.stock_count} ${v.unit_label} available
            </span>
        `;
        stockLabel.classList.remove('hidden');

        // Reset qty and update displays
        document.getElementById('picker-qty-display').innerText = '1';
        document.getElementById('picker-add-btn').disabled = false;
        document.getElementById('picker-add-btn').innerHTML = `
            <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add ${v.name}
        `;
        document.getElementById('picker-validation-msg').style.opacity = '0';
        document.getElementById('picker-validation-msg').style.display = 'none';
        pickerUpdateTotal();
    }

    function pickerChangeQty(delta) {
        if (!pickerSelectedId) {
            // Show validation message with animation
            const msg = document.getElementById('picker-validation-msg');
            msg.style.opacity = '1';
            msg.style.display = 'block';
            msg.textContent = 'Please select a variant first';

            // Shake the variant section
            const variantSection = document.querySelector('#variant-picker-pills');
            variantSection.classList.add('animate-shake');
            setTimeout(() => variantSection.classList.remove('animate-shake'), 500);
            return;
        }

        const v = pickerVariants.find(x => x.id === pickerSelectedId);
        if (!v) return;

        const newQty = pickerQty + delta;
        if (newQty < 1 || newQty > v.stock_count) {
            // Show max/min message
            const msg = document.getElementById('picker-validation-msg');
            msg.style.opacity = '1';
            msg.style.display = 'block';
            msg.textContent = newQty < 1 ? 'Minimum quantity is 1' : `Maximum ${v.stock_count} available`;
            setTimeout(() => {
                msg.style.opacity = '0';
                msg.style.display = 'none';
            }, 2000);
            return;
        }

        pickerQty = newQty;
        document.getElementById('picker-qty-display').innerText = pickerQty;
        pickerUpdateTotal();
    }

    function pickerUpdateTotal() {
        if (!pickerSelectedId) {
            document.getElementById('picker-total-display').innerText = '0 MMK';
            return;
        }
        const v = pickerVariants.find(x => x.id === pickerSelectedId);
        if (!v) return;

        let unitPrice = 0;
        if (pickerPriceMode === 'wholesale') {
            unitPrice = parseInt(v.selling_price);
        } else {
            unitPrice = parseInt(v.price_per_pyi) * parseInt(v.pyi_per_bag);
        }

        const total = unitPrice * pickerQty;
        document.getElementById('picker-total-display').innerText = total.toLocaleString() + ' MMK';
    }

    function pickerAddToCart() {
        if (!pickerSelectedId) {
            const msg = document.getElementById('picker-validation-msg');
            msg.style.opacity = '1';
            msg.textContent = 'Please select a variant';
            return;
        }

        const v = pickerVariants.find(x => x.id === pickerSelectedId);
        if (!v) return;

        // Add the item
        const key = 'v' + v.id;

        let unitPrice = 0;
        let priceLabel = '';
        if (pickerPriceMode === 'wholesale') {
            unitPrice = parseInt(v.selling_price);
            priceLabel = '(Wholesale)';
        } else {
            unitPrice = parseInt(v.price_per_pyi) * parseInt(v.pyi_per_bag);
            priceLabel = '(Retail)';
        }

        if (!cart[key]) {
            cart[key] = {
                id: key,
                variant_id: v.id,
                product_id: pickerProductId,
                name: pickerProductName + ' – ' + v.name + ' ' + priceLabel,
                price: unitPrice,
                quantity: 0,
                maxStock: v.stock_count,
                discount: 0,
                warehouse_id: 1,
                unit_label: v.unit_label,
            };
        } else {
            // Update existing item in cart if price changed (retail/wholesale switch)
            cart[key].price = unitPrice;
            cart[key].name = pickerProductName + ' – ' + v.name + ' ' + priceLabel;
        }

        const spaceLeft = cart[key].maxStock - cart[key].quantity;
        const toAdd = Math.min(pickerQty, spaceLeft);

        if (toAdd <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Limit Reached',
                text: 'You already have all available stock in your bag.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            return;
        }

        cart[key].quantity += toAdd;
        renderCart();
        closeVariantPicker();

        // Show success toast
        Swal.fire({
            icon: 'success',
            title: 'Added to Bag!',
            text: `${toAdd} × ${v.name} added successfully`,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    }

    function closeVariantPicker() {
        const modal = document.getElementById('variant-picker-modal');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'opacity-0');
        }, 300);
    }

    // Add shake animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }
        .animate-shake {
            animation: shake 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
    `;
    document.head.appendChild(style);

    // ─── Cart Logic ──────────────────────────────────────────────────────────
    // Cart keyed by variant ID (prefixed: 'v{id}')
    function addToCart(productId, productName, variant) {
        const key = 'v' + variant.id;
        if (!cart[key]) {
            if (variant.stock_count <= 0) {
                alert('Out of stock!');
                return;
            }
            cart[key] = {
                id: key,          // unique cart key
                variant_id: variant.id,
                product_id: productId,
                name: productName + ' – ' + variant.name,
                price: parseInt(variant.selling_price),
                quantity: 0,
                maxStock: variant.stock_count,
                discount: 0,
                warehouse_id: 1,
                unit_label: variant.unit_label,
            };
        }
        if (cart[key].quantity < cart[key].maxStock) {
            cart[key].quantity++;
            renderCart();
        } else {
            alert('Max stock reached!');
        }
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const checkoutBtn = document.getElementById('checkout-btn');

        container.innerHTML = '';

        let subtotal = 0;
        let totalDiscount = 0;
        // Only include items with quantity > 0
        const items = Object.values(cart).filter(item => item.quantity > 0);

        if (items.length === 0) {
            container.appendChild(emptyMsgTemplate.cloneNode(true));
            checkoutBtn.disabled = true;
        } else {
            items.forEach(item => {
                const itemSubtotal = item.price * item.quantity;
                subtotal += itemSubtotal;
                totalDiscount += parseInt(item.discount || 0);

                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-white p-3 rounded-lg border border-slate-100 shadow-sm group hover:border-indigo-300 transition-colors cursor-pointer';
                div.onclick = (e) => {
                    if(!e.target.closest('button')) openModal(item.id);
                };

                div.innerHTML = `
                    <div class="flex-1 min-w-0 mr-3">
                        <div class="font-medium text-slate-800 text-sm truncate">${item.name}</div>
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                             <span>${parseInt(item.price).toLocaleString()} x ${item.quantity}</span>
                             ${item.discount > 0 ? `<span class="text-red-500 bg-red-50 px-1 rounded">-${parseInt(item.discount).toLocaleString()}</span>` : ''}
                             <span class="text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200 ml-auto uppercase font-bold">${warehouses[item.warehouse_id] || 'Shop 1'}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="updateQty('${item.id}', -1)" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 active:bg-slate-300 flex items-center justify-center text-2xl leading-none pb-1 transition-colors">-</button>
                        <span class="text-base font-bold w-6 text-center text-slate-800">${item.quantity}</span>
                        <button onclick="updateQty('${item.id}', 1)" class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 hover:bg-indigo-200 active:bg-indigo-300 flex items-center justify-center text-2xl leading-none pb-1 transition-colors">+</button>
                    </div>
                `;
                container.appendChild(div);
            });
            checkoutBtn.disabled = false;
        }

        const netTotal = subtotal - totalDiscount;

        document.getElementById('cart-subtotal').innerText = subtotal.toLocaleString() + ' MMK';
        document.getElementById('cart-discount').innerText = totalDiscount > 0 ? '-' + totalDiscount.toLocaleString() + ' MMK' : '0 MMK';
        document.getElementById('cart-total').innerText = netTotal.toLocaleString() + ' MMK';

        updatePaymentCalculations();
    }

    function updateQty(key, change) {
        if (cart[key]) {
            const newQty = cart[key].quantity + change;
            if (newQty > 0 && newQty <= cart[key].maxStock) {
                cart[key].quantity = newQty;
            } else if (newQty <= 0) {
                delete cart[key];
            }
            renderCart();
        }
    }

    function clearCart() {
        if(confirm('Clear cart?')) {
            cart = {};
            payments = [];
            renderCart();
            renderPayments();
        }
    }

    // --- Modal Logic ---
    const modal = document.getElementById('edit-modal');

    let activeEditField = 'price'; // price, qty, discount
    let editValues = {
        price: '0',
        qty: '0',
        discount: '0'
    };

    function setActiveInput(field) {
        activeEditField = field;

        // Reset styles for active state indicating
        ['price', 'qty', 'discount'].forEach(f => {
            const el = document.getElementById('input-btn-' + f);
            if(f === field) {
                el.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-900');
                el.classList.remove('border-slate-200', 'bg-white', 'text-slate-800', 'opacity-50', 'bg-slate-100');
            } else {
                el.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-900');
                el.classList.add('border-slate-200', 'bg-white', 'text-slate-800');

                // Keep disabled styling if applicable
                const customerId = document.getElementById('customer-select').value;
                if (f === 'price' && !customerId) {
                    el.classList.add('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
                }
            }
        });
    }

    function updateEditDisplay() {
        // Format with commas if it's purely a big number
        const formatNum = (val, field) => {
            if(!val) return '0';

            let num = parseInt(val);
            if(isNaN(num)) return '0';

            return num.toLocaleString();
        }

        document.getElementById('input-btn-price').innerText = formatNum(editValues.price, 'price');
        document.getElementById('input-btn-qty').innerText = formatNum(editValues.qty, 'qty');
        document.getElementById('input-btn-discount').innerText = formatNum(editValues.discount, 'discount');

        document.getElementById('modal-price').value = editValues.price || 0;
        document.getElementById('modal-qty').value = editValues.qty || 0;
        document.getElementById('modal-discount').value = editValues.discount || 0;
    }

    function appendEditNumpad(val) {
        let current = editValues[activeEditField].toString();

        // If current is just '0' and we type a number (not 00 or 000)
        if (current === '0' && val !== '00' && val !== '000') {
            current = val;
        } else if (current === '0' && (val === '00' || val === '000')) {
            current = '0';
        } else {
            // Optional cap length to prevent crazy values?
            if (current.length > 12) return;
            current += val;
        }

        editValues[activeEditField] = current;
        updateEditDisplay();
    }

    function popEditNumpad() {
        let current = editValues[activeEditField].toString();
        if (current.length > 1) {
            current = current.slice(0, -1);
        } else {
            current = '0';
        }
        editValues[activeEditField] = current;
        updateEditDisplay();
    }

    function clearEditNumpad() {
        editValues[activeEditField] = '0';
        updateEditDisplay();
    }

    function openModal(id) {
        const item = cart[id];
        if(!item) return;

        const customerId = document.getElementById('customer-select').value;
        const priceBtn = document.getElementById('input-btn-price');

        if (!customerId) {
            // Disable price editing for Walk-in customer
            priceBtn.onclick = null;
            priceBtn.classList.add('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
            priceBtn.title = "Cannot edit unit price for Walk-in customers";
        } else {
            // Enable price editing
            priceBtn.onclick = () => setActiveInput('price');
            priceBtn.classList.remove('opacity-50', 'bg-slate-100', 'cursor-not-allowed');
            priceBtn.title = "";
        }

        document.getElementById('modal-item-id').value = id;
        document.getElementById('modal-item-name').innerText = item.name;

        editValues.price = item.price.toString();
        editValues.qty = item.quantity.toString();
        editValues.discount = (item.discount || 0).toString();

        updateEditDisplay();
        setActiveInput('discount'); // default to editing discount

        document.getElementById('modal-warehouse').value = item.warehouse_id || 1;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function saveModal() {
        const key = document.getElementById('modal-item-id').value;
        const price = parseInt(document.getElementById('modal-price').value) || 0;
        const qty = parseInt(document.getElementById('modal-qty').value) || 0;
        const discount = parseInt(document.getElementById('modal-discount').value) || 0;
        const warehouseId = parseInt(document.getElementById('modal-warehouse').value) || 1;

        if (cart[key]) {
             if (qty <= 0) {
                delete cart[key];
            } else {
                 if(qty > cart[key].maxStock) {
                     alert('Exceeds stock!');
                     return;
                 }
                cart[key].price = price;
                cart[key].quantity = qty;
                cart[key].discount = discount;
                cart[key].warehouse_id = warehouseId;
            }
            renderCart();
            closeModal();
        }
    }

    function updatePaymentOptions(selectedId = null) {
        // If called without arg, get from hidden input
        const customerId = selectedId !== null ? selectedId : document.getElementById('customer-select').value;
        const creditBtn = document.getElementById('btn-credit');

        if (customerId) {
            creditBtn.disabled = false;
            creditBtn.classList.remove('text-slate-400', 'cursor-not-allowed', 'border-slate-200');
            creditBtn.classList.add('text-slate-600', 'hover:bg-slate-100', 'border-slate-200'); // Default state
            creditBtn.removeAttribute('title');
        } else {
            creditBtn.disabled = true;
            creditBtn.classList.add('text-slate-400', 'cursor-not-allowed', 'border-slate-200');
            creditBtn.classList.remove('bg-indigo-50', 'text-indigo-700', 'ring-2', 'ring-indigo-500', 'text-slate-600', 'hover:bg-slate-100');
            creditBtn.title = "Select a customer first";

            // If credit was in the payments list, remove it
            payments = payments.filter(p => p.method !== 'Credit');
            renderPayments();
            updatePaymentCalculations();
        }
    }

    function setPayment(method) {
        // Redundant with new multi-payment system, but keeping for compatibility if called
        addPayment(method);
    }

    function addPayment(method) {
        if(method === 'Credit' && document.getElementById('btn-credit').disabled) return;

        // Check if payment method already exists
        const existingIndex = payments.findIndex(p => p.method === method);

        if (existingIndex !== -1) {
             // Edit existing
             editPayment(existingIndex);
        } else {
             // Add new
             currentEditingPayment = { index: -1, method: method };
             openPaymentModal(method);
        }
    }

    function removePayment(index) {
        payments.splice(index, 1);
        renderPayments();
        updatePaymentCalculations();
    }

    function editPayment(index) {
        const payment = payments[index];
        currentEditingPayment = { index: index, method: payment.method };
        openPaymentModal(payment.method, payment.amount);
    }

    // --- Numpad Modal Logic ---
    function openPaymentModal(method, initialAmount = null) {
        const cartTotal = getCartTotal();
        const currentPaid = payments.reduce((sum, p, i) => i === (currentEditingPayment?.index ?? -1) ? sum : sum + p.amount, 0);
        const remaining = Math.max(0, cartTotal - currentPaid);

        document.getElementById('payment-modal-title').innerText = (currentEditingPayment.index === -1 ? 'Add ' : 'Edit ') + method + ' Payment';

        // Default to remaining amount if adding new, or current amount if editing
        numpadValue = (initialAmount !== null ? initialAmount : remaining).toString();
        updateNumpadDisplay();

        // Update Quick Button Text
        const quickBtn = document.getElementById('btn-quick-full');
        quickBtn.innerText = (payments.length === 0 || (payments.length === 1 && currentEditingPayment.index !== -1)) ? 'Full Amount' : 'Remaining';

        const confirmBtn = document.querySelector('#payment-modal button[onclick="confirmPaymentModal()"]');
        confirmBtn.innerText = currentEditingPayment.index === -1 ? 'Add Payment' : 'Save Changes';

        const modal = document.getElementById('payment-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex'); // Ensure flex is added back
    }

    function closePaymentModal() {
        const modal = document.getElementById('payment-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentEditingPayment = null;
    }

    function updateNumpadDisplay() {
        const val = parseInt(numpadValue) || 0;
        document.getElementById('payment-numpad-display').innerText = val.toLocaleString();
    }

    function appendNumpad(num) {
        if (numpadValue === '0') numpadValue = num.toString();
        else numpadValue += num.toString();
        updateNumpadDisplay();
    }

    function appendNumpadQuick(amount) {
        const current = parseFloat(numpadValue) || 0;
        numpadValue = (current + amount).toString();
        updateNumpadDisplay();
    }

    function setNumpadValue(type) {
        const cartTotal = getCartTotal();
        const otherPaid = payments.reduce((sum, p, i) => i === (currentEditingPayment?.index ?? -1) ? sum : sum + p.amount, 0);
        const remaining = Math.max(0, cartTotal - otherPaid);

        if (type === 'full_remain') {
            numpadValue = remaining.toString();
        }
        updateNumpadDisplay();
    }

    function clearNumpad() {
        numpadValue = '0';
        updateNumpadDisplay();
    }

    function popNumpad() {
        if (numpadValue.length > 1) {
            numpadValue = numpadValue.slice(0, -1);
        } else {
            numpadValue = '0';
        }
        updateNumpadDisplay();
    }

    function confirmPaymentModal() {
        let finalAmount = parseInt(numpadValue) || 0;

        // Validation: Cap input to remaining if not Cash
        // Or if user insists "don't allow user to input more than total amount" strictly:
        // We will interpret "total amount" as "Cart Total".
        // Logic: Cannot pay more than what is owed?
        // Let's cap at (CartTotal - OtherPayments) for ALL methods including Cash as per presumed request.
        // If they want change, they might need to relax this rule.
        // But the prompt was specific: "don't allow user to input more than total amount"

        const cartTotal = getCartTotal();
        const otherPaid = payments.reduce((sum, p, i) => i === (currentEditingPayment?.index ?? -1) ? sum : sum + p.amount, 0);
        const maxAllowed = Math.max(0, cartTotal - otherPaid);

        // STRICT CHECK: If final amount > maxAllowed (plus a small epsilon/tolerance if needed), cap it?
        // Or strictly strictly "Total Amount" (Cart Total).
        // If I put 50,000 for 8,000 item, that is > total amount.
        // I will cap it at maxAllowed.

        if (finalAmount > maxAllowed) {
             Swal.fire({
                 icon: 'error',
                 title: 'Invalid Amount',
                 text: 'Amount cannot exceed ' + maxAllowed.toLocaleString() + ' MMK',
                 toast: true,
                 position: 'top-end',
                 showConfirmButton: false,
                 timer: 3000
             });
             return;
        }

        if (currentEditingPayment.index === -1) {
            // Addition
            payments.push({
                method: currentEditingPayment.method,
                amount: finalAmount
            });
        } else {
            // Edit
            payments[currentEditingPayment.index].amount = finalAmount;
        }

        renderPayments();
        updatePaymentCalculations();

        // FIX: Ensure modal closes
        setTimeout(() => closePaymentModal(), 50);
    }

    function getCartTotal() {
        let subtotal = 0;
        let totalDiscount = 0;
        Object.values(cart).filter(item => item.quantity > 0).forEach(item => {
            subtotal += item.price * item.quantity;
            totalDiscount += parseInt(item.discount || 0);
        });
        return subtotal - totalDiscount;
    }

    function renderPayments() {
        const container = document.getElementById('active-payments');
        container.innerHTML = '';

        payments.forEach((payment, index) => {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-indigo-300 transition-colors group';
            div.onclick = (e) => {
                if(!e.target.closest('button')) editPayment(index);
            };
            div.innerHTML = `
                <div class="flex-1">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">${payment.method}</div>
                    <div class="text-sm font-bold text-slate-700">${payment.amount.toLocaleString()} MMK</div>
                </div>
                <button onclick="removePayment(${index})" class="text-slate-300 hover:text-red-500 p-1 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
                <div class="text-indigo-400 group-hover:text-indigo-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </div>
            `;
            container.appendChild(div);
        });
    }

    function updatePaymentCalculations() {
        const cartTotal = getCartTotal();
        const paidTotal = payments.reduce((sum, p) => sum + p.amount, 0);
        const remaining = cartTotal - paidTotal;

        document.getElementById('paid-total').innerText = paidTotal.toLocaleString() + ' MMK';
        document.getElementById('remaining-amount').innerText = Math.abs(remaining).toLocaleString() + ' MMK';

        const remainingLabel = document.getElementById('remaining-label');
        const remainingEl = document.getElementById('remaining-amount');

        if (remaining > 0) {
            // Remaining
            remainingLabel.childNodes[0].nodeValue = 'Remaining: '; // Update text node only
            remainingEl.className = 'text-red-500 font-bold ml-1';
            remainingEl.innerText = Math.abs(remaining).toLocaleString() + ' MMK';
        } else if (remaining < 0) {
            // Change
            remainingLabel.childNodes[0].nodeValue = 'Change: ';
            remainingEl.className = 'text-emerald-500 font-bold ml-1';
            remainingEl.innerText = Math.abs(remaining).toLocaleString() + ' MMK';
        } else {
            // Balanced
            remainingLabel.childNodes[0].nodeValue = 'Balanced ';
            remainingEl.className = 'text-emerald-600 font-bold ml-1';
            remainingEl.innerText = '✓';
        }

        const checkoutBtn = document.getElementById('checkout-btn');
        const hasCredit = payments.some(p => p.method === 'Credit');

        if (hasCredit) {
            checkoutBtn.disabled = (Math.abs(remaining) > 1) || cartTotal <= 0;
        } else {
            // If Change is allowed (remaining < 0), we enable checkout.
            // Only disable if remaining > 0 (underpaid)
            checkoutBtn.disabled = (remaining > 0) || cartTotal <= 0;
        }
    }

    // --- Checkout ---
    async function processCheckout() {
        if (Object.keys(cart).length === 0) return;

        const customerId = document.getElementById('customer-select').value;
        const checkoutBtn = document.getElementById('checkout-btn');
        checkoutBtn.disabled = true;
        checkoutBtn.innerText = 'Checking Stock...';

        const payload = {
            cart: Object.values(cart).filter(i => i.quantity > 0).map(item => ({
                variant_id: item.variant_id,
                quantity: item.quantity,
                unit_price: item.price,
                discount: item.discount,
                warehouse_id: item.warehouse_id
            })),
            payments: payments.map(p => ({
                method: p.method,
                amount: p.amount
            })),
            customer_id: customerId || null
        };

        try {
            // ✅ STEP 1 — CHECK STOCK FIRST
            const checkResponse = await fetch('{{ route("pos.checkStock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const checkResult = await checkResponse.json();
            // ✅ IF INSUFFICIENT
            if (checkResult.status === 'insufficient') {
                const transferResult = await confirmStockTransfer(checkResult);

                // Check if user cancelled or transfer failed
                if (!transferResult.transferred) {

                    // Show appropriate message
                    if (transferResult.cancelled) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Cancelled',
                            text: 'Checkout cancelled due to insufficient stock.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        cart = {};
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Transfer Failed',
                            text: transferResult.error || 'Failed to transfer stock. Please try again.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }

                    checkoutBtn.disabled = false;
                    checkoutBtn.innerText = 'Checkout';
                    return;
                }

            }

            // ✅ STEP 2 — PROCESS SALE
            checkoutBtn.innerText = 'Processing...';
            const response = await fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sale Successful!',
                    text: 'Invoice: ' + result.invoice,
                    confirmButtonText: 'OK'
                }).then(() => {
                    cart = {};
                    window.location.reload();
                });
            } else
            {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('System Error: ' + error.message);
            console.error(error);
        } finally {
            checkoutBtn.disabled = false;
            checkoutBtn.innerText = 'Checkout';
        }
    }

    async function confirmStockTransfer(checkResult) {
        const itemsList = checkResult.items.map(item =>
            `• ${item.product_name}: ${item.needed} units (From ${item.from_warehouse_name} to ${item.to_warehouse_name})`
        ).join('<br>');

        const result = await Swal.fire({
            title: '⚠️ Insufficient Stock',
        html: `
            <div style="text-align: left;">
                <p style="color: #dc3545; font-weight: bold;">Missing stock for:</p>
                <p style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    ${itemsList}
                </p>
                <p style="margin-top: 15px;">Transfer stock and continue?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, transfer',
        cancelButtonText: 'Cancel'
    });

    if (!result.isConfirmed) {
        return { transferred: false, cancelled: true };
    }

    try {
        // Show loading state
        Swal.fire({
            title: 'Transferring Stock',
            html: 'Please wait...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // ===========================================
        // CORRECT PAYLOAD FOR YOUR SERVICE
        // ===========================================

        // Since service processes ONE product at a time,
        // we need to handle each insufficient item separately

        const transferPromises = checkResult.items.map(async (item) => {
            if (!item.from_warehouse_id) {
                throw new Error(`Critical: No warehouse has enough stock of ${item.product_name}!`);
            }
            const payload = {
                product_id: item.product_id,           // $productId
                from_warehouse_id: item.from_warehouse_id, // $fromId
                to_warehouse_id: item.to_warehouse_id,   // $toId (Dynamically use selection)
                quantity: item.needed                  // $quantityToTransfer
            };

            const response = await fetch('{{ route("pos.transferStock") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(`Failed to transfer ${item.product_name}: ${error.message}`);
            }

            return await response.json();
        });

        // Wait for all transfers to complete
        const transferResults = await Promise.all(transferPromises);

        // Close loading
        Swal.close();

        // Show success
        await Swal.fire({
            icon: 'success',
            title: 'Stock Transferred!',
            text: 'All items have been transferred successfully.',
            timer: 2000,
            showConfirmButton: false
        });

        return {
            transferred: true,
            cancelled: false,
            results: transferResults
        };

    } catch (error) {
        Swal.close();

        await Swal.fire({
            icon: 'error',
            title: 'Transfer Failed',
            text: error.message || 'Failed to transfer stock',
            confirmButtonColor: '#dc3545'
        });

        return {
            transferred: false,
            cancelled: false,
            error: error.message
        };
    }
}
    // --- Customer Modal Logic ---
    function openCustomerModal() {
        const modal = document.getElementById('customer-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Focus search input after a short delay to allow transition
        setTimeout(() => {
            document.getElementById('customer-search').focus();
        }, 100);
    }

    function closeCustomerModal() {
        const modal = document.getElementById('customer-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('customer-search').value = '';
        filterCustomers(); // Reset filter
    }

    function filterCustomers() {
        const term = document.getElementById('customer-search').value.toLowerCase();
        const items = document.querySelectorAll('.customer-item');
        let visibleCount = 0;

        items.forEach(item => {
            // Always show Walk-in unless specifically searching
            // Check if it is the default walk-in item (no data-name attribute or explicit check)
            const name = item.dataset.name;

            if (!name) { // Walk-in customer
                 if(term === '' || 'walk-in customer'.includes(term)) {
                     item.style.display = 'flex';
                     visibleCount++;
                 } else {
                     item.style.display = 'none';
                 }
                 return;
            }

            if (name.includes(term)) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        document.getElementById('no-customers').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function selectCustomer(id, name, creditBalance) {
        document.getElementById('customer-select').value = id;
        document.getElementById('selected-customer-name').innerText = name;

        updatePaymentOptions(id);
        closeCustomerModal();
    }

    function updatePaymentOptions(selectedId = null) {
        const customerId = selectedId !== null ? selectedId : document.getElementById('customer-select').value;
        const creditBtn = document.getElementById('btn-credit');

        if (customerId) {
            creditBtn.disabled = false;
            creditBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            creditBtn.classList.add('hover:bg-slate-50', 'text-slate-700');
            creditBtn.classList.remove('text-slate-400', 'bg-slate-50');
            creditBtn.title = "";
        } else {
            creditBtn.disabled = true;
            creditBtn.classList.add('opacity-60', 'cursor-not-allowed', 'text-slate-400', 'bg-slate-50');
            creditBtn.classList.remove('hover:bg-slate-50', 'text-slate-700');
            creditBtn.title = "Select a customer first";

            // Remove any existing credit payments if switching to Walk-in
            payments = payments.filter(p => p.method !== 'Credit');
            renderPayments();
            updatePaymentCalculations();
        }
    }
</script>
@endsection
