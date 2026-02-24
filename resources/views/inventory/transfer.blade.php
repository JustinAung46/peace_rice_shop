@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Transfer Stock</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('inventory.transfer.store') }}" method="POST">
        @csrf
        
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-lg">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                <p class="text-xs text-slate-500 mt-1">Select a category to quickly narrow down the products below.</p>
            </div>

            <!-- Product Selection -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Select Product to Move</label>
                <select name="product_id" class="searchable-select w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Search and select a product...</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-category="{{ $product->category_id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Source Warehouse -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">From (Source)</label>
                <select name="from_warehouse_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $warehouse->name == 'Warehouse 2 (Storage)' ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Destination Warehouse -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">To (Destination)</label>
                <select name="to_warehouse_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ $warehouse->name == 'Shop 1 (Main)' ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Quantity -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Quantity to Transfer</label>
                <input type="number" name="quantity" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" step="1" required>
                <p class="text-xs text-slate-500 mt-1">System will automatically take from the oldest batches first (FIFO).</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Transfer Stock
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
