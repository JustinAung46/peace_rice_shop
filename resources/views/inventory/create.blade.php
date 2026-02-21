@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Add New Product</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Category <span class="text-slate-400 text-xs">(Optional)</span></label>
                    <select name="category_id" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">Product Name</label>
                    <input type="text" name="name" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Shwe Bo Paw San" required>
                </div>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">SKU / Barcode</label>
                <input type="text" name="sku" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional">
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Selling Price (MMK) <span class="text-red-500">*</span></label>
                <input type="number" name="current_selling_price" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" step="0.01" required>
            </div>

            <!-- Price per Pyi -->
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Price per Pyi (MMK) <span class="text-slate-400 text-xs">(Optional)</span></label>
                <input type="number" name="price_per_pyi" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" step="0.01">
            </div>

            <!-- Pyi per Bag -->
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Pyi Per Bag <span class="text-slate-400 text-xs">(For transformations)</span></label>
                <input type="number" name="pyi_per_bag" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 24 or 6" step="1" min="1">
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-base font-bold text-slate-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>

            <!-- Image Upload -->
            <div class="md:col-span-2">
                <label class="block text-base font-bold text-slate-700 mb-2">Product Image</label>
                <input type="file" name="image" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-slate-500">Allowed formats: JPG, PNG, GIF, SVG. Max size: 2MB.</p>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg shadow-md hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                Save Product
            </button>
        </div>
    </form>
</div>
@endsection
