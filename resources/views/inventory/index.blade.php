@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Inventory Management</h1>
            <p class="text-slate-500 mt-1">Manage your products, track stock levels, and organize variants.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('inventory.movements') }}" class="flex items-center px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50 hover:text-indigo-600 font-medium transition-all text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                History
            </a>
            <a href="{{ route('inventory.transform') }}" class="flex items-center px-4 py-2 bg-white text-amber-600 border border-amber-200 rounded-lg shadow-sm hover:bg-amber-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                Transform Stock
            </a>
            <a href="{{ route('inventory.create') }}" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg shadow-sm hover:bg-indigo-700 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add Product
            </a>
        </div>
    </div>

    {{-- Search & Filter (Placeholder for future functionality, but adds to the UI) --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" id="inventorySearch" placeholder="Search products, variants, or SKU..." class="pl-10 w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-colors">
        </div>
        <div class="w-full sm:w-64">
            <select id="categoryFilter" class="w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-colors">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Product List --}}
    <div class="space-y-6" id="productList">
        @forelse($products as $product)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300 product-card" 
             data-category-id="{{ $product->category_id }}">
            <div class="flex flex-col md:flex-row">
                {{-- Product Info Sidebar --}}
                <div class="md:w-64 bg-slate-50/50 border-b md:border-b-0 md:border-r border-slate-100 p-6 flex flex-col items-center md:items-start text-center md:text-left">
                    <div class="relative mb-4 group">
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-lg shadow-sm border border-slate-200">
                        @else
                            <div class="w-24 h-24 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200 text-slate-300">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        @if(!$product->is_active)
                            <div class="absolute top-0 right-0 -mt-2 -mr-2">
                                <span class="flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500"></span>
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-800 mb-1 leading-tight">{{ $product->name }}</h3>
                    
                    @if($product->category)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 mb-3">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    <div class="mt-auto pt-4 w-full grid grid-cols-1 gap-2">
                        <a href="{{ route('inventory.edit', $product->id) }}" class="flex items-center justify-center px-3 py-2 border border-slate-200 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Product
                        </a>
                        <a href="{{ route('inventory.stock.add', ['product_id' => $product->id]) }}" class="flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add Stock
                        </a>
                    </div>
                </div>

                {{-- Variants Table --}}
                <div class="flex-1 p-0 overflow-x-auto max-h-[400px] overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 scrollbar-track-transparent">
                    <table class="w-full text-left text-sm relative">
                        <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 w-1/3">Variant</th>
                                <th class="px-6 py-3">Price</th>
                                <th class="px-6 py-3 w-1/4">Stock Level</th>
                                <th class="px-6 py-3 text-right">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($product->variants as $variant)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="font-medium text-slate-800">{{ $variant->name }}</div>
                                            @if($variant->sku)
                                                <div class="text-xs text-slate-400 font-mono mt-0.5">SKU: {{ $variant->sku }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700">{{ number_format($variant->selling_price) }} K</div>
                                    <div class="text-xs text-slate-400">per {{ $variant->unit_label }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-full max-w-xs">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-medium {{ ($variant->total_stock ?? 0) <= 10 ? 'text-red-600' : 'text-slate-600' }}">
                                                {{ number_format($variant->total_stock ?? 0) }} {{ $variant->unit_label }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full {{ ($variant->total_stock ?? 0) <= 10 ? 'bg-red-500' : 'bg-emerald-500' }}" 
                                                 style="width: {{ min(100, max(5, (($variant->total_stock ?? 0) / 100) * 100)) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(!$variant->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                            Inactive
                                        </span>
                                    @elseif(($variant->total_stock ?? 0) <= 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Out of Stock
                                        </span>
                                    @elseif(($variant->total_stock ?? 0) <= 10)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                            In Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('inventory.stock.add', ['product_id' => $product->id, 'product_variant_id' => $variant->id]) }}" class="inline-flex items-center px-3 py-1.5 border border-indigo-200 text-xs font-bold rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Add Stock
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                    No variants defined for this product.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="py-16 px-6 text-center bg-white rounded-2xl border-2 border-slate-200 border-dashed">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">No products found</h3>
            <p class="text-slate-500 mb-8">Create your first product to get started.</p>
            <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-6 py-3 text-base font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add New Product
            </a>
        </div>
        @endforelse
    </div>
</div>

<script>
    function filterInventory() {
        let searchText = document.getElementById('inventorySearch').value.toLowerCase();
        let selectedCategory = document.getElementById('categoryFilter').value;
        let productCards = document.querySelectorAll('.product-card');

        productCards.forEach(function(card) {
            let cardText = card.textContent.toLowerCase();
            let cardCategory = card.dataset.categoryId;
            
            let matchesSearch = cardText.includes(searchText);
            let matchesCategory = !selectedCategory || cardCategory == selectedCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    document.getElementById('inventorySearch').addEventListener('keyup', filterInventory);
    document.getElementById('categoryFilter').addEventListener('change', filterInventory);
</script>
@endsection
