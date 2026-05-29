@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('credits.history', $customer) }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Audit Log</h1>
                <p class="text-slate-500 text-sm mt-0.5">Payment change history for <span class="font-semibold text-slate-700">{{ $customer->name }}</span></p>
            </div>
        </div>
        <a href="{{ route('credits.history', $customer) }}"
            class="text-sm bg-slate-100 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-200 transition-colors font-medium">
            ← Back to History
        </a>
    </div>

    {{-- Summary Cards --}}
    @php
        $totalCreated = $logs->getCollection()->where('action', 'created')->count();
        $totalEdited  = $logs->getCollection()->where('action', 'edited')->count();
        $totalDeleted = $logs->getCollection()->where('action', 'deleted')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Payments Created</p>
                <p class="text-2xl font-black text-emerald-600">{{ $totalCreated }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Payments Edited</p>
                <p class="text-2xl font-black text-indigo-600">{{ $totalEdited }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Payments Deleted</p>
                <p class="text-2xl font-black text-rose-600">{{ $totalDeleted }}</p>
            </div>
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                All Changes (latest first)
            </h2>
            <span class="text-sm text-slate-400">{{ $logs->total() }} record(s)</span>
        </div>

        @if($logs->isEmpty())
            <div class="text-center py-16 text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm">No audit logs found for this customer</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                        <th class="text-left px-5 py-3 font-semibold">Date &amp; Time</th>
                        <th class="text-center px-5 py-3 font-semibold">Action</th>
                        <th class="text-left px-5 py-3 font-semibold">Changed By</th>
                        <th class="text-right px-5 py-3 font-semibold">Old Amount</th>
                        <th class="text-right px-5 py-3 font-semibold">New Amount</th>
                        <th class="text-left px-5 py-3 font-semibold">Old Note</th>
                        <th class="text-left px-5 py-3 font-semibold">New Note</th>
                        <th class="text-left px-5 py-3 font-semibold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-slate-50 transition-colors
                        @if($log->action === 'deleted') bg-rose-50/30
                        @elseif($log->action === 'edited') bg-amber-50/20
                        @endif">

                        {{-- Date --}}
                        <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap text-xs">
                            {{ $log->created_at->format('d M Y') }}<br>
                            <span class="text-slate-400">{{ $log->created_at->format('h:i:s A') }}</span>
                        </td>

                        {{-- Action Badge --}}
                        <td class="px-5 py-3.5 text-center">
                            @if($log->action === 'created')
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-2.5 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Created
                                </span>
                            @elseif($log->action === 'edited')
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-indigo-700 bg-indigo-100 border border-indigo-200 px-2.5 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edited
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 bg-rose-100 border border-rose-200 px-2.5 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Deleted
                                </span>
                            @endif
                        </td>

                        {{-- Performer --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600 shrink-0">
                                    {{ strtoupper(substr($log->performer->name ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-slate-700 font-medium text-xs">{{ $log->performer->name ?? 'Unknown' }}</span>
                            </div>
                        </td>

                        {{-- Old Amount --}}
                        <td class="px-5 py-3.5 text-right">
                            @if($log->old_amount !== null)
                                <span class="font-semibold text-slate-600">{{ number_format($log->old_amount) }} Ks</span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- New Amount --}}
                        <td class="px-5 py-3.5 text-right">
                            @if($log->new_amount !== null)
                                <span class="font-bold
                                    @if($log->action === 'created') text-emerald-600
                                    @elseif($log->old_amount && $log->new_amount > $log->old_amount) text-emerald-600
                                    @elseif($log->old_amount && $log->new_amount < $log->old_amount) text-rose-600
                                    @else text-slate-700
                                    @endif">
                                    {{ number_format($log->new_amount) }} Ks
                                </span>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>

                        {{-- Old Note --}}
                        <td class="px-5 py-3.5 text-slate-500 text-xs max-w-[120px] truncate">
                            {{ $log->old_note ?: '—' }}
                        </td>

                        {{-- New Note --}}
                        <td class="px-5 py-3.5 text-slate-500 text-xs max-w-[120px] truncate">
                            {{ $log->new_note ?: '—' }}
                        </td>

                        {{-- IP Address --}}
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded">
                                {{ $log->ip_address ?? '—' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>

    {{-- Legend --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mb-3">Legend</p>
        <div class="flex flex-wrap gap-4 text-xs text-slate-600">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
                <span><strong>Created</strong> — A new payment was recorded</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span>
                <span><strong>Edited</strong> — Amount or note was corrected by an admin</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
                <span><strong>Deleted</strong> — Payment was removed and balance restored</span>
            </div>
        </div>
    </div>

</div>
@endsection
