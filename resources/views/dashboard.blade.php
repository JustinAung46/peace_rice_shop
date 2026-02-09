@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6">

    <!-- Welcome Banner -->
    <div class="col-span-12 mb-4">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    </div>

    <!-- Quick Action: Sale -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 cursor-pointer" onclick="window.location='{{ route('pos.index') }}'">
        <div class="bg-indigo-600 rounded-xl p-6 shadow-lg hover:bg-indigo-700 transition-colors flex items-center justify-between group">
            <div class="flex flex-col text-white">
                <span class="font-bold text-lg mb-1">New Sale</span>
                <span class="text-indigo-100 text-sm">Open Point of Sale</span>
            </div>
            <div class="bg-white/20 p-3 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </div>
    
    <!-- Quick Action: Stock -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 cursor-pointer" onclick="window.location='{{ route('inventory.stock.add') }}'">
        <div class="bg-emerald-600 rounded-xl p-6 shadow-lg hover:bg-emerald-700 transition-colors flex items-center justify-between group">
            <div class="flex flex-col text-white">
                <span class="font-bold text-lg mb-1">Add Stock</span>
                <span class="text-emerald-100 text-sm">Receive Inventory</span>
            </div>
            <div class="bg-white/20 p-3 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </div>
        </div>
    </div>
    
    <!-- Quick Action: Transfer -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 cursor-pointer" onclick="window.location='{{ route('inventory.transfer') }}'">
        <div class="bg-sky-600 rounded-xl p-6 shadow-lg hover:bg-sky-700 transition-colors flex items-center justify-between group">
            <div class="flex flex-col text-white">
                <span class="font-bold text-lg mb-1">Transfer</span>
                <span class="text-sky-100 text-sm">Warehouse -> Shop</span>
            </div>
            <div class="bg-white/20 p-3 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
            </div>
        </div>
    </div>
    
    <!-- Quick Action: Report -->
    <div class="col-span-12 sm:col-span-6 xl:col-span-4 cursor-pointer" onclick="window.location='{{ route('reports.index') }}'">
        <div class="bg-amber-600 rounded-xl p-6 shadow-lg hover:bg-amber-700 transition-colors flex items-center justify-between group">
            <div class="flex flex-col text-white">
                <span class="font-bold text-lg mb-1">Profit Report</span>
                <span class="text-amber-100 text-sm">View Daily Stats</span>
            </div>
             <div class="bg-white/20 p-3 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
    </div>

</div>
@endsection
