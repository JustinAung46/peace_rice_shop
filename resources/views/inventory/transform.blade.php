@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Transform Product Bags</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    @if(session('error'))
        <div class="p-4 mb-6 rounded-lg bg-red-50 text-red-700 border border-red-200">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="p-4 mb-6 rounded-lg bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('inventory.transform.process') }}" method="POST">
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
                <p class="text-xs text-slate-500 mt-1">Select a category to quickly narrow down the product lists below.</p>
            </div>

            <!-- Warehouse Selection -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Select Warehouse <span class="text-red-500">*</span></label>
                <select name="warehouse_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Choose Warehouse</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
                @error('warehouse_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Original Product -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Original Product (Full Bag) <span class="text-red-500">*</span></label>
                <select name="original_product_id" id="original_product_id" required class="searchable-select w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Choose Original Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-pyi="{{ $product->pyi_per_bag }}" data-category="{{ $product->category_id }}" {{ old('original_product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->pyi_per_bag }} Pyi/Bag)
                        </option>
                    @endforeach
                </select>
                @error('original_product_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Target Product -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Target Product (Unit Bag) <span class="text-red-500">*</span></label>
                <select name="target_product_id" id="target_product_id" required class="searchable-select w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Choose Target Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-pyi="{{ $product->pyi_per_bag }}" data-category="{{ $product->category_id }}" {{ old('target_product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->pyi_per_bag }} Pyi/Bag)
                        </option>
                    @endforeach
                </select>
                @error('target_product_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Quantity to Transform -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Quantity to Transform (Original Bags) <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required min="1" step="1" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 1">
                @error('quantity') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            
            <!-- Summary Output -->
            <div class="md:col-span-2 bg-indigo-50 rounded-lg p-4 border border-indigo-100 hidden" id="transform-summary">
                <p class="text-sm text-indigo-800 font-medium">Transformation Summary:</p>
                <p class="text-indigo-600 mt-1">Transform <span id="summary-qty" class="font-bold">0</span> bags of <span id="summary-original" class="font-bold">-</span> into <span id="summary-target-qty" class="font-bold text-lg">0</span> bags of <span id="summary-target" class="font-bold">-</span>.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Process Transformation
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const originalSelect = document.getElementById('original_product_id');
    const targetSelect = document.getElementById('target_product_id');
    const quantityInput = document.getElementById('quantity');
    const summaryDiv = document.getElementById('transform-summary');
    
    
    function updateSummary() {
        const originalOpt = originalSelect.options[originalSelect.selectedIndex];
        const targetOpt = targetSelect.options[targetSelect.selectedIndex];
        const qty = parseInt(quantityInput.value);
        
        if (originalOpt && targetOpt && originalOpt.value && targetOpt.value && qty > 0) {
            const originalPyi = parseInt(originalOpt.getAttribute('data-pyi')) || 0;
            const targetPyi = parseInt(targetOpt.getAttribute('data-pyi')) || 0;
            
            if (originalPyi > 0 && targetPyi > 0) {
                const targetQty = (qty * originalPyi) / targetPyi;
                
                document.getElementById('summary-qty').textContent = qty;
                document.getElementById('summary-original').textContent = originalOpt.text;
                document.getElementById('summary-target').textContent = targetOpt.text;
                document.getElementById('summary-target-qty').textContent = targetQty;
                
                summaryDiv.classList.remove('hidden');
            } else {
                summaryDiv.classList.add('hidden');
            }
        } else {
            summaryDiv.classList.add('hidden');
        }
    }
    
    // Setup category filtering for Tom Select instances
    const categoryFilter = document.getElementById('category_filter');
    
    function filterTomSelect(tomSelectInstance, categoryId) {
        if (!tomSelectInstance) return;
        
        tomSelectInstance.clear();
        tomSelectInstance.clearOptions();
        
        let hasOptions = false;
        
        Array.from(tomSelectInstance.input.options).forEach(option => {
            const optCat = option.getAttribute('data-category');
            if (!categoryId || optCat === categoryId || !option.value) {
                tomSelectInstance.addOption({
                    value: option.value,
                    text: option.text,
                    $option: option // Pass original option to keep attributes
                });
                hasOptions = true;
            }
        });
        
        tomSelectInstance.refreshOptions(false);
    }
    
    // Wait for Tom Select to be initialized (since it's done globally)
    setTimeout(() => {
        const originalTs = originalSelect.tomselect;
        const targetTs = targetSelect.tomselect;
        
        if (originalTs && targetTs) {
            categoryFilter.addEventListener('change', function() {
                const selectedCat = this.value;
                filterTomSelect(originalTs, selectedCat);
                filterTomSelect(targetTs, selectedCat);
            });
            
            originalTs.on('change', updateSummary);
            targetTs.on('change', updateSummary);
        }
    }, 100);

    quantityInput.addEventListener('input', updateSummary);
});
</script>
@endsection
