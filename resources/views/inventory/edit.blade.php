@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Edit Product: {{ $product->name }}</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('inventory.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-slate-400 text-xs">(Optional)</span></label>
                    <select name="category_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">SKU / Barcode</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Selling Price (MMK) <span class="text-red-500">*</span></label>
                <input type="number" name="current_selling_price" value="{{ old('current_selling_price', $product->current_selling_price) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01" required>
            </div>

             <!-- Price per Pyi -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Price per Pyi (MMK) <span class="text-slate-400 text-xs">(Optional)</span></label>
                <input type="number" name="price_per_pyi" value="{{ old('price_per_pyi', $product->price_per_pyi) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" step="0.01">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Image Upload -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Product Image</label>
                @if($product->image_path)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-32 w-32 object-cover rounded-lg border border-slate-200 shadow-sm">
                        <p class="mt-1 text-xs text-slate-500">Current Image</p>
                    </div>
                @endif
                <input type="file" name="image" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-slate-500">Upload new to replace. Allowed: JPG, PNG, GIF, SVG. Max: 2MB.</p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
             <a href="{{ route('inventory.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Update Product
            </button>
        </div>
    </form>
</div>
@endsection
