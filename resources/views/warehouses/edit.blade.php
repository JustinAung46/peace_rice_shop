@extends('layouts.app')

@section('content')
<div class="mb-6">
    <a href="{{ route('warehouses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-block">&larr; Back to Warehouses</a>
    <h1 class="text-2xl font-bold text-slate-800">{{ isset($warehouse) ? 'Edit Warehouse' : 'Add New Warehouse' }}</h1>
</div>

<div class="max-w-2xl bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <form action="{{ isset($warehouse) ? route('warehouses.update', $warehouse->id) : route('warehouses.store') }}" method="POST">
        @csrf
        @if(isset($warehouse))
            @method('PUT')
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Warehouse Name</label>
                <input type="text" name="name" value="{{ old('name', $warehouse->name ?? '') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Location / Description</label>
                <input type="text" name="location" value="{{ old('location', $warehouse->location ?? '') }}" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                    {{ isset($warehouse) ? 'Update Warehouse' : 'Save Warehouse' }}
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
