@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Warehouse Management</h1>
        <p class="text-slate-500 text-sm mt-1">Manage all your storage locations and review their stock.</p>
    </div>
    @can('admin')
    <a href="{{ route('warehouses.create') }}" class="w-full sm:w-auto flex justify-center items-center px-6 py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow transition-all focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        New Warehouse
    </a>
    @endcan
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 flex items-center shadow-sm">
        <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200 flex items-center shadow-sm">
        <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($warehouses as $warehouse)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col relative overflow-hidden group hover:shadow-lg transition-shadow duration-300">
        <!-- Background decor -->
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-slate-50 rounded-full z-0 group-hover:bg-indigo-50/50 transition-colors duration-300"></div>
        
        <div class="relative z-10 flex-1">
            <h3 class="text-xl font-bold text-slate-800 mb-1 flex items-center">
                <svg class="w-6 h-6 mr-2 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="truncate">{{ $warehouse->name }}</span>
            </h3>
            <p class="text-sm text-slate-500 mb-5 flex items-start">
                <svg class="w-4 h-4 mr-1.5 mt-0.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="line-clamp-2 leading-relaxed">{{ $warehouse->location ?? 'No location specified' }}</span>
            </p>
            
            <div class="bg-indigo-50/30 rounded-xl p-5 border border-indigo-50 mb-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Stock Contained</p>
                <p class="text-3xl font-black text-slate-800 tracking-tight">
                    {{ number_format($warehouse->total_stock) }} <span class="text-base font-semibold text-slate-500 tracking-normal">Bags</span>
                </p>
            </div>
        </div>
        
        <div class="relative z-10 grid grid-cols-2 gap-3 mt-auto">
            <a href="{{ route('warehouses.show', $warehouse->id) }}" class="col-span-2 flex justify-center items-center py-3.5 bg-indigo-50 text-indigo-700 rounded-xl font-bold hover:bg-indigo-100 transition-colors focus:ring-2 focus:ring-indigo-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                View Inventory
            </a>
            
            @can('admin')
            <a href="{{ route('warehouses.edit', $warehouse->id) }}" class="flex justify-center items-center py-3 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition-colors">
                Edit
            </a>
            <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" class="inline-block col-span-1" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex justify-center items-center py-3 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100 transition-colors">
                    Delete
                </button>
            </form>
            @endcan
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-slate-200 border-dashed">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">No warehouses found</h3>
        <p class="text-slate-500 text-lg mb-8 max-w-md mx-auto">Get started by adding your first warehouse to begin managing stock across multiple locations.</p>
        @can('admin')
        <a href="{{ route('warehouses.create') }}" class="inline-flex items-center px-8 py-4 border border-transparent shadow-md text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform active:scale-95">
            <svg class="-ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Warehouse
        </a>
        @endcan
    </div>
    @endforelse
</div>
@endsection
