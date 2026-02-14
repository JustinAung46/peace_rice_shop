<div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 h-full transform transition-all duration-300 ease-in-out lg:static translate-x-0 [.sidebar-closed_&]:-translate-x-full lg:[.sidebar-closed_&]:w-0 lg:[.sidebar-closed_&]:translate-x-0 overflow-hidden">
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
            
            @can('view-inventory')
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
                <a href="{{ route('categories.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="text-sm font-medium">Categories</span>
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
            @endcan

            <li class="mt-4 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sales</li>
            @can('view-pos')
            <li>
                <a href="{{ route('pos.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('pos.index') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-sm font-medium">Point of Sale</span>
                </a>
            </li>
            <li>
                <a href="{{ route('customers.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-sm font-medium">Customers</span>
                </a>
            </li>
            @endcan
            
            @can('view-profit')
            <li class="mt-4 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Reports</li>
            <li>
                <a href="{{ route('reports.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('reports.index') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium">Profit Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.daily') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('reports.daily') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium">Daily Sale Report</span>
                </a>
            </li>
            <li>
                <a href="{{ route('reports.items') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('reports.items') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="text-sm font-medium">Sale Items Report</span>
                </a>
            </li>
            @endcan

            @can('admin')
            <li class="mt-4 px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Administration</li>
            <li>
                <a href="{{ route('accounts.index') }}" class="group flex items-center px-4 py-3 text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition-colors {{ request()->routeIs('accounts.*') ? 'bg-slate-800 text-white' : '' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" viewBox="0 24 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-sm font-medium">Accounts</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>
</div>
