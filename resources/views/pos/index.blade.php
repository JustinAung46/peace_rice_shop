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
            <div class="w-full sm:w-1/4">
                <div class="h-full px-4 py-2 rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col justify-center">
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold">Warehouse</span>
                    <select id="session-warehouse" class="w-full bg-transparent font-bold text-slate-800 focus:outline-none border-none p-0 h-6 text-sm cursor-pointer" onchange="updateSessionWarehouse()">
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>
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
                            <img src="{{ asset('storage/' . $product->image_path) }}" class="h-full w-full object-cover" loading="lazy" width="200" height="200">
                        @else
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        @endif
                    </div>

                    <h3 class="font-bold text-slate-800 text-base mb-2">{{ $product->name }}</h3>
                    @if($product->category)
                    <p class="text-xs text-indigo-600 font-semibold mb-2">{{ $product->category->name }}</p>
                    @endif

                    
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
                <button onclick="addPayment('Credit')" id="btn-credit" class="py-2.5 px-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 active:scale-95 transition-all shadow-sm flex flex-col items-center gap-1 opacity-60 cursor-not-allowed" disabled title="Select a customer first">
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

@include('pos.partials.variant-picker-modal')
@include('pos.partials.edit-modal')
@include('pos.partials.payment-modal')
@include('pos.partials.customer-modal')

<script>
    window.POS_CONFIG = {
        csrf: '{{ csrf_token() }}',
        routes: {
            checkStock: '{{ route("pos.checkStock") }}',
            store: '{{ route("pos.store") }}',
            transferStock: '{{ route("pos.transferStock") }}'
        },
        warehouses: {
            @foreach($warehouses as $w)
                {{ $w->id }}: '{{ addslashes($w->name) }}',
            @endforeach
        }
    };
</script>
@vite(['resources/js/pos_logic.js'])

@endsection
