<header class="sticky top-0 bg-white border-b border-slate-200 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 -mb-px">
            <!-- Header: Left side -->
            <div class="flex items-center">
                <!-- Sidebar Toggle Button (Desktop & Mobile) -->
                <button onclick="toggleSidebar()" class="p-2 -ml-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors" aria-controls="sidebar" aria-expanded="true">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="3" y="5" width="18" height="2" rx="1" />
                        <rect x="3" y="11" width="18" height="2" rx="1" />
                        <rect x="3" y="17" width="18" height="2" rx="1" />
                    </svg>
                </button>
                <div class="flex items-center ml-4">
                    @php
                        $siteLogo = \App\Models\Setting::get('site_logo');
                        $shopName = \App\Models\Setting::get('shop_name', 'RICE SHOP');
                        $headerLogoMtime = $siteLogo && file_exists(storage_path('app/public/' . $siteLogo)) ? filemtime(storage_path('app/public/' . $siteLogo)) : '1';
                    @endphp
                    @if($siteLogo)
                        <img src="{{ asset('storage/' . $siteLogo) }}?v={{ $headerLogoMtime }}" alt="Shop logo" class="h-10 w-auto rounded-md object-contain" width="40" height="40" fetchpriority="high" />
                    @endif
                    <span class="text-lg font-bold text-slate-900 tracking-wide {{ $siteLogo ? 'ml-3' : '' }}">{{ $shopName }}</span>
                </div>
            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3 relative">
                <button id="account-menu-button" onclick="toggleAccountMenu()" class="flex items-center space-x-2 focus:outline-none group px-2 py-1 rounded-xl hover:bg-slate-100 transition-colors">
                    @php
                        $userName = auth()->user()->name ?? 'User';
                        $initials = collect(explode(' ', $userName))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
                    @endphp

                    {{-- Gradient avatar with initials --}}
                    <div class="relative">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md transition-transform group-hover:scale-105"
                             style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%);">
                            {{ $initials }}
                        </div>
                        {{-- Online indicator --}}
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-400 border-2 border-white rounded-full"></span>
                    </div>

                    {{-- User name --}}
                    <div class="text-left hidden sm:block">
                        <p class="font-semibold text-sm text-slate-700 group-hover:text-slate-900 leading-tight transition-colors">{{ $userName }}</p>
                        <p class="text-xs text-slate-400 leading-tight">{{ auth()->user()->role ?? 'Staff' }}</p>
                    </div>

                    {{-- Chevron --}}
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Account Dropdown -->
                <div id="account-dropdown" class="absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden" style="z-index:50;">
                    {{-- User info header --}}
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                             style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%);">
                            {{ $initials }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-semibold text-sm text-slate-800 truncate">{{ $userName }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('accounts.index') }}" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 font-medium transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                        </svg>
                        <span>Manage Users</span>
                    </a>
                    <div class="border-t border-slate-100 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 font-semibold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
