@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('customers.index') }}" class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-slate-800">Edit Customer</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Customer Name</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number <span class="text-slate-400 text-xs">(Optional)</span></label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Address <span class="text-slate-400 text-xs">(Optional)</span></label>
                <textarea name="address" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $customer->address) }}</textarea>
            </div>
            
            <div class="mb-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
                <label class="block text-sm font-medium text-slate-700 mb-1">Credit Balance (Read-only)</label>
                <div class="text-lg font-bold text-slate-800">{{ number_format($customer->credit_balance) }} MMK</div>
                <p class="text-xs text-slate-500 mt-1">Credit balance is updated automatically via POS credit sales.</p>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('customers.index') }}" class="px-4 py-2 text-slate-600 hover:text-slate-900 font-medium">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm shadow-indigo-200">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
