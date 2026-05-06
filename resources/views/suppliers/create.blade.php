@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Add Supplier</h1>
        <p class="text-slate-500 text-sm mt-1">Fill in the supplier details below.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Supplier Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror"
                       placeholder="e.g. Shwe Hmaw Supplier">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. 09-XXXX-XXXXX">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                <textarea name="address" rows="2"
                          class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Supplier address...">{{ old('address') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notes</label>
                <textarea name="notes" rows="2"
                          class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Any extra notes...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    Save Supplier
                </button>
                <a href="{{ route('suppliers.index') }}"
                   class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
