@extends('layouts.app')

@section('content')
@include('partials.alerts')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Transform Bag Sizes</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300 font-semibold">Back to Inventory</a>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-800">
    <strong>Note:</strong> Use this to convert stock from one bag size variant to another (e.g. 12 Pyi bags → 6 Pyi bags). Both source and target variants must have <em>Pyi per Bag</em> set.
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl mx-auto">
    <form action="{{ route('inventory.transform.process') }}" method="POST">
        @csrf
        <div class="space-y-5">

            {{-- Warehouse --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Warehouse <span class="text-red-500">*</span></label>
                <select name="warehouse_id" class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Source Product → Variant --}}
                <div class="space-y-4 p-4 bg-red-50 rounded-xl border border-red-100">
                    <h3 class="font-bold text-red-700 text-sm uppercase tracking-wide">Source (From)</h3>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Product</label>
                        <select id="source-product-select" onchange="loadVariants('source', this.value)"
                            class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-variants="{{ $product->variants->toJson() }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Variant</label>
                        <select name="original_variant_id" id="source-variant-select"
                            class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                            <option value="">— select product first —</option>
                        </select>
                    </div>
                </div>

                {{-- Target Product → Variant --}}
                <div class="space-y-4 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <h3 class="font-bold text-emerald-700 text-sm uppercase tracking-wide">Target (To)</h3>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Product</label>
                        <select id="target-product-select" onchange="loadVariants('target', this.value)"
                            class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-variants="{{ $product->variants->toJson() }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Variant</label>
                        <select name="target_variant_id" id="target-variant-select"
                            class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                            <option value="">— select product first —</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-base font-bold text-slate-700 mb-2">Quantity to Convert <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" min="1" step="1" required
                    class="w-full px-4 py-3 text-base border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Number of source bags to convert">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-10 py-4 bg-amber-500 text-white rounded-xl font-bold text-lg shadow-md hover:bg-amber-600 transition-all">
                    Transform Stock
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function loadVariants(prefix, productId) {
    const select = document.getElementById(`${prefix}-variant-select`);
    select.innerHTML = '<option value="">— select variant —</option>';
    if (!productId) return;
    const opt = document.querySelector(`#${prefix}-product-select option[value="${productId}"]`);
    if (!opt) return;
    const variants = JSON.parse(opt.dataset.variants || '[]');
    variants.forEach(v => {
        const o = document.createElement('option');
        o.value = v.id;
        o.textContent = v.pyi_per_bag
            ? `${v.name} (${v.pyi_per_bag} Pyi/Bag)`
            : v.name;
        select.appendChild(o);
    });
}
</script>
@endsection
