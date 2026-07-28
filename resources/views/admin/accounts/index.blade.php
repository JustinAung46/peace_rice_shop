@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Account Management</h1>
        <a href="{{ route('accounts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
            Add New Account
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @php
        $siteLogo = \App\Models\Setting::get('site_logo');
        $shopName = \App\Models\Setting::get('shop_name', 'RICE SHOP');
    @endphp
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    @if($siteLogo)
                        @php
                            $indexLogoMtime = file_exists(storage_path('app/public/' . $siteLogo)) ? filemtime(storage_path('app/public/' . $siteLogo)) : '1';
                        @endphp
                        <img src="{{ asset('storage/' . $siteLogo) }}?v={{ $indexLogoMtime }}" alt="Shop logo" class="h-20 w-20 rounded-lg object-contain" width="80" height="80" />
                    @else
                        <div class="h-20 w-20 rounded-lg border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center text-slate-400">
                            No logo
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">Shop Branding</h2>
                        <p class="text-sm text-slate-600">Upload a logo and set the shop name displayed in the header and sidebar.</p>
                    </div>
                </div>
                <form action="{{ route('accounts.settings.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto">
                    @csrf
                    <div class="w-full max-w-sm">
                        <label class="block text-sm font-medium text-slate-700">Shop Name</label>
                        <input type="text" name="shop_name" value="{{ old('shop_name', $shopName) }}" placeholder="Enter shop name" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        @error('shop_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <label class="block w-full sm:w-auto">
                        <span class="sr-only">Choose logo</span>
                        <input type="file" name="site_logo" accept="image/*" class="block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 cursor-pointer" />
                        @error('site_logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </label>
                    <button type="submit" class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition">
                        Save Branding
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Account ID
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Role
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $user->name }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">{{ $user->account_id }}</p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold text-{{ $user->role === 'admin' ? 'red' : ($user->role === 'cashier' ? 'green' : 'blue') }}-900 leading-tight">
                                <span aria-hidden="true" class="absolute inset-0 bg-{{ $user->role === 'admin' ? 'red' : ($user->role === 'cashier' ? 'green' : 'blue') }}-200 opacity-50 rounded-full"></span>
                                <span class="relative">{{ ucfirst($user->role) }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="{{ route('accounts.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900 mr-4">Edit</a>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('accounts.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
