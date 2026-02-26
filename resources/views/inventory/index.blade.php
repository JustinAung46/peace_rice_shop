@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Inventory Management</h1>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('inventory.movements') }}" class="flex-1 md:flex-none flex justify-center items-center px-5 py-3.5 bg-white text-slate-700 border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 hover:text-indigo-600 font-semibold transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            History
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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($products as $product)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-lg transition-shadow duration-300 group">
        {{-- Image --}}
        <div class="aspect-[4/3] w-full bg-slate-100 flex items-center justify-center relative border-b border-slate-100 overflow-hidden">
            @if($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            @else
                <svg class="w-16 h-16 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            @endif
            @if($product->category)
            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 shadow-sm border border-indigo-100 uppercase tracking-wider">
                {{ $product->category->name }}
            </div>
            @endif
            @if(!$product->is_active)
            <div class="absolute top-3 right-3 bg-rose-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md uppercase tracking-wide">
                Inactive
            </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="p-5 flex-1 flex flex-col">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-bold text-xl text-slate-800 leading-tight">{{ $product->name }}</h3>
                <a href="{{ route('inventory.edit', $product->id) }}" class="flex items-center px-3 py-1.5 bg-slate-100 rounded-lg text-slate-600 font-semibold text-xs hover:bg-slate-200 hover:text-indigo-700 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </a>
            </div>

            {{-- Variants list --}}
            @if($product->variants->count() > 0)
            <div class="space-y-2">
                @foreach($product->variants as $variant)
                <div class="flex items-center justify-between bg-slate-50 rounded-xl px-3 py-2.5 border border-slate-100 {{ !$variant->is_active ? 'opacity-60 grayscale' : '' }}">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-sm text-slate-800 truncate">{{ $variant->name }}</p>
                            @if(!$variant->is_active)
                                <span class="text-[9px] font-bold text-rose-500 bg-rose-50 px-1 py-0.5 rounded uppercase">Inactive</span>
                            @endif
                        </div>
                        <p class="text-xs text-indigo-600 font-bold">{{ number_format($variant->selling_price) }} MMK <span class="text-slate-400 font-normal">/ {{ $variant->unit_label }}</span></p>
                    </div>
                    <span class="ml-3 shrink-0 px-2.5 py-1 rounded-lg text-xs font-bold {{ ($variant->total_stock ?? 0) > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                        {{ number_format($variant->total_stock ?? 0) }} left
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex-1 flex items-center justify-center py-4">
                <p class="text-sm text-slate-400">No variants yet.</p>
            </div>
            @endif

            {{-- Add stock shortcut --}}
            <a href="{{ route('inventory.stock.add') }}" class="flex items-center justify-center w-full mt-4 py-2.5 bg-indigo-50 rounded-xl text-indigo-700 font-semibold text-sm hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add Stock
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-slate-200 border-dashed">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">No products found</h3>
        <p class="text-slate-500 mb-8">Create your first product with variants to get started.</p>
        <a href="{{ route('inventory.create') }}" class="inline-flex items-center px-8 py-4 text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
            <svg class="-ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add New Product
        </a>
    </div>
    @endforelse
</div>
@endsection
