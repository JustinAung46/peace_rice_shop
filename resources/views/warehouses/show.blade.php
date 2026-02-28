@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('warehouses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-block">&larr; Back to Warehouses</a>
        <h1 class="text-2xl font-bold text-slate-800">{{ $warehouse->name }} - Stock Summary</h1>
        <p class="text-slate-500">{{ $warehouse->location }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-100">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Category</label>
                <select id="category-filter" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-500 mb-1">Search</label>
                <input type="text" id="search-input" placeholder="Search product or variant" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold">
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4">Variant</th>
                    <th class="px-6 py-4">Unit</th>
                    <th class="px-6 py-4 text-right">Available Qty</th>
                    <th class="px-6 py-4 text-right">Last Purchase</th>
                </tr>
            </thead>
            <tbody id="stock-rows" class="divide-y divide-slate-100">
                @forelse($stockRows as $row)
                <tr class="hover:bg-slate-50 transition-colors"
                    data-name="{{ strtolower(($row['product_name'] ?? '') . ' ' . ($row['variant_name'] ?? '')) }}"
                    data-category="{{ $row['category_id'] ?? '' }}">
                    <td class="px-6 py-4 font-medium text-slate-800">
                        {{ $row['product_name'] }}
                        @if(!empty($row['category_name']))
                            <span class="ml-2 text-xs text-slate-500">({{ $row['category_name'] }})</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-700">{{ $row['variant_name'] ?? '-' }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $row['unit_label'] ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">{{ number_format($row['total_quantity']) }}</td>
                    <td class="px-6 py-4 text-right text-slate-500">{{ \Carbon\Carbon::parse($row['last_purchase_date'])->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                        No stock currently in this warehouse.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const categorySelect = document.getElementById('category-filter');
const searchInput = document.getElementById('search-input');
const rows = document.querySelectorAll('#stock-rows tr');

function applyFilter() {
    const cat = categorySelect.value;
    const term = searchInput.value.toLowerCase();
    let visible = 0;
    rows.forEach(tr => {
        const name = tr.dataset.name || '';
        const rowCat = tr.dataset.category || '';
        const matchesCat = !cat || rowCat === cat;
        const matchesSearch = !term || name.includes(term);
        const show = matchesCat && matchesSearch;
        tr.style.display = show ? '' : 'none';
        if (show) visible++;
    });
}

categorySelect.addEventListener('change', applyFilter);
searchInput.addEventListener('input', applyFilter);
</script>
@endsection
