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
            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3 relative">
                <button id="account-menu-button" onclick="toggleAccountMenu()" class="flex items-center space-x-3 focus:outline-none group">
                    <div class="font-medium text-base text-slate-600 group-hover:text-slate-800 transition-colors">Admin</div>
                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-lg group-hover:bg-indigo-600 shadow-sm transition-colors">
                        A
                    </div>
                </button>

                <!-- Account Dropdown -->
                <div id="account-dropdown" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-2 hidden">
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">My Account</p>
                    </div>
                    <a href="{{ route('accounts.index') }}" class="block px-4 py-3 text-base text-slate-700 hover:bg-slate-50 font-medium">Manage Users</a>
                    <div class="border-t border-slate-100 mt-2 pt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-base text-red-600 hover:bg-red-50 font-bold transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
