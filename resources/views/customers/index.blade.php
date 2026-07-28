@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Customer Management</h1>
        <p class="text-slate-500 text-sm mt-1">Manage your customer database and track credit balances.</p>
    </div>
    <a href="{{ route('customers.create') }}" class="w-full sm:w-auto flex justify-center items-center px-6 py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow transition-all focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        New Customer
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 flex items-center shadow-sm">
        <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($customers as $customer)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col hover:shadow-md transition-shadow group relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-bl-full -z-10 group-hover:bg-indigo-50/50 transition-colors"></div>
        
        <div class="flex items-start justify-between mb-5 z-10">
            <div class="flex items-center">
                <div class="w-14 h-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl font-black mr-4 shadow-sm border border-indigo-50">
                    {{ substr($customer->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $customer->name }}</h3>
                    <p class="text-sm font-medium text-slate-500 flex items-center mt-0.5">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        {{ $customer->phone ?? 'No Phone' }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mb-5 flex-1 z-10">
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 mb-3">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Credit Balance</p>
                @if($customer->credit_balance > 0)
                    <p class="text-xl font-bold text-red-600">
                        {{ number_format($customer->credit_balance) }} <span class="text-sm font-medium">MMK</span>
                    </p>
                @else
                    <p class="text-lg font-bold text-slate-400">Settled (0 MMK)</p>
                @endif
            </div>
            
            <p class="text-sm text-slate-600 flex items-start">
                <svg class="w-4 h-4 mr-2 mt-0.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="line-clamp-2">{{ $customer->address ?? 'No Address' }}</span>
            </p>
        </div>
        
        <div class="flex gap-3 pt-4 border-t border-slate-100 z-10 mt-auto">
            <a href="{{ route('customers.edit', $customer->id) }}" class="flex-1 text-center py-3 bg-slate-100 font-bold text-slate-700 rounded-xl hover:bg-slate-200 transition-colors focus:ring-2 focus:ring-slate-300">Edit</a>
            <button type="button" onclick="openDeleteCustomerModal({{ $customer->id }}, '{{ addslashes($customer->name) }}')" class="flex-1 py-3 bg-red-50 font-bold text-red-600 rounded-xl hover:bg-red-100 transition-colors focus:ring-2 focus:ring-red-300">Delete</button>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-slate-200 border-dashed">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">No customers found</h3>
        <p class="text-slate-500 text-lg mb-8 max-w-md mx-auto">Build your customer base to track credit balances and sales history.</p>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center px-8 py-4 border border-transparent shadow-md text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform active:scale-95">
            <svg class="-ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Customer
        </a>
    </div>
    @endforelse
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-customer-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 overflow-y-auto">
    <div class="w-full max-w-md bg-white rounded-3xl overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-slate-200">
            <h3 class="text-xl font-bold text-slate-900">Confirm Delete</h3>
            <p id="delete-customer-message" class="mt-2 text-sm text-slate-600">Are you sure you want to delete this customer?</p>
        </div>
        <form id="delete-customer-form" method="POST" class="p-6 space-y-4">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteCustomerModal()" class="flex-1 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors">Cancel</button>
                <button type="submit" class="flex-1 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDeleteCustomerModal(customerId, customerName) {
        const modal = document.getElementById('delete-customer-modal');
        const form = document.getElementById('delete-customer-form');
        const message = document.getElementById('delete-customer-message');

        form.action = '/customers/' + customerId;
        message.textContent = `Delete "${customerName}"? This action cannot be undone.`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteCustomerModal() {
        const modal = document.getElementById('delete-customer-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
