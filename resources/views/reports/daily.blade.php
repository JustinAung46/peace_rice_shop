@extends('layouts.app')

@section('content')

{{-- ─── Page-scoped Styles ─── --}}
<style>
  /* ── Light Premium Dashboard ── */
  .daily-dash {
    --accent: #6366f1;
    --accent-light: #eef2ff;
    --accent-glow: rgba(99,102,241,0.18);
    --emerald: #059669;
    --emerald-bg: #ecfdf5;
    --rose: #e11d48;
    --rose-bg: #fff1f2;
    --amber: #d97706;
    --amber-bg: #fffbeb;
    --violet: #7c3aed;
    --violet-bg: #f5f3ff;
    --surface: #ffffff;
    --surface-soft: #f8fafc;
    --border: #e2e8f0;
    --border-light: #f1f5f9;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #94a3b8;
    padding: 0;
  }

  /* ── Header ── */
  .dash-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.75rem;
    flex-wrap: wrap;
    gap: 1rem;
  }
  .dash-header .title-group {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .dash-header .back-btn {
    width: 2.5rem; height: 2.5rem;
    display: flex; align-items: center; justify-content: center;
    border-radius: 0.75rem;
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text-muted);
    transition: all 0.2s;
    text-decoration: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }
  .dash-header .back-btn:hover {
    background: var(--surface-soft);
    color: var(--text-secondary);
    transform: translateX(-2px);
  }
  .dash-header h1 {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -0.02em;
  }
  .dash-header h1 span {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .dash-header .date-badge {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    background: var(--surface);
    border: 1px solid var(--border);
    padding: 0.4rem 1rem;
    border-radius: 999px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }

  /* ── Filter Card ── */
  .glass-filter {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.03);
  }
  .filter-top {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
  }
  .filter-top .filter-icon {
    width: 2rem; height: 2rem;
    border-radius: 0.5rem;
    background: var(--accent-light);
    display: flex; align-items: center; justify-content: center;
  }
  .filter-top span {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
  }
  .filter-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 1rem;
  }
  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    flex: 1;
    min-width: 160px;
  }
  .filter-group label {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
  }
  .date-input-wrap {
    position: relative;
  }
  .date-input-wrap svg {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    pointer-events: none;
  }
  .date-input-wrap input[type="date"] {
    width: 100%;
    padding: 0.6rem 0.75rem 0.6rem 2.25rem;
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    font-size: 0.85rem;
    color: var(--text-primary);
    background: var(--surface);
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    appearance: none;
    -webkit-appearance: none;
    font-family: 'Inter', sans-serif;
  }
  .date-input-wrap input[type="date"]:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-glow);
  }
  .filter-arrow {
    color: var(--border);
    font-size: 1.25rem;
    padding-bottom: 0.4rem;
    flex-shrink: 0;
  }
  .filter-btns {
    display: flex;
    align-items: flex-end;
    gap: 0.5rem;
    flex-shrink: 0;
  }
  .btn-apply {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 700;
    padding: 0.6rem 1.25rem;
    border-radius: 0.75rem;
    border: none;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 14px var(--accent-glow);
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
  }
  .btn-apply:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(99,102,241,0.35);
  }
  .btn-apply:active { transform: scale(0.97); }
  .btn-clear {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--surface-soft);
    color: var(--text-secondary);
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.6rem 1rem;
    border-radius: 0.75rem;
    border: 1.5px solid var(--border);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    white-space: nowrap;
  }
  .btn-clear:hover { background: var(--border-light); }

  /* Quick preset chips */
  .preset-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
  }
  .preset-label {
    font-size: 0.62rem;
    font-weight: 700;
    color: var(--text-muted);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    align-self: center;
    margin-right: 0.25rem;
  }
  .preset-btn {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    border: 1.5px solid var(--border);
    background: var(--surface);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
    font-family: 'Inter', sans-serif;
  }
  .preset-btn:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--accent-light);
  }

  /* ── Stat Cards ── */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.75rem;
  }
  @media (max-width: 768px) {
    .stat-grid { grid-template-columns: 1fr; }
  }
  .glass-stat {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: cardSlideUp 0.6s ease both;
    box-shadow: 0 4px 24px rgba(0,0,0,0.03);
  }
  .glass-stat:nth-child(2) { animation-delay: 0.1s; }
  .glass-stat:nth-child(3) { animation-delay: 0.2s; }
  .glass-stat:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.08);
  }
  .glass-stat .accent-stripe {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 1.25rem 1.25rem 0 0;
  }
  .glass-stat .stat-icon {
    width: 3rem; height: 3rem;
    border-radius: 0.875rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.25rem;
  }
  .glass-stat .stat-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 0.5rem;
  }
  .glass-stat .stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
    margin-bottom: 0.5rem;
  }
  .glass-stat .stat-value .unit {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-muted);
  }
  .glass-stat .stat-sub {
    font-size: 0.7rem;
    color: var(--text-muted);
  }

  @keyframes cardSlideUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* Card color accents */
  .stat-cash .accent-stripe   { background: linear-gradient(90deg, #34d399, #059669); }
  .stat-cash .stat-icon       { background: #ecfdf5; }
  .stat-cash .stat-value      { color: #065f46; }

  .stat-kpay .accent-stripe   { background: linear-gradient(90deg, #818cf8, #6366f1); }
  .stat-kpay .stat-icon       { background: #eef2ff; }
  .stat-kpay .stat-value      { color: #3730a3; }

  .stat-total .accent-stripe  { background: linear-gradient(90deg, #6366f1, #8b5cf6, #a855f7); }
  .stat-total                 { background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border-color: #c7d2fe; }
  .stat-total .stat-icon      { background: rgba(99,102,241,0.12); }
  .stat-total .stat-value     { background: linear-gradient(135deg, #4f46e5, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

  /* ── Chart Section ── */
  .chart-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.5rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.03);
    animation: cardSlideUp 0.6s 0.3s ease both;
  }
  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 0.75rem;
  }
  .chart-header h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .chart-toggle-group {
    display: flex;
    gap: 0.25rem;
    background: var(--surface-soft);
    border: 1px solid var(--border);
    border-radius: 0.6rem;
    padding: 0.2rem;
  }
  .chart-toggle {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.35rem 0.85rem;
    border-radius: 0.45rem;
    border: none;
    background: transparent;
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.2s;
    font-family: 'Inter', sans-serif;
  }
  .chart-toggle.active {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 2px 8px var(--accent-glow);
  }
  .chart-toggle:hover:not(.active) {
    color: var(--text-secondary);
    background: rgba(0,0,0,0.02);
  }
  .chart-canvas-wrap {
    position: relative;
    height: 280px;
  }

  /* ── Data Table ── */
  .table-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.03);
    animation: cardSlideUp 0.6s 0.4s ease both;
  }
  .table-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-light);
  }
  .table-header-bar h3 {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .table-header-bar .row-count {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--text-muted);
    background: var(--surface-soft);
    border: 1px solid var(--border);
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
  }
  .premium-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }
  .premium-table thead th {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 0.85rem 1.25rem;
    text-align: left;
    background: var(--surface-soft);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    white-space: nowrap;
  }
  .premium-table thead th.text-right { text-align: right; }
  .premium-table thead th.text-center { text-align: center; }
  .premium-table tbody tr {
    transition: background 0.15s;
  }
  .premium-table tbody tr:hover {
    background: #f8fafc;
  }
  .premium-table tbody td {
    padding: 0.85rem 1.25rem;
    font-size: 0.825rem;
    color: var(--text-secondary);
    border-bottom: 1px solid var(--border-light);
    white-space: nowrap;
  }
  .premium-table tbody td.text-right { text-align: right; }
  .premium-table tbody td.text-center { text-align: center; }
  .premium-table tbody td a {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
  }
  .premium-table tbody td a:hover {
    color: #4338ca;
    text-decoration: underline;
  }
  .premium-table tfoot td {
    padding: 1rem 1.25rem;
    font-size: 0.825rem;
    font-weight: 700;
    color: var(--text-primary);
    background: var(--surface-soft);
    border-top: 2px solid var(--border);
    white-space: nowrap;
  }
  .premium-table tfoot td.text-right { text-align: right; }
  .premium-table tfoot td.text-center { text-align: center; }

  /* Value colors */
  .v-cash   { color: var(--emerald) !important; }
  .v-kpay   { color: #4f46e5 !important; }
  .v-credit { color: var(--rose) !important; }
  .v-violet { color: var(--violet) !important; }
  .v-bold   { color: var(--text-primary) !important; font-weight: 700; }
  .v-up     { color: var(--emerald) !important; font-weight: 700; }
  .v-down   { color: var(--rose) !important; font-weight: 700; }
  .v-muted  { color: var(--text-muted) !important; }

  /* Margin pill */
  .margin-pill {
    display: inline-block;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    letter-spacing: 0.02em;
  }
  .mg-good { background: #ecfdf5; color: #059669; }
  .mg-mid  { background: #fffbeb; color: #d97706; }
  .mg-low  { background: #fff1f2; color: #e11d48; }

  /* ── Empty State ── */
  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
  }
  .empty-state svg { margin: 0 auto 1rem; opacity: 0.35; }
  .empty-state p { font-size: 0.85rem; }

  /* Scrollbar */
  .table-card .overflow-x-auto::-webkit-scrollbar { height: 6px; }
  .table-card .overflow-x-auto::-webkit-scrollbar-track { background: transparent; }
  .table-card .overflow-x-auto::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }
</style>

<div class="daily-dash">

  {{-- ── Header ── --}}
  <div class="dash-header">
    <div class="title-group">
      <a href="{{ route('reports.index') }}" class="back-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <h1>Daily <span>Sales</span> Report</h1>
    </div>
    <div class="date-badge">
      {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
    </div>
  </div>

  {{-- ── Filter ── --}}
  <div class="glass-filter">
    <div class="filter-top">
      <div class="filter-icon">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
      </div>
      <span>Filter by date range</span>
    </div>

    <form action="{{ route('reports.daily') }}" method="GET" id="filterForm">
      <div class="filter-row">
        <div class="filter-group">
          <label>Start Date</label>
          <div class="date-input-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}">
          </div>
        </div>

        <div class="filter-arrow">→</div>

        <div class="filter-group">
          <label>End Date</label>
          <div class="date-input-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}">
          </div>
        </div>

        <div class="filter-btns">
          <button type="submit" class="btn-apply">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
            Apply
          </button>
          <a href="{{ route('reports.daily') }}" class="btn-clear">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2A8.001 8.001 0 0019.419 15m0 0H15"/></svg>
            Reset
          </a>
        </div>
      </div>

      <div class="preset-row">
        <span class="preset-label">Quick:</span>
        <button type="button" class="preset-btn" onclick="setPreset(0,0)">Today</button>
        <button type="button" class="preset-btn" onclick="setPreset(1,1)">Yesterday</button>
        <button type="button" class="preset-btn" onclick="setPreset(6,0)">Last 7 Days</button>
        <button type="button" class="preset-btn" onclick="setPreset(29,0)">Last 30 Days</button>
        <button type="button" class="preset-btn" onclick="setThisMonth()">This Month</button>
        <button type="button" class="preset-btn" onclick="setLastMonth()">Last Month</button>
      </div>
    </form>
  </div>

  {{-- ── Stat Cards ── --}}
  <div class="stat-grid">

    {{-- Cash --}}
    <div class="glass-stat stat-cash">
      <div class="accent-stripe"></div>
      <div class="stat-icon">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 12h14l-1 7H6l-1-7z"/></svg>
      </div>
      <p class="stat-label">Cash Received</p>
      <p class="stat-value">
        <span class="counter" data-target="{{ $cashReceived }}">0</span> <span class="unit">Ks</span>
      </p>
      <p class="stat-sub">Cash sales + credit repayments</p>
    </div>

    {{-- KPay --}}
    <div class="glass-stat stat-kpay">
      <div class="accent-stripe"></div>
      <div class="stat-icon">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
      </div>
      <p class="stat-label">KPay Received</p>
      <p class="stat-value">
        <span class="counter" data-target="{{ $kpayReceived }}">0</span> <span class="unit">Ks</span>
      </p>
      <p class="stat-sub">KPay sales + credit repayments</p>
    </div>

    {{-- Total --}}
    <div class="glass-stat stat-total">
      <div class="accent-stripe"></div>
      <div class="stat-icon">
        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <p class="stat-label">Total Money Received</p>
      <p class="stat-value">
        <span class="counter" data-target="{{ $totalReceived }}">0</span> <span class="unit" style="-webkit-text-fill-color:#94a3b8;">Ks</span>
      </p>
      <p class="stat-sub">Combined cash & KPay in this period</p>
    </div>

  </div>

  {{-- ── Revenue Chart ── --}}
  @if(!$dailyStats->isEmpty())
  <div class="chart-section">
    <div class="chart-header">
      <h3>
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Revenue & Profit Trend
      </h3>
      <div class="chart-toggle-group">
        <button class="chart-toggle active" onclick="switchChart('revenue', this)">Revenue</button>
        <button class="chart-toggle" onclick="switchChart('profit', this)">Profit</button>
        <button class="chart-toggle" onclick="switchChart('money', this)">Money In</button>
      </div>
    </div>
    <div class="chart-canvas-wrap">
      <canvas id="dailyChart"></canvas>
    </div>
  </div>
  @endif

  {{-- ── Data Table ── --}}
  <div class="table-card">
    <div class="table-header-bar">
      <h3>
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
        Daily Breakdown
      </h3>
      <span class="row-count">{{ $dailyStats->count() }} {{ Str::plural('day', $dailyStats->count()) }}</span>
    </div>
    <div class="overflow-x-auto">
      <table class="premium-table">
        <thead>
          <tr>
            <th>Date</th>
            <th class="text-center">Txns</th>
            <th class="text-right">Revenue</th>
            <th class="text-right">Cash</th>
            <th class="text-right">KPay</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Collected</th>
            <th class="text-right">Money In</th>
            <th class="text-right">Cost</th>
            <th class="text-right">Profit</th>
            <th class="text-right">Margin</th>
          </tr>
        </thead>
        <tbody>
          @foreach($dailyStats as $stat)
          <tr>
            <td>
              <a href="{{ route('reports.items', ['start_date' => $stat->date, 'end_date' => $stat->date]) }}" title="View details for this day">
                {{ \Carbon\Carbon::parse($stat->date)->format('d M Y') }}
              </a>
            </td>
            <td class="text-center">{{ $stat->transaction_count }}</td>
            <td class="text-right v-bold">{{ number_format($stat->revenue) }} K</td>
            <td class="text-right v-cash">{{ number_format($stat->cash) }} K</td>
            <td class="text-right v-kpay">{{ number_format($stat->kpay) }} K</td>
            <td class="text-right v-credit">{{ number_format($stat->credit) }} K</td>
            <td class="text-right v-violet">{{ number_format($stat->credit_collected) }} K</td>
            <td class="text-right v-bold">{{ number_format($stat->money_received) }} K</td>
            <td class="text-right v-muted">{{ number_format($stat->total_cost) }} K</td>
            <td class="text-right {{ $stat->profit >= 0 ? 'v-up' : 'v-down' }}">
              {{ number_format($stat->profit) }} K
            </td>
            <td class="text-right">
              @php $margin = $stat->revenue > 0 ? ($stat->profit / $stat->revenue) * 100 : 0; @endphp
              <span class="margin-pill {{ $margin >= 20 ? 'mg-good' : ($margin >= 10 ? 'mg-mid' : 'mg-low') }}">
                {{ number_format($margin, 1) }}%
              </span>
            </td>
          </tr>
          @endforeach

          @if($dailyStats->isEmpty())
          <tr>
            <td colspan="11">
              <div class="empty-state">
                <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p>No data found for the selected period.</p>
              </div>
            </td>
          </tr>
          @endif
        </tbody>
        @if(!$dailyStats->isEmpty())
        <tfoot>
          <tr>
            <td>TOTAL</td>
            <td class="text-center">{{ $dailyStats->sum('transaction_count') }}</td>
            <td class="text-right">{{ number_format($dailyStats->sum('revenue')) }} K</td>
            <td class="text-right v-cash">{{ number_format($dailyStats->sum('cash')) }} K</td>
            <td class="text-right v-kpay">{{ number_format($dailyStats->sum('kpay')) }} K</td>
            <td class="text-right v-credit">{{ number_format($dailyStats->sum('credit')) }} K</td>
            <td class="text-right v-violet">{{ number_format($dailyStats->sum('credit_collected')) }} K</td>
            <td class="text-right">{{ number_format($dailyStats->sum('money_received')) }} K</td>
            <td class="text-right v-muted">{{ number_format($dailyStats->sum('total_cost')) }} K</td>
            <td class="text-right v-up">{{ number_format($dailyStats->sum('profit')) }} K</td>
            <td class="text-right">
              @php
                $totalRevenue = $dailyStats->sum('revenue');
                $totalProfit  = $dailyStats->sum('profit');
                $totalMargin  = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
              @endphp
              <span class="margin-pill {{ $totalMargin >= 20 ? 'mg-good' : ($totalMargin >= 10 ? 'mg-mid' : 'mg-low') }}">
                {{ number_format($totalMargin, 1) }}%
              </span>
            </td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>
  </div>

</div>

{{-- ─── Scripts ─── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
  /* ── Date presets ── */
  function fmtDate(d) { return d.toISOString().split('T')[0]; }
  function setPreset(daysBack, endDaysBack) {
    const today = new Date();
    const s = new Date(today); s.setDate(today.getDate() - daysBack);
    const e = new Date(today); e.setDate(today.getDate() - endDaysBack);
    document.getElementById('start_date').value = fmtDate(s);
    document.getElementById('end_date').value   = fmtDate(e);
    document.getElementById('filterForm').submit();
  }
  function setThisMonth() {
    const t = new Date();
    const s = new Date(t.getFullYear(), t.getMonth(), 1);
    document.getElementById('start_date').value = fmtDate(s);
    document.getElementById('end_date').value   = fmtDate(t);
    document.getElementById('filterForm').submit();
  }
  function setLastMonth() {
    const t = new Date();
    const s = new Date(t.getFullYear(), t.getMonth() - 1, 1);
    const e = new Date(t.getFullYear(), t.getMonth(), 0);
    document.getElementById('start_date').value = fmtDate(s);
    document.getElementById('end_date').value   = fmtDate(e);
    document.getElementById('filterForm').submit();
  }

  /* ── Animated counters ── */
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.counter').forEach((el, i) => {
      const target = parseInt(el.dataset.target) || 0;
      const duration = 2000;
      setTimeout(() => {
        const start = performance.now();
        const ease = t => 1 - Math.pow(1 - t, 4);
        const tick = now => {
          const p = Math.min((now - start) / duration, 1);
          el.textContent = Math.floor(ease(p) * target).toLocaleString();
          if (p < 1) requestAnimationFrame(tick);
          else el.textContent = target.toLocaleString();
        };
        requestAnimationFrame(tick);
      }, i * 150);
    });
  });

  /* ── Chart ── */
  @if(!$dailyStats->isEmpty())
  const chartData = {!! json_encode($dailyStats->sortBy('date')->values()->map(fn($s) => [
    'date'     => \Carbon\Carbon::parse($s->date)->format('d M'),
    'revenue'  => $s->revenue,
    'profit'   => $s->profit,
    'money'    => $s->money_received,
  ])) !!};

  const labels   = chartData.map(d => d.date);
  const datasets = {
    revenue: {
      label: 'Revenue',
      data: chartData.map(d => d.revenue),
      borderColor: '#6366f1',
      backgroundColor: 'rgba(99,102,241,0.08)',
      fill: true,
    },
    profit: {
      label: 'Profit',
      data: chartData.map(d => d.profit),
      borderColor: '#059669',
      backgroundColor: 'rgba(5,150,105,0.08)',
      fill: true,
    },
    money: {
      label: 'Money In',
      data: chartData.map(d => d.money),
      borderColor: '#d97706',
      backgroundColor: 'rgba(217,119,6,0.08)',
      fill: true,
    },
  };

  const ctx = document.getElementById('dailyChart').getContext('2d');
  let dailyChart = new Chart(ctx, {
    type: 'line',
    data: { labels, datasets: [{ ...datasets.revenue, tension: 0.4, pointRadius: 4, pointHoverRadius: 7, borderWidth: 2.5, pointBackgroundColor: '#fff', pointBorderWidth: 2 }] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#fff',
          titleColor: '#0f172a',
          bodyColor: '#475569',
          borderColor: '#e2e8f0',
          borderWidth: 1,
          cornerRadius: 10,
          padding: 12,
          titleFont: { weight: '700' },
          boxShadow: '0 4px 16px rgba(0,0,0,0.08)',
          callbacks: {
            label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()} K`
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } },
          border: { color: '#e2e8f0' }
        },
        y: {
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: {
            color: '#94a3b8',
            font: { size: 11, weight: '600' },
            callback: v => v.toLocaleString()
          },
          border: { display: false }
        }
      }
    }
  });

  function switchChart(key, btn) {
    document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const ds = datasets[key];
    dailyChart.data.datasets = [{ ...ds, tension: 0.4, pointRadius: 4, pointHoverRadius: 7, borderWidth: 2.5, pointBackgroundColor: '#fff', pointBorderWidth: 2 }];
    dailyChart.update();
  }
  @endif
</script>

@endsection
