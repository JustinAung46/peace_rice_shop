@extends('layouts.app')

@section('content')
@include('partials.alerts')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Transfer Stock Between Warehouses</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300 font-semibold">Back to Inventory</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('inventory.transfer.store') }}" method="POST">
        @csrf
        <div class="space-y-5">

            {{-- Product --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Product <span class="text-red-500">*</span></label>
                <select id="product-select" onchange="loadVariants(this.value)"
                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-variants="{{ $product->variants->toJson() }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Variant --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Variant <span class="text-red-500">*</span></label>
                <select name="product_variant_id" id="variant-select"
                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                    <option value="">— Select product first —</option>
                </select>
            </div>

            {{-- From / To Warehouse --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">From Warehouse <span class="text-red-500">*</span></label>
                    <select name="from_warehouse_id"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-base font-bold text-slate-700 mb-2">To Warehouse <span class="text-red-500">*</span></label>
                    <select name="to_warehouse_id"
                        class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" min="1" step="1" required
                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="e.g. 50">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-10 py-4 bg-amber-500 text-white rounded-xl font-bold text-lg shadow-md hover:bg-amber-600 transition-all">
                    Transfer Stock
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function loadVariants(productId) {
    const variantSelect = document.getElementById('variant-select');
    variantSelect.innerHTML = '<option value="">— Select variant —</option>';
    if (!productId) return;
    const option = document.querySelector(`#product-select option[value="${productId}"]`);
    if (!option) return;
    const variants = JSON.parse(option.dataset.variants || '[]');
    variants.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = `${v.name} — ${Number(v.selling_price).toLocaleString()} MMK / ${v.unit_label}`;
        variantSelect.appendChild(opt);
    });
}
</script>
@endsection
