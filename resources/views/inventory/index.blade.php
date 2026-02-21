@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Inventory Management</h1>
    
    <!-- Large Action Buttons for Touch -->
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('inventory.movements') }}" class="flex-1 md:flex-none flex justify-center items-center px-5 py-3.5 bg-white text-slate-700 border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 hover:text-indigo-600 font-semibold transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            History & Records
        </a>
        <a href="{{ route('inventory.transform') }}" class="flex-1 md:flex-none flex justify-center items-center px-5 py-3.5 bg-amber-500 text-white rounded-xl shadow-sm hover:bg-amber-600 font-semibold transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            Transform
        </a>
        <a href="{{ route('inventory.create') }}" class="flex-1 md:flex-none flex justify-center items-center px-5 py-3.5 bg-indigo-600 text-white rounded-xl shadow-sm hover:bg-indigo-700 font-semibold transition-colors">
            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add Product
        </a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($products as $product)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-lg transition-shadow duration-300 group">
        <!-- Image Section -->
        <div class="aspect-[4/3] w-full bg-slate-100 flex items-center justify-center relative border-b border-slate-100 overflow-hidden">
            @if($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            @endif
            
            <!-- Category Badge -->
            @if($product->category)
            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 shadow-sm border border-indigo-100 uppercase tracking-wider">
                {{ $product->category->name }}
            </div>
            @endif

            <!-- Stock Status Badge Overlay -->
            <div class="absolute bottom-3 right-3 shadow-md">
                <span class="px-3 py-1.5 rounded-lg text-sm font-bold flex items-center shadow-inner {{ $product->total_stock > 10 ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    {{ number_format($product->total_stock ?? 0) }} Bags
                </span>
            </div>
        </div>
        
        <!-- Content Section -->
        <div class="p-5 flex-1 flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="font-bold text-xl text-slate-800 leading-tight mb-1">{{ $product->name }}</h3>
                <p class="text-sm text-slate-500 uppercase tracking-wider font-medium">SKU: {{ $product->sku ?? 'N/A' }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                    <p class="text-[11px] text-indigo-600 font-bold uppercase tracking-wider mb-1">Selling Price</p>
                    <p class="font-bold text-slate-800 text-lg sm:text-base md:text-lg">{{ number_format($product->current_selling_price) }} <span class="text-sm font-medium text-slate-500">MMK</span></p>
                </div>
                
                <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-100">
                    <p class="text-[11px] text-amber-700 font-bold uppercase tracking-wider mb-1">Unit Info</p>
                    <p class="font-bold text-slate-800 flex items-baseline">
                        {{ $product->pyi_per_bag ?? '1' }} <span class="ml-1 text-sm font-medium text-slate-500">Pyi/Bag</span>
                    </p>
                    @if($product->price_per_pyi)
                    <p class="text-xs text-slate-500 mt-1 font-medium">{{ number_format($product->price_per_pyi) }} MMK/Pyi</p>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <a href="{{ route('inventory.edit', $product->id) }}" class="flex items-center justify-center w-full py-3.5 bg-slate-100 rounded-xl text-slate-700 font-bold hover:bg-slate-200 hover:text-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-300 transform active:scale-[0.98]">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Details
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-slate-200 border-dashed">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">No products found</h3>
        <p class="text-slate-500 text-lg mb-8 max-w-md mx-auto">Get started by creating your first product. You can manage prices, stocks, and details easily.</p>
        <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-8 py-4 border border-transparent shadow-md text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform active:scale-95">
            <svg class="-ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Product Now
        </a>
    </div>
    @endforelse
</div>
@endsection
