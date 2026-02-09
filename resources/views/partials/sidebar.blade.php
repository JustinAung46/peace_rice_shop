<div class="flex flex-col flex-none w-64 bg-slate-900 h-full">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-slate-950 px-4">
        <span class="text-white text-xl font-bold tracking-wider">RICE SHOP</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-2">
            <li>
                <a href="{{ route('dashboard') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" class="current-fill" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>
            
            <li class="mt-4 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Inventory</li>
            <li>
                <a href="{{ route('inventory.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('inventory.*') && !request()->routeIs('inventory.transfer') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="text-sm font-medium">Rice Stock</span>
                </a>
            </li>
            <li>
                <a href="{{ route('inventory.stock.add') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('inventory.stock.add') ? 'bg-slate-800 text-white' : '' }}">
                     <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="text-sm font-medium">Add Stock (Inbound)</span>
                </a>
            </li>
            <li>
                <a href="{{ route('inventory.transfer') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('inventory.transfer') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span class="text-sm font-medium">Stock Transfer</span>
                </a>
            </li>

            <li class="mt-4 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sales</li>
            <li>
                <a href="{{ route('pos.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('pos.index') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-sm font-medium">Point of Sale</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('reports.index') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium">Daily Profit</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
