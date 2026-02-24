@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Add Stock (Inbound)</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('inventory.stock.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category Filter -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Filter Products by Category</label>
                <select id="category_filter" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Select Product</label>
                <select name="product_id" class="searchable-select w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Search and select a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-category="{{ $product->category_id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Warehouse Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Destination Warehouse</label>
                <select name="warehouse_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Quantity (Bags/Kg)</label>
                <input type="number" name="quantity" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" step="1" required>
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Cost Price per Unit (MMK)</label>
                <input type="number" name="cost_price" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" step="1" required>
                <p class="text-xs text-slate-500 mt-1">This is crucial for calculating exact profit.</p>
            </div>

            <!-- Purchase Date -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Purchase Date</label>
                <input type="date" name="purchase_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" value="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Batch Code -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Batch Code (Optional)</label>
                <input type="text" name="batch_code" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. BATCH-001">
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                Add Stock
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category_filter');
    const productSelect = document.querySelector('select[name="product_id"]');
    
    function filterTomSelect(tomSelectInstance, categoryId) {
        if (!tomSelectInstance) return;
        
        tomSelectInstance.clear();
        tomSelectInstance.clearOptions();
        
        Array.from(tomSelectInstance.input.options).forEach(option => {
            const optCat = option.getAttribute('data-category');
            if (!categoryId || optCat === categoryId || !option.value) {
                tomSelectInstance.addOption({
                    value: option.value,
                    text: option.text,
                    $option: option
                });
            }
        });
        
        tomSelectInstance.refreshOptions(false);
    }
    
    setTimeout(() => {
        const productTs = productSelect.tomselect;
        if (productTs) {
            categoryFilter.addEventListener('change', function() {
                filterTomSelect(productTs, this.value);
            });
        }
    }, 100);
});
</script>
@endsection
