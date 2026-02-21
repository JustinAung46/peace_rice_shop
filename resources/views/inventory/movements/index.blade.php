@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-slate-800">Stock Movements Record</h1>
    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg hover:bg-slate-300">
        Back to Inventory
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
        <h2 class="font-semibold text-slate-700">Recent Transfers & Transformations</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold border-b border-slate-200">
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Original Product</th>
                    <th class="px-6 py-4 text-center">Quantity (Bags)</th>
                    <th class="px-6 py-4">Details / Target</th>
                    <th class="px-6 py-4">Performed By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($movements as $movement)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                        {{ $movement->created_at->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($movement->type === 'warehouse_transfer')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Warehouse Transfer
                            </span>
                        @elseif($movement->type === 'bag_transformation')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                Bag Transformation
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                {{ ucfirst(str_replace('_', ' ', $movement->type)) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-800">
                        {{ $movement->product ? $movement->product->name : 'Unknown Product' }}
                        @if($movement->product && $movement->product->pyi_per_bag)
                            <span class="text-xs text-slate-500 block">({{ $movement->product->pyi_per_bag }} Pyi)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center font-semibold text-slate-700">
                        {{ number_format($movement->quantity, 2) }}
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        @if($movement->type === 'warehouse_transfer')
                            <div class="flex items-center text-xs">
                                <span class="text-slate-500">{{ $movement->fromWarehouse ? $movement->fromWarehouse->name : 'N/A' }}</span>
                                <svg class="w-4 h-4 mx-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                <span class="font-medium text-slate-800">{{ $movement->toWarehouse ? $movement->toWarehouse->name : 'N/A' }}</span>
                            </div>
                        @elseif($movement->type === 'bag_transformation')
                            <div class="flex items-center text-xs">
                                <span class="text-slate-500">To:</span>
                                <span class="ml-1 font-medium text-slate-800">{{ $movement->targetProduct ? $movement->targetProduct->name : 'N/A' }}</span>
                                @if($movement->targetProduct && $movement->targetProduct->pyi_per_bag)
                                    <span class="ml-1 text-slate-500">({{ $movement->targetProduct->pyi_per_bag }} Pyi)</span>
                                @endif
                                <br>
                                <span class="text-slate-500 ml-2">Wh: {{ $movement->fromWarehouse ? $movement->fromWarehouse->name : 'N/A' }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ $movement->user ? $movement->user->name : 'System' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <p class="text-lg font-medium text-slate-700">No records found</p>
                        <p class="text-sm">Transfers and transformations will appear here.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($movements->hasPages())
    <div class="p-4 border-t border-slate-200">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
