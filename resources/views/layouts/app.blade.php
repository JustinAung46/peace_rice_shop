<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rice Shop Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div id="layout-wrapper" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            @include('partials.header')

            <main class="grow">
                <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        // Sidebar state management
        const wrapper = document.getElementById('layout-wrapper');
        const sidebarExpanded = localStorage.getItem('sidebar-expanded') !== 'false';

        if (!sidebarExpanded) {
            wrapper.classList.add('sidebar-closed');
        }

        function toggleSidebar() {
            wrapper.classList.toggle('sidebar-closed');
            const isClosed = wrapper.classList.contains('sidebar-closed');
            localStorage.setItem('sidebar-expanded', isClosed ? 'false' : 'true');
        }

        // Account Dropdown management
        function toggleAccountMenu() {
            const dropdown = document.getElementById('account-dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('account-dropdown');
            const accountBtn = document.getElementById('account-menu-button');
            if (dropdown && !dropdown.contains(e.target) && !accountBtn.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
