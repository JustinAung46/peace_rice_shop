{{-- ============================================================
     SIDEBAR – Light Theme · Indigo/Slate · Matches POS Design
     ============================================================ --}}

@php
    $sidebarLogo  = \App\Models\Setting::get('site_logo');
    $sidebarName  = \App\Models\Setting::get('shop_name', 'RICE SHOP');
    $sidebarLogoMtime = $sidebarLogo && file_exists(storage_path('app/public/' . $sidebarLogo))
        ? filemtime(storage_path('app/public/' . $sidebarLogo))
        : '1';
@endphp

<style>
/* ── Base sidebar ─────────────────────────────────────── */
#sidebar {
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    box-shadow: 4px 0 24px rgba(0,0,0,0.06);
}

/* ── Logo bar ─────────────────────────────────────────── */
#sb-logo-bar {
    background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%);
    border-bottom: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
}
#sb-logo-bar::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 20% 50%, rgba(99,102,241,0.06) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Section labels ───────────────────────────────────── */
.sb-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #94a3b8;
    padding: 1rem 1.1rem 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.sb-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, #e2e8f0 0%, transparent 100%);
}

/* ── Nav links ────────────────────────────────────────── */
.sb-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 0.65rem 0.9rem;
    margin: 0.1rem 0.5rem;
    border-radius: 0.6rem;
    color: #64748b;
    font-size: 0.9375rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    cursor: pointer;
    border: 1px solid transparent;
    background: transparent;
    width: 100%;
    text-align: left;
}

/* Ripple on hover */
.sb-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
        rgba(99,102,241,0.08) 0%,
        transparent 65%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    border-radius: inherit;
}

/* Left accent bar */
.sb-link::after {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    height: 60%;
    width: 3px;
    background: linear-gradient(180deg, #6366f1, #818cf8);
    border-radius: 0 3px 3px 0;
    transform: scaleY(0);
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}

.sb-link:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #1e293b;
    transform: translateX(2px);
}
.sb-link:hover::before { opacity: 1; }

/* Active state */
.sb-link.active {
    background: #eef2ff;
    border-color: #c7d2fe;
    color: #4338ca;
    font-weight: 600;
    box-shadow: 0 1px 4px rgba(99,102,241,0.1);
}
.sb-link.active::after { transform: scaleY(1); }
.sb-link.active .sb-icon { color: #6366f1; }

/* Icon */
.sb-icon {
    width: 1.2rem;
    height: 1.2rem;
    flex-shrink: 0;
    transition: transform 0.2s ease, color 0.2s ease;
    color: #94a3b8;
}
.sb-link:hover .sb-icon {
    transform: scale(1.12);
    color: #6366f1;
}

/* ── Badge ────────────────────────────────────────────── */
.sb-badge {
    margin-left: auto;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    line-height: 1.4;
    flex-shrink: 0;
}
.sb-badge-red   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
.sb-badge-amber { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }

/* ── Submenu ──────────────────────────────────────────── */
.sb-submenu {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease;
    opacity: 0;
}
.sb-submenu.open {
    max-height: 300px;
    opacity: 1;
}
.sb-submenu-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.45rem 0.9rem 0.45rem 2.4rem;
    margin: 0.05rem 0.5rem;
    border-radius: 0.5rem;
    color: #94a3b8;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.18s ease;
    border: 1px solid transparent;
    position: relative;
}
.sb-submenu-link::before {
    content: '';
    position: absolute;
    left: 1.1rem;
    top: 50%;
    width: 0.3rem;
    height: 0.3rem;
    border-radius: 50%;
    background: #cbd5e1;
    transform: translateY(-50%);
    transition: background 0.18s ease;
}
.sb-submenu-link:hover {
    background: #f1f5f9;
    border-color: #e2e8f0;
    color: #334155;
    padding-left: calc(2.4rem + 2px);
}
.sb-submenu-link:hover::before { background: #6366f1; }
.sb-submenu-link.active {
    color: #4338ca;
    background: #eef2ff;
    border-color: #c7d2fe;
    font-weight: 600;
}
.sb-submenu-link.active::before { background: #6366f1; }

/* ── Chevron ──────────────────────────────────────────── */
.sb-chevron {
    margin-left: auto;
    width: 0.85rem;
    height: 0.85rem;
    flex-shrink: 0;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), color 0.2s ease;
    color: #cbd5e1;
}
.sb-chevron.rotated { transform: rotate(90deg); color: #6366f1; }

/* ── Stagger load animation ───────────────────────────── */
@keyframes sbSlideIn {
    from { opacity: 0; transform: translateX(-12px); }
    to   { opacity: 1; transform: translateX(0); }
}
.sb-animate {
    opacity: 0;
    animation: sbSlideIn 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

/* ── Scrollbar ────────────────────────────────────────── */
#sidebar nav::-webkit-scrollbar { width: 3px; }
#sidebar nav::-webkit-scrollbar-track { background: transparent; }
#sidebar nav::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 99px;
}
#sidebar nav::-webkit-scrollbar-thumb:hover {
    background: #c7d2fe;
}

/* ── Collapsed state ──────────────────────────────────── */
.sidebar-closed #sidebar {
    transform: translateX(-100%);
}
@media (min-width: 1024px) {
    .sidebar-closed #sidebar {
        width: 0 !important;
        transform: translateX(0);
        border-right-color: transparent;
    }
}

/* ── Bottom user pill ─────────────────────────────────── */
#sb-user-pill {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
</style>


<div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 h-full flex flex-col transform transition-all duration-300 ease-in-out lg:static overflow-hidden">

    {{-- ── Logo ── --}}
    <div id="sb-logo-bar" class="flex items-center h-14 px-5 gap-3 flex-shrink-0">
        @if($sidebarLogo)
            <div class="relative flex-shrink-0">
                <div class="absolute inset-0 rounded-xl bg-indigo-200/50 blur-sm"></div>
                <img src="{{ asset('storage/' . $sidebarLogo) }}?v={{ $sidebarLogoMtime }}"
                     alt="Shop logo"
                     class="relative h-9 w-9 rounded-xl object-contain shadow-sm"
                     width="36" height="36" fetchpriority="high" />
            </div>
        @else
            <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-md flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        @endif
        <div class="flex flex-col leading-tight">
            <span class="text-slate-800 font-bold text-base tracking-wide">{{ $sidebarName }}</span>
            <span class="text-indigo-400 text-xs font-semibold tracking-widest uppercase"></span>
        </div>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="flex-1 overflow-y-auto py-3" id="sb-nav">
        <ul class="space-y-0.5" id="sb-nav-list">

            {{-- Dashboard --}}
            <li class="sb-animate" style="animation-delay:0.04s">
                <a href="{{ route('dashboard') }}" class="sb-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- ─── INVENTORY ─── --}}
            @can('view-inventory')
            <li class="sb-animate" style="animation-delay:0.08s">
                <div class="sb-section-label">Inventory</div>
            </li>
            <li class="sb-animate" style="animation-delay:0.10s">
                <a href="{{ route('inventory.index') }}" class="sb-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Stock Management</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.12s">
                <a href="{{ route('warehouses.index') }}" class="sb-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Warehouses</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.14s">
                <a href="{{ route('categories.index') }}" class="sb-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span>Categories</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.16s">
                <a href="{{ route('inventory.stock.add') }}" class="sb-link {{ request()->routeIs('inventory.stock.add') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Add Stock (Inbound)</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.18s">
                <a href="{{ route('inventory.transfer') }}" class="sb-link {{ request()->routeIs('inventory.transfer') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>Stock Transfer</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.20s">
                <a href="{{ route('inventory.transform') }}" class="sb-link {{ request()->routeIs('inventory.transform') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2-2-2-2m4 0l2 2-2 2m0-10a7 7 0 00-7 7 7 7 0 007 7 7 7 0 007-7 7 7 0 00-7-7z"/>
                    </svg>
                    <span>Stock Transform</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.22s">
                <a href="{{ route('inventory.batches') }}" class="sb-link {{ request()->routeIs('inventory.batches') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Stock Batches</span>
                </a>
            </li>
            @endcan

            {{-- ─── SALES ─── --}}
            <li class="sb-animate" style="animation-delay:0.24s">
                <div class="sb-section-label">Sales</div>
            </li>
            @can('view-pos')
            <li class="sb-animate" style="animation-delay:0.26s">
                <a href="{{ route('pos.index') }}" class="sb-link {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Point of Sale</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.28s">
                <a href="{{ route('customers.index') }}" class="sb-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Customers</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.30s">
                <a href="{{ route('credits.index') }}" class="sb-link {{ request()->routeIs('credits.*') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span>Credit Report</span>
                    @php $totalCredit = \App\Models\Customer::where('credit_balance', '>', 0)->count(); @endphp
                    @if($totalCredit > 0)
                        <span class="sb-badge sb-badge-red">{{ $totalCredit }}</span>
                    @endif
                </a>
            </li>
            @endcan

            {{-- ─── REPORTS ─── --}}
            @can('view-profit')
            <li class="sb-animate" style="animation-delay:0.32s">
                <div class="sb-section-label">Reports</div>
            </li>
            <li class="sb-animate" style="animation-delay:0.34s">
                <a href="{{ route('reports.index') }}" class="sb-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Profit Dashboard</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.36s">
                <a href="{{ route('reports.daily') }}" class="sb-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Daily Sale Report</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.38s">
                <a href="{{ route('reports.items') }}" class="sb-link {{ request()->routeIs('reports.items') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Sale Items Report</span>
                </a>
            </li>
            <li class="sb-animate" style="animation-delay:0.40s">
                <a href="{{ route('reports.receipts') }}" class="sb-link {{ request()->routeIs('reports.receipts') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Receipts</span>
                </a>
            </li>
            @endcan

            {{-- ─── PURCHASE ORDERS ─── --}}
            @can('admin')
            @php
                $poActive  = request()->routeIs('purchase-orders.*') || request()->routeIs('suppliers.*');
                $pendingPo = \App\Models\PurchaseOrder::where('receive_status','!=','received')->count();
            @endphp

            <li class="sb-animate" style="animation-delay:0.42s">
                <div class="sb-section-label">Purchase Orders</div>
            </li>
            <li class="sb-animate" style="animation-delay:0.44s">
                <button id="po-menu-btn" onclick="togglePoMenu()" class="sb-link {{ $poActive ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="flex-1 text-left">Purchase Orders</span>
                    @if($pendingPo > 0)
                        <span class="sb-badge sb-badge-amber mr-1">{{ $pendingPo }}</span>
                    @endif
                    <svg id="po-chevron" class="sb-chevron {{ $poActive ? 'rotated' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <ul id="po-submenu" class="sb-submenu {{ $poActive ? 'open' : '' }}">
                    <li>
                        <a href="{{ route('purchase-orders.index') }}" class="sb-submenu-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            All Orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('purchase-orders.create') }}" class="sb-submenu-link">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            New Order
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('suppliers.index') }}" class="sb-submenu-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Suppliers
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ─── ADMINISTRATION ─── --}}
            <li class="sb-animate" style="animation-delay:0.46s">
                <div class="sb-section-label">Administration</div>
            </li>
            <li class="sb-animate" style="animation-delay:0.48s">
                <a href="{{ route('accounts.index') }}" class="sb-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                    <svg class="sb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Accounts</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>

    {{-- ── Bottom user pill ── --}}
    <div id="sb-user-pill" class="flex items-center gap-3 px-4 py-3 flex-shrink-0">
        @php
            $sbUser     = auth()->user();
            $sbName     = $sbUser->name ?? 'User';
            $sbRole     = $sbUser->role ?? 'Staff';
            $sbInitials = collect(explode(' ', $sbName))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
        @endphp
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm"
             style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
            {{ $sbInitials }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-slate-700 text-sm font-semibold truncate leading-tight">{{ $sbName }}</p>
            <p class="text-slate-400 text-xs truncate leading-tight">{{ $sbRole }}</p>
        </div>
    </div>

</div>

<script>
(function () {
    /* ── Glowing ripple on hover ─────────────────────── */
    document.querySelectorAll('.sb-link').forEach(function (link) {
        link.addEventListener('mousemove', function (e) {
            var rect = link.getBoundingClientRect();
            var x = ((e.clientX - rect.left) / rect.width * 100).toFixed(1) + '%';
            var y = ((e.clientY - rect.top) / rect.height * 100).toFixed(1) + '%';
            link.style.setProperty('--mouse-x', x);
            link.style.setProperty('--mouse-y', y);
        });
    });

    /* ── Purchase Orders submenu ─────────────────────── */
    window.togglePoMenu = function () {
        var submenu = document.getElementById('po-submenu');
        var chevron = document.getElementById('po-chevron');
        if (!submenu) return;
        submenu.classList.toggle('open');
        chevron && chevron.classList.toggle('rotated');
    };
})();
</script>
