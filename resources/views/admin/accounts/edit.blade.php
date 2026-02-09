@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Account: {{ $account->name }}</h2>

        @if ($errors->any())
            <div class="bg-red-50 text-red-500 p-4 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('accounts.update', $account->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $account->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="account_id" class="block text-gray-700 text-sm font-bold mb-2">Account ID (Login ID)</label>
                <input type="number" name="account_id" id="account_id" value="{{ old('account_id', $account->account_id) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Passcode (Leave blank to keep current)</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Min 4 characters">
            </div>

            <div class="mb-6">
                <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="employee" {{ old('role', $account->role) == 'employee' ? 'selected' : '' }}>Employee</option>
                    <option value="cashier" {{ old('role', $account->role) == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="admin" {{ old('role', $account->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div class="mb-6">
                <span class="block text-gray-700 text-sm font-bold mb-2">Permissions (For Non-Admins)</span>
                <div class="flex flex-col space-y-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="permissions[]" value="inventory_access" class="form-checkbox h-5 w-5 text-blue-600" {{ $account->hasPermission('inventory_access') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Inventory Access</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="permissions[]" value="pos_access" class="form-checkbox h-5 w-5 text-blue-600" {{ $account->hasPermission('pos_access') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">POS Access</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="permissions[]" value="profit_access" class="form-checkbox h-5 w-5 text-blue-600" {{ $account->hasPermission('profit_access') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Daily Profit Access</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Account
                </button>
                <a href="{{ route('accounts.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
