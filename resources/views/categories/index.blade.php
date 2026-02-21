@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Category Management</h1>
        <p class="text-slate-500 text-sm mt-1">Organize your rice products easily.</p>
    </div>
    <a href="{{ route('categories.create') }}" class="w-full sm:w-auto flex justify-center items-center px-6 py-3.5 bg-indigo-600 text-white font-bold rounded-xl shadow-sm hover:bg-indigo-700 hover:shadow transition-all focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        New Category
    </a>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 flex items-center shadow-sm">
        <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($categories as $category)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col hover:shadow-lg transition-all group duration-300">
        <div class="mb-5 flex-1">
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-xl font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    {{ $category->name }}
                </h3>
            </div>
            <p class="text-sm text-slate-500 leading-relaxed min-h-[40px]">{{ $category->description ?? 'No description provided.' }}</p>
            
            <div class="mt-4 bg-slate-50 rounded-xl p-3 inline-flex items-center border border-slate-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                <span class="text-xl font-black text-slate-700 group-hover:text-indigo-700 mr-2">{{ $category->products_count ?? 0 }}</span>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide group-hover:text-indigo-500">Products linked</span>
            </div>
        </div>
        
        <div class="flex gap-3 mt-auto pt-4 border-t border-slate-100">
            <a href="{{ route('categories.edit', $category->id) }}" class="flex-1 text-center py-3 bg-slate-100 font-bold text-slate-700 rounded-xl hover:bg-slate-200 transition-colors focus:ring-2 focus:ring-slate-300">Edit</a>
            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this category?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-3 bg-red-50 font-bold text-red-600 rounded-xl hover:bg-red-100 transition-colors focus:ring-2 focus:ring-red-300">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full py-16 px-6 text-center bg-white rounded-2xl border-2 border-slate-200 border-dashed">
         <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">No categories found</h3>
        <p class="text-slate-500 text-lg mb-8 max-w-md mx-auto">Set up your first category to keep your products organized.</p>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center px-8 py-4 border border-transparent shadow-md text-lg font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform active:scale-95">
            <svg class="-ml-1 mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Category
        </a>
    </div>
    @endforelse
</div>
@endsection
