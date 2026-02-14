@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Add New Product</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-slate-400 text-xs">(Optional)</span></label>
                    <select name="category_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Product Name</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Shwe Bo Paw San" required>
                </div>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">SKU / Barcode</label>
                <input type="text" name="sku" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional">
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Selling Price (MMK) <span class="text-red-500">*</span></label>
                <input type="number" name="current_selling_price" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" step="0.01" required>
            </div>

            <!-- Price per Pyi -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Price per Pyi (MMK) <span class="text-slate-400 text-xs">(Optional)</span></label>
                <input type="number" name="price_per_pyi" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" step="0.01">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Save Product
            </button>
        </div>
    </form>
</div>
@endsection
