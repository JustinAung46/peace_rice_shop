@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Active Stock Batches</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300 font-medium transition-colors">
        Back to Inventory
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
    <form action="{{ route('inventory.batches') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
            <select name="category_id" id="filter_category" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Product</label>
            <select name="product_id" id="filter_product" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">All Products</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}" data-category="{{ $product->category_id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                    {{ $product->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Warehouse</label>
            <select name="warehouse_id" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                    {{ $warehouse->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex space-x-2 h-[38px] mt-6 md:mt-0">
            <button type="submit" class="flex-1 bg-indigo-600 text-white rounded-lg px-4 hover:bg-indigo-700 font-medium transition-colors flex items-center justify-center text-sm shadow-sm hover:shadow">
                Filter
            </button>
            <a href="{{ route('inventory.batches') }}" class="px-4 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-medium transition-colors flex items-center justify-center text-sm border border-slate-200">
                Clear
            </a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
        <h2 class="font-semibold text-slate-700">Stock Batches Overview</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-200">
                    <th class="px-6 py-4">Batch Code</th>
                    <th class="px-6 py-4">Product Variant</th>
                    <th class="px-6 py-4">Warehouse</th>
                    <th class="px-6 py-4 text-right">Original Quantity</th>
                    <th class="px-6 py-4 text-right">Remaining Quantity</th>
                    <th class="px-6 py-4 text-right">Cost Price</th>
                    <th class="px-6 py-4">Purchase Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($batches as $batch)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-slate-600 font-medium bg-slate-100 px-2 py-1 rounded text-xs border border-slate-200">
                            {{ $batch->batch_code ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $batch->product ? $batch->product->name : 'Unknown Product' }}</div>
                        @if($batch->variant)
                            <div class="text-xs text-indigo-600 font-semibold mt-0.5">{{ $batch->variant->name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 text-slate-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="text-slate-600">{{ $batch->warehouse ? $batch->warehouse->name : 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-500">
                        {{ number_format($batch->original_quantity) }} 
                        <span class="text-xs text-slate-400 font-normal ml-0.5">{{ $batch->variant ? $batch->variant->unit_label : '' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-medium text-slate-700">
                        {{ number_format($batch->remaining_quantity) }} 
                        <span class="text-xs text-slate-400 font-normal ml-0.5">{{ $batch->variant ? $batch->variant->unit_label : '' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-medium">
                        <div class="text-emerald-600">{{ number_format($batch->cost_price) }} K</div>
                    </td>
                    <td class="px-6 py-4 text-slate-500 whitespace-nowrap text-xs">
                        {{ $batch->purchase_date ? \Carbon\Carbon::parse($batch->purchase_date)->format('M d, Y') : 'N/A' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-10 text-center text-slate-500 bg-slate-50/30">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <p class="text-lg font-medium text-slate-700">No active stock batches found</p>
                        <p class="text-sm mt-1">Try adjusting your filters or add new stock.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($batches->count() > 0)
            <tfoot class="bg-slate-50 border-t border-slate-200">
                <tr>
                    <td colspan="4" class="px-6 py-3 font-semibold text-right text-slate-700">Total Found:</td>
                    <td class="px-6 py-3 text-right font-bold text-slate-800">{{ number_format($batches->sum('remaining_quantity')) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    
    @if($batches->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $batches->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('filter_category');
        const productSelect = document.getElementById('filter_product');
        
        // Save original options to memory
        const originalOptions = Array.from(productSelect.options);
        
        function filterProducts() {
            const selectedCategoryId = categorySelect.value;
            const currentSelected = productSelect.value;
            
            productSelect.innerHTML = '';
            let foundValidOption = false;
            
            originalOptions.forEach(option => {
                if (option.value === "") {
                    productSelect.appendChild(option);
                    if (currentSelected === "") foundValidOption = true;
                    return;
                }
                
                const optionCategory = option.getAttribute('data-category');
                
                if (selectedCategoryId === "" || optionCategory === selectedCategoryId) {
                    productSelect.appendChild(option);
                    if (currentSelected === option.value) foundValidOption = true;
                }
            });
            
            productSelect.value = foundValidOption ? currentSelected : "";
        }
        
        categorySelect.addEventListener('change', filterProducts);
        // Initialization on load is already done correctly if the server rendered it, 
        // but we can call it to sync the JS state.
        filterProducts();
    });
</script>
@endsection
