@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Add New Product</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">Back to List</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Product Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="e.g. Rice, Oil, Sugar" required value="{{ old('name') }}">
            </div>
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Category <span class="text-slate-400 text-xs">(Optional)</span></label>
                <select name="category_id"required class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-base font-bold text-slate-700 mb-2">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-base font-bold text-slate-700 mb-2">Product Image</label>
                <input type="file" name="image" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700">
            </div>
            <div class="md:col-span-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </div>
                    <span class="text-base font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">Active Product</span>
                </label>
                <p class="text-xs text-slate-400 mt-1 ml-14">Inactive products will not appear in the POS.</p>
            </div>
        </div>

        {{-- Variants Section --}}
        <div class="border-t border-slate-200 pt-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Product Variants</h2>
                    <p class="text-sm text-slate-500">Add each size/type as a separate variant. Each variant has its own price and stock.</p>
                </div>
                <button type="button" onclick="addVariantRow()" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Variant
                </button>
            </div>

            <div id="variants-container" class="space-y-3">
                {{-- Rows added by JS --}}
            </div>
            <p id="no-variants-msg" class="text-center text-slate-400 py-6 border-2 border-dashed border-slate-200 rounded-xl hidden">No variants yet. Click "Add Variant" to get started.</p>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-xl font-bold text-lg shadow-md hover:bg-indigo-700 transition-all">
                Save Product
            </button>
        </div>
    </form>
</div>

<script>
let variantCount = 0;

function addVariantRow(data = {}) {
    variantCount++;
    const idx = variantCount;
    const container = document.getElementById('variants-container');
    document.getElementById('no-variants-msg').classList.add('hidden');

    const row = document.createElement('div');
    row.id = `variant-row-${idx}`;
    row.className = 'grid grid-cols-2 md:grid-cols-8 gap-3 p-4 bg-slate-50 rounded-xl border border-slate-200 items-end relative';
    row.innerHTML = `
        <div class="md:col-span-1.5">
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide text-truncate">Variant Name *</label>
            <input type="text" name="variants[${idx}][name]" required placeholder="e.g. 6 Pyi"
                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="${data.name||''}">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide">Unit *</label>
            <input type="text" name="variants[${idx}][unit_label]" required placeholder="Bag"
                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="${data.unit_label||''}">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide">Wholesale *</label>
            <input type="number" name="variants[${idx}][selling_price]" required min="0" step="1" placeholder="0"
                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="${data.selling_price||''}">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide">Bag Factor</label>
            <input type="number" name="variants[${idx}][bag_factor]" min="0" step="any" placeholder="1 or 0.5"
                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="${data.bag_factor||''}">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide">Retail (Bag)</label>
            <input type="number" name="variants[${idx}][retail_price]" min="0" step="1" placeholder="0"
                class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="${data.retail_price||''}">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide text-center">Active</label>
            <div class="flex justify-center h-[42px] items-center">
                <input type="checkbox" name="variants[${idx}][is_active]" value="1" checked class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
            </div>
        </div>
        <div class="md:col-span-1.5">
            <label class="block text-[10px] font-bold text-slate-600 mb-1 uppercase tracking-wide">SKU</label>
            <div class="flex gap-2">
                <input type="text" name="variants[${idx}][sku]" placeholder="SKU"
                    class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" value="${data.sku||''}">
                <button type="button" onclick="removeVariantRow(${idx})" class="shrink-0 w-10 h-10 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-100 border border-red-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
}

function removeVariantRow(idx) {
    const row = document.getElementById(`variant-row-${idx}`);
    if (row) row.remove();
    if (document.getElementById('variants-container').children.length === 0) {
        document.getElementById('no-variants-msg').classList.remove('hidden');
    }
}

// Start with one empty variant row
addVariantRow();
</script>
@endsection
