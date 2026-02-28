<!-- Customer Modal -->
<div id="customer-modal" class="fixed inset-0 bg-black/60 z-[70] hidden flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[80vh]">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Select Customer</h3>
            <button onclick="closeCustomerModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="p-4 border-b border-slate-100 bg-white sticky top-0 z-10">
            <div class="relative">
                <input type="text" id="customer-search" placeholder="Search customers..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 shadow-sm" oninput="filterCustomers()">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-2 space-y-1" id="customer-list">
            <button onclick="selectCustomer('', 'Walk-in', 0)" class="w-full text-left px-4 py-3 rounded-xl hover:bg-slate-50 flex items-center justify-between group transition-colors customer-item">
                <span class="font-bold text-slate-800">Walk-in</span>
                <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-lg group-hover:bg-white">Default</span>
            </button>
            @foreach($customers as $customer)
            <button onclick="selectCustomer('{{ $customer->id }}', '{{ addslashes($customer->name) }}', {{ $customer->credit_balance }})" class="w-full text-left px-4 py-3 rounded-xl hover:bg-slate-50 flex items-center justify-between group transition-colors customer-item" data-name="{{ strtolower($customer->name) }}">
                <div class="flex flex-col">
                    <span class="font-bold text-slate-800 group-hover:text-indigo-700 transition-colors">{{ $customer->name }}</span>
                    <span class="text-xs text-slate-500">Credit Balance: {{ number_format($customer->credit_balance) }} MMK</span>
                </div>
                <svg class="w-5 h-5 text-slate-300 group-hover:text-indigo-500 opacity-0 group-hover:opacity-100 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </button>
            @endforeach
            <div id="no-customers" class="hidden text-center py-8 text-slate-400">No customers found</div>
        </div>
    </div>
</div>
