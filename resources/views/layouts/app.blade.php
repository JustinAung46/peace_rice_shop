<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rice Shop Management</title>
    <!-- Tom Select for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .page-transition {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }
        .page-loaded {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div id="layout-wrapper" class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            @include('partials.header')

            <main class="grow page-transition" id="main-content">
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

        // Initialize Tom Select globally for inputs with class 'searchable-select'
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.searchable-select').forEach(function(el) {
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    }
                });
            });

            // Auto-hide sidebar when a link is clicked
            const sidebarLinks = document.querySelectorAll('#sidebar nav a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    wrapper.classList.add('sidebar-closed');
                    localStorage.setItem('sidebar-expanded', 'false');
                });
            });
            
            // Trigger smooth page load transition
            setTimeout(() => {
                document.getElementById('main-content').classList.add('page-loaded');
            }, 50);
        });
    </script>
</body>
</html>
