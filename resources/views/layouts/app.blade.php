<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rice Shop Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            @include('partials.header')

            <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-end">
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">Logout</button>
                </form>
            </div>


            <main class="grow">
                <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
