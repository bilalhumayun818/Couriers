<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Dashboard') — CX COURIER</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { font-family: 'Inter', sans-serif; box-sizing: border-box; }

/* ─── Sidebar ─── */
#sidebar {
  width: 248px;
  min-width: 248px;
  background: #f8fafc;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: width 0.25s ease, min-width 0.25s ease;
  flex-shrink: 0;
  position: relative;
}
#sidebar.collapsed {
  width: 60px;
  min-width: 60px;
}

/* hide text when collapsed */
#sidebar.collapsed .nav-text,
#sidebar.collapsed .brand-text,
#sidebar.collapsed .tenant-block,
#sidebar.collapsed .chevron,
#sidebar.collapsed .nav-version { display: none !important; }

#sidebar.collapsed .nav-link,
#sidebar.collapsed .nav-group-btn {
  justify-content: center;
  padding: 8px;
}
#sidebar.collapsed .nav-link { gap: 0; }
#sidebar.collapsed .nav-group-btn span { gap: 0; }
#sidebar.collapsed .sub-nav { display: none !important; }
#sidebar.collapsed .brand-logo { margin: 0 auto; }
#sidebar.collapsed .brand-wrap { justify-content: center; padding: 16px 0; }

/* ─── Nav links ─── */
.nav-link {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 12px; border-radius: 8px;
  color: #374151; font-size: 13.5px; font-weight: 500;
  text-decoration: none; transition: background 0.12s, color 0.12s;
  cursor: pointer; white-space: nowrap;
}
.nav-link:hover { background: #e0e7ff; color: #3730a3; }
.nav-link.active { background: #6366f1; color: #fff !important; font-weight: 600; }

.nav-group-btn {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%; padding: 8px 12px; border-radius: 8px;
  background: transparent; border: none;
  color: #111827; font-size: 13.5px; font-weight: 500;
  cursor: pointer; transition: background 0.12s;
  white-space: nowrap;
}
.nav-group-btn:hover { background: #f1f5f9; }
.nav-group-btn.open { color: #4338ca; }

/* sub-menu */
.sub-nav { max-height: 0; overflow: hidden; transition: max-height 0.25s ease; }
.sub-nav.open { max-height: 600px; }
.sub-nav .nav-link { font-size: 13px; color: #4b5563; padding: 7px 10px 7px 12px; }
.sub-nav .nav-link:hover { background: #e0e7ff; color: #3730a3; }
.sub-nav .nav-link.active { background: #6366f1; color: #fff; }

/* chevron */
.chevron { transition: transform 0.2s; flex-shrink: 0; color: #9ca3af; }
.nav-group-btn.open .chevron { transform: rotate(180deg); }

/* ─── Shared UI ─── */
.card { background:#fff; border-radius:12px; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,.06); }
.btn-primary { display:inline-flex; align-items:center; gap:6px; background:#6366f1; color:#fff; border:none; font-size:13px; font-weight:600; padding:8px 16px; border-radius:8px; cursor:pointer; transition:background 0.12s; }
.btn-primary:hover { background:#4f46e5; }
.btn-ghost { display:inline-flex; align-items:center; gap:6px; background:transparent; color:#374151; border:1px solid #d1d5db; font-size:13px; font-weight:500; padding:7px 14px; border-radius:8px; cursor:pointer; transition:background 0.12s; }
.btn-ghost:hover { background:#f9fafb; }
.table-header { background:#f8fafc; }
.table-header th { color:#6b7280; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
.table-row { border-bottom:1px solid #f1f5f9; transition:background 0.1s; }
.table-row:hover { background:#f8fafc; }

input[type=text], input[type=number], input[type=email],
input[type=date], input[type=password], select, textarea {
  border:1px solid #d1d5db; border-radius:8px; padding:8px 12px;
  font-size:13px; color:#111827; background:#fff; outline:none;
  transition:border .15s, box-shadow .15s; width:100%;
}
input:focus, select:focus, textarea:focus {
  border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12);
}

::-webkit-scrollbar { width:4px; height:4px; }
::-webkit-scrollbar-track { background:#f1f5f9; }
::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:4px; }
</style>
</head>
<body style="background:#f1f5f9;margin:0;">

<div style="display:flex;height:100vh;overflow:hidden;">

{{-- ════════════════ SIDEBAR ════════════════ --}}
<aside id="sidebar">

  {{-- Brand --}}
  <div class="brand-wrap" style="padding:18px 14px 12px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:10px;">
    <div class="brand-logo" style="width:34px;height:34px;background:#6366f1;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
      </svg>
    </div>
    <div class="brand-text">
      <div style="font-weight:800;font-size:14px;color:#111827;letter-spacing:.03em;">CX COURIER</div>
      <div style="font-size:10.5px;color:#6b7280;margin-top:1px;">Fleet &amp; Courier ERP</div>
    </div>
  </div>

  {{-- Nav --}}
  <nav style="flex:1;padding:10px 8px;overflow-y:auto;">

    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="margin-bottom:3px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      <span class="nav-text">Dashboard</span>
    </a>

    {{-- Fleet Management --}}
    <div id="grp-fleet" style="margin-top:3px;">
      <button onclick="toggleGroup('grp-fleet')" class="nav-group-btn">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
          </svg>
          <span class="nav-text">Fleet Management</span>
        </span>
        <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="sub-nav" style="padding-left:6px;margin-top:2px;">
        <a href="{{ route('fleet.vans') }}" class="nav-link {{ request()->routeIs('fleet.vans') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          <span class="nav-text">Vehicles (Vans)</span>
        </a>
        <a href="{{ route('fleet.fixed-costs') }}" class="nav-link {{ request()->routeIs('fleet.fixed-costs') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          <span class="nav-text">Fixed Costs</span>
        </a>
        <a href="{{ route('fleet.assignments') }}" class="nav-link {{ request()->routeIs('fleet.assignments') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span class="nav-text">Driver Assignments</span>
        </a>
      </div>
    </div>

    {{-- Daily Operations --}}
    <div id="grp-ops" style="margin-top:3px;">
      <button onclick="toggleGroup('grp-ops')" class="nav-group-btn">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 12h6M9 16h4"/></svg>
          <span class="nav-text">Daily Operations</span>
        </span>
        <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav" style="padding-left:6px;margin-top:2px;">
        <a href="{{ route('operations.trips') }}" class="nav-link {{ request()->routeIs('operations.trips') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
          <span class="nav-text">Trip Entries</span>
        </a>
        <a href="{{ route('operations.expenses') }}" class="nav-link {{ request()->routeIs('operations.expenses') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span class="nav-text">Daily Expenses</span>
        </a>
        <a href="{{ route('operations.wages') }}" class="nav-link {{ request()->routeIs('operations.wages') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span class="nav-text">Advances &amp; Wages</span>
        </a>
      </div>
    </div>

    {{-- Stakeholders --}}
    <div id="grp-crm" style="margin-top:3px;">
      <button onclick="toggleGroup('grp-crm')" class="nav-group-btn">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <span class="nav-text">Stakeholders &amp; CRM</span>
        </span>
        <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav" style="padding-left:6px;margin-top:2px;">
        <a href="{{ route('crm.customers') }}" class="nav-link {{ request()->routeIs('crm.customers') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <span class="nav-text">Customers</span>
        </a>
        <a href="{{ route('crm.drivers') }}" class="nav-link {{ request()->routeIs('crm.drivers') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
          <span class="nav-text">Drivers</span>
        </a>
        <a href="{{ route('crm.investors') }}" class="nav-link {{ request()->routeIs('crm.investors') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span class="nav-text">Investors &amp; Directors</span>
        </a>
      </div>
    </div>

    {{-- Ledgers --}}
    <div id="grp-ledger" style="margin-top:3px;">
      <button onclick="toggleGroup('grp-ledger')" class="nav-group-btn">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          <span class="nav-text">Ledgers &amp; Reports</span>
        </span>
        <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav" style="padding-left:6px;margin-top:2px;">
        <a href="{{ route('ledger.van') }}" class="nav-link {{ request()->routeIs('ledger.van') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          <span class="nav-text">Van-Wise Ledger</span>
        </a>
        <a href="{{ route('ledger.customer') }}" class="nav-link {{ request()->routeIs('ledger.customer') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <span class="nav-text">Customer Ledger</span>
        </a>
        <a href="{{ route('ledger.trial-balance') }}" class="nav-link {{ request()->routeIs('ledger.trial-balance') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
          <span class="nav-text">Trial Balance</span>
        </a>
        <a href="{{ route('ledger.profit-loss') }}" class="nav-link {{ request()->routeIs('ledger.profit-loss') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
          <span class="nav-text">Profit &amp; Loss</span>
        </a>
        <a href="{{ route('ledger.balance-sheet') }}" class="nav-link {{ request()->routeIs('ledger.balance-sheet') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
          <span class="nav-text">Balance Sheet</span>
        </a>
      </div>
    </div>

    {{-- Settings --}}
    <div id="grp-settings" style="margin-top:3px;">
      <button onclick="toggleGroup('grp-settings')" class="nav-group-btn">
        <span style="display:flex;align-items:center;gap:10px;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
          <span class="nav-text">System Settings</span>
        </span>
        <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav" style="padding-left:6px;margin-top:2px;">
        <a href="{{ route('settings.users') }}" class="nav-link {{ request()->routeIs('settings.users') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          <span class="nav-text">Users &amp; Roles</span>
        </a>
        <a href="{{ route('settings.tenant') }}" class="nav-link {{ request()->routeIs('settings.tenant') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <span class="nav-text">Currency &amp; Tax</span>
        </a>
      </div>
    </div>

  </nav>

  {{-- ── Tenant info at bottom ── --}}
  <div class="tenant-block" style="padding:12px 14px;border-top:1px solid #e2e8f0;background:#f8fafc;">
    <div style="background:#eff6ff;border:1px solid #dbeafe;border-radius:8px;padding:9px 11px;">
      <div style="font-size:10px;color:#6b7280;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">Tenant</div>
      <div style="font-size:12.5px;font-weight:700;color:#1e40af;margin-top:2px;">Acme Logistics Ltd</div>
      <div style="font-size:11px;color:#6366f1;margin-top:2px;font-weight:600;">● Admin</div>
    </div>
    <p class="nav-version" style="font-size:10.5px;color:#9ca3af;margin:8px 0 0;text-align:center;">CX COURIER &bull; v1.0</p>
  </div>

</aside>

{{-- ════════════════ MAIN ════════════════ --}}
<div style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

  {{-- Top bar --}}
  <header style="background:#fff;border-bottom:1px solid #e5e7eb;padding:10px 20px;display:flex;align-items:center;gap:12px;flex-shrink:0;">

    {{-- Sidebar toggle button --}}
    <button onclick="toggleSidebar()" id="sidebarToggleBtn"
      style="width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:#f8fafc;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:background .13s;"
      title="Toggle Sidebar">
      <svg id="toggleIconOpen" width="18" height="18" fill="none" stroke="#374151" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
      <svg id="toggleIconClose" width="18" height="18" fill="none" stroke="#374151" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

    <div style="flex:1;min-width:0;">
      <h1 style="font-size:15px;font-weight:700;color:#111827;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@yield('page-title','Dashboard')</h1>
      <p style="font-size:11.5px;color:#6b7280;margin:1px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@yield('page-subtitle','Fleet Management &amp; Courier Operations')</p>
    </div>

    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
      <span style="font-size:11.5px;color:#9ca3af;white-space:nowrap;">June 2025</span>
      <span style="display:inline-flex;align-items:center;gap:5px;background:#f0fdf4;color:#16a34a;font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;border:1px solid #bbf7d0;white-space:nowrap;">
        <span style="width:6px;height:6px;background:#22c55e;border-radius:50%;display:inline-block;"></span> Demo
      </span>
      <div style="width:32px;height:32px;background:#6366f1;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;">AD</div>
    </div>
  </header>

  {{-- Content --}}
  <main style="flex:1;overflow-y:auto;padding:22px;background:#f1f5f9;">
    @yield('content')
  </main>
</div>
</div>

@yield('modals')

<script>
// ── Sidebar collapse / expand ──
function toggleSidebar() {
  const sb   = document.getElementById('sidebar');
  const ico1 = document.getElementById('toggleIconOpen');
  const ico2 = document.getElementById('toggleIconClose');
  const collapsed = sb.classList.toggle('collapsed');
  ico1.style.display = collapsed ? 'none'  : 'block';
  ico2.style.display = collapsed ? 'block' : 'none';
}

// ── Nav group toggle ──
function toggleGroup(id) {
  const grp  = document.getElementById(id);
  const btn  = grp.querySelector('button');
  const menu = grp.querySelector('.sub-nav');
  const isOpen = menu.classList.contains('open');
  if (isOpen) {
    menu.classList.remove('open');
    btn.classList.remove('open');
  } else {
    menu.classList.add('open');
    btn.classList.add('open');
  }
}

// ── Auto-open active group on load ──
document.querySelectorAll('[id^="grp-"]').forEach(grp => {
  const menu = grp.querySelector('.sub-nav');
  const btn  = grp.querySelector('button');
  if (menu && menu.querySelector('.active')) {
    menu.classList.add('open');
    if (btn) btn.classList.add('open');
  }
});
</script>
@yield('scripts')
</body>
</html>
