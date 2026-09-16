<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title','Dashboard') — CX COURIER</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; box-sizing: border-box; }

body {
  background-color: #040711;
  background-image:
    linear-gradient(180deg, rgba(4,7,17,0.18) 0%, rgba(4,7,17,0.28) 100%),
    url('{{ asset('images/van_hero_bg.png') }}');
  background-size: cover;
  background-position: center center;
  background-attachment: fixed;
  color: #f8fafc;
  margin: 0;
  overflow: hidden;
}

/* ─── Glass Panels ─── */
.glass-panel {
  background: rgba(8, 14, 30, 0.28);
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 8px 32px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.12);
  border-radius: 12px;
}

.glass-card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px) saturate(160%);
  -webkit-backdrop-filter: blur(10px) saturate(160%);
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.14), 0 4px 20px rgba(0,0,0,0.2);
  border-radius: 10px;
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}
.glass-card:hover {
  background: rgba(255, 255, 255, 0.10);
  border-color: rgba(56, 189, 248, 0.45);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.2), 0 8px 30px rgba(56,189,248,0.18);
}

.glass-pill {
  background: rgba(8, 14, 30, 0.25);
  backdrop-filter: blur(14px) saturate(180%);
  -webkit-backdrop-filter: blur(14px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.22);
  box-shadow: 0 8px 28px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.25);
  border-radius: 9999px;
}

.glass-input {
  background: rgba(8, 14, 30, 0.35) !important;
  border: 1px solid rgba(255, 255, 255, 0.2) !important;
  color: #f8fafc !important;
  backdrop-filter: blur(10px);
  border-radius: 8px;
}
.glass-input:focus {
  border-color: #38bdf8 !important;
  box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
  outline: none !important;
}
.glass-input option { background: #0f172a; color: #f8fafc; }

/* ─── Sidebar ─── */
#sidebar {
  width: 252px;
  min-width: 252px;
  background: rgba(4, 7, 17, 0.32);
  backdrop-filter: blur(14px) saturate(180%);
  -webkit-backdrop-filter: blur(14px) saturate(180%);
  border-right: 1px solid rgba(255, 255, 255, 0.12);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: width 0.25s ease, min-width 0.25s ease;
  flex-shrink: 0;
  position: relative;
  z-index: 40;
}
#sidebar.collapsed { width: 68px; min-width: 68px; }

/* hide text when collapsed */
#sidebar.collapsed .nav-text,
#sidebar.collapsed .brand-text,
#sidebar.collapsed .tenant-block,
#sidebar.collapsed .chevron,
#sidebar.collapsed .nav-version { display: none !important; }
#sidebar.collapsed .nav-link,
#sidebar.collapsed .nav-group-btn { justify-content: center; padding: 10px; }
#sidebar.collapsed .nav-link { gap: 0; }
#sidebar.collapsed .nav-group-btn span { gap: 0; }
#sidebar.collapsed .sub-nav { display: none !important; }
#sidebar.collapsed .brand-logo { margin: 0 auto; }
#sidebar.collapsed .brand-wrap { justify-content: center; padding: 16px 0; }

/* ─── Nav links ─── */
.nav-link {
  display: flex; align-items: center; gap: 11px;
  padding: 8px 12px; border-radius: 8px;
  color: #94a3b8; font-size: 13px; font-weight: 500;
  text-decoration: none; transition: background 0.15s, color 0.15s;
  cursor: pointer; white-space: nowrap;
}
.nav-link:hover { background: rgba(255,255,255,0.08); color: #e2e8f0; }
.nav-link.active {
  background: linear-gradient(135deg, rgba(56,189,248,0.22) 0%, rgba(99,102,241,0.28) 100%);
  border: 1px solid rgba(56,189,248,0.4);
  color: #38bdf8 !important;
  font-weight: 600;
  box-shadow: 0 3px 14px rgba(56,189,248,0.18);
}

.nav-group-btn {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%; padding: 8px 12px; border-radius: 8px;
  background: transparent; border: none;
  color: #cbd5e1; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: background 0.15s, color 0.15s;
  white-space: nowrap;
}
.nav-group-btn:hover { background: rgba(255,255,255,0.07); color: #f8fafc; }
.nav-group-btn.open { color: #38bdf8; }

/* sub-menu */
.sub-nav { max-height: 0; overflow: hidden; transition: max-height 0.28s ease; }
.sub-nav.open { max-height: 600px; }
.sub-nav .nav-link { font-size: 12.5px; color: #94a3b8; padding: 7px 10px 7px 14px; }
.sub-nav .nav-link:hover { background: rgba(255,255,255,0.07); color: #e2e8f0; }
.sub-nav .nav-link.active { background: rgba(56,189,248,0.18); color: #38bdf8; border: none; }

/* chevron */
.chevron { transition: transform 0.2s; flex-shrink: 0; color: #64748b; }
.nav-group-btn.open .chevron { transform: rotate(180deg); color: #38bdf8; }

/* ─── Tables ─── */
.table-header { background: rgba(15,23,42,0.45); }
.table-header th { color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
.table-row { border-bottom: 1px solid rgba(255,255,255,0.06); transition: background 0.15s; }
.table-row:hover { background: rgba(255,255,255,0.08); }

/* ─── Shared Cards / Buttons used in inner pages ─── */
.card {
  background: rgba(8,14,30,0.28);
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  border: 1px solid rgba(255,255,255,0.12);
  box-shadow: 0 6px 24px rgba(0,0,0,0.3);
  border-radius: 10px;
}

.btn-primary {
  display: inline-flex; align-items: center; gap: 7px;
  background: linear-gradient(135deg, #0284c7, #6366f1);
  color: #fff; font-size: 13px; font-weight: 600;
  padding: 8px 16px; border-radius: 8px; border: none;
  cursor: pointer; transition: opacity 0.15s, transform 0.1s;
  box-shadow: 0 4px 14px rgba(2,132,199,0.3);
  text-decoration: none;
}
.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

.btn-ghost {
  display: inline-flex; align-items: center; gap: 7px;
  background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.14);
  color: #cbd5e1; font-size: 13px; font-weight: 500;
  padding: 8px 16px; border-radius: 8px; cursor: pointer;
  transition: background 0.15s; text-decoration: none;
}
.btn-ghost:hover { background: rgba(255,255,255,0.1); color: #f8fafc; }

/* Input/select base styles */
input[type="text"], input[type="number"], input[type="date"],
input[type="email"], input[type="tel"], select, textarea {
  background: rgba(8,14,30,0.5) !important;
  border: 1px solid rgba(255,255,255,0.15) !important;
  color: #f8fafc !important;
  border-radius: 8px !important;
  padding: 8px 12px;
  font-size: 13px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  width: 100%;
  transition: border-color 0.15s, box-shadow 0.15s;
}
input:focus, select:focus, textarea:focus {
  border-color: #38bdf8 !important;
  box-shadow: 0 0 0 3px rgba(56,189,248,0.18) !important;
  outline: none !important;
}
input::placeholder, textarea::placeholder { color: #64748b !important; }
select option { background: #0f172a; color: #f8fafc; }
label { color: #94a3b8 !important; }

/* ─── Scrollbars ─── */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: rgba(15,23,42,0.3); }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: rgba(56,189,248,0.45); }

/* ─── Status Pills / Badges ─── */
.badge-green { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.35); color: #34d399; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-amber { background: rgba(56,189,248,0.15); border: 1px solid rgba(56,189,248,0.35); color: #38bdf8; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-blue  { background: rgba(56,189,248,0.15); border: 1px solid rgba(56,189,248,0.35); color: #38bdf8; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-red   { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.35); color: #f87171; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-slate { background: rgba(100,116,139,0.15); border: 1px solid rgba(100,116,139,0.35); color: #94a3b8; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }

/* Modal overlay */
.modal-overlay {
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(4px);
}
.modal-box {
  background: rgba(8,14,30,0.92);
  backdrop-filter: blur(24px) saturate(180%);
  -webkit-backdrop-filter: blur(24px) saturate(180%);
  border: 1px solid rgba(255,255,255,0.14);
  box-shadow: 0 24px 60px rgba(0,0,0,0.6);
  border-radius: 12px;
}
</style>
</head>
<body style="margin:0;">

@if(request()->is('demo*') || (isset($isDemoMode) && $isDemoMode))
{{-- Demo mode top bar banner --}}
<div style="background: linear-gradient(90deg, rgba(8,14,30,0.85) 0%, rgba(56,189,248,0.2) 50%, rgba(8,14,30,0.85) 100%); border-bottom: 1px solid rgba(56,189,248,0.3); padding: 8px 20px; font-size: 12.5px; color: #cbd5e1; display: flex; align-items: center; justify-content: space-between; backdrop-filter: blur(10px); z-index: 100; position: relative;">
  <div style="display:flex;align-items:center;gap:10px;">
    <span style="background: rgba(56,189,248,0.2); color: #38bdf8; border: 1px solid rgba(56,189,248,0.4); border-radius: 9999px; padding: 2px 10px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">🎯 Demo Mode Active</span>
    <span>Each browser session is strictly isolated with up to 10 records per entity.</span>
  </div>
  <div style="font-size:11.5px;color:#94a3b8;">
    Quota: <span style="color:#34d399;font-weight:700;">10 Max Per Entity</span>
  </div>
</div>

@if(session('demo_limit_error'))
{{-- Limit error toast --}}
<div id="demoLimitToast" style="position: fixed; top: 24px; right: 24px; z-index: 99999; max-width: 420px; background: rgba(8, 14, 30, 0.95); border: 1px solid rgba(248, 113, 113, 0.4); border-radius: 12px; padding: 16px 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.6), 0 0 20px rgba(248, 113, 113, 0.2); backdrop-filter: blur(16px); color: #f8fafc; display: flex; align-items: flex-start; gap: 14px;">
  <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(248, 113, 113, 0.15); border: 1px solid rgba(248, 113, 113, 0.3); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #f87171;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
  </div>
  <div style="flex: 1;">
    <div style="font-weight: 700; font-size: 14px; color: #f87171; margin-bottom: 3px;">Demo Limit Reached</div>
    <div style="font-size: 12.5px; color: #cbd5e1; line-height: 1.4;">{{ session('demo_limit_error') }}</div>
  </div>
  <button onclick="document.getElementById('demoLimitToast').remove()" style="background: none; border: none; color: #64748b; cursor: pointer; padding: 2px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>
  </button>
</div>
@endif
@endif

<div style="display:flex;height:100vh;overflow:hidden;position:relative;">

{{-- ════════════════ SIDEBAR ════════════════ --}}
<aside id="sidebar">

  {{-- Brand --}}
  <div class="brand-wrap" style="padding:18px 16px 14px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;gap:12px;">
    <div class="brand-logo" style="width:36px;height:36px;background:linear-gradient(135deg,#38bdf8,#6366f1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 0 18px rgba(56,189,248,0.4);">
      <svg width="19" height="19" fill="none" stroke="#fff" stroke-width="2.2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM3 5h11l4 7v5h-2m-13 0h.01M3 5v8h15"/>
      </svg>
    </div>
    <div class="brand-text">
      <div style="font-weight:800;font-size:14.5px;color:#f8fafc;letter-spacing:.04em;">CX COURIER</div>
      <div style="font-size:10px;color:#38bdf8;font-weight:600;margin-top:1px;letter-spacing:.06em;text-transform:uppercase;">Fleet Royale</div>
    </div>
  </div>

  {{-- Nav --}}
  <nav style="flex:1;padding:10px 8px;overflow-y:auto;">

    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="margin-bottom:3px;">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
        <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
        <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
      </svg>
      <span class="nav-text">Dashboard</span>
    </a>

    {{-- Fleet Management --}}
    <div id="grp-fleet" style="margin-top:3px;">
      <button onclick="toggleGroup('grp-fleet')" class="nav-group-btn open">
        <span style="display:flex;align-items:center;gap:11px;">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM3 5h11l4 7v5h-2m-13 0h.01M3 5v8h15"/>
          </svg>
          <span class="nav-text">Fleet Management</span>
        </span>
        <svg class="chevron" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div class="sub-nav open" style="padding-left:6px;margin-top:1px;">
        <a href="{{ route('fleet.vans') }}" class="nav-link {{ request()->routeIs('fleet.vans') || request()->routeIs('fleet.vans.model') ? 'active' : '' }}">
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
      <button onclick="toggleGroup('grp-ops')" class="nav-group-btn open">
        <span style="display:flex;align-items:center;gap:11px;">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 12h6M9 16h4"/></svg>
          <span class="nav-text">Daily Operations</span>
        </span>
        <svg class="chevron" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav open" style="padding-left:6px;margin-top:1px;">
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
      <button onclick="toggleGroup('grp-crm')" class="nav-group-btn open">
        <span style="display:flex;align-items:center;gap:11px;">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
          <span class="nav-text">Stakeholders &amp; CRM</span>
        </span>
        <svg class="chevron" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav open" style="padding-left:6px;margin-top:1px;">
        <a href="{{ route('crm.customers') }}" class="nav-link {{ request()->routeIs('crm.customers') || request()->routeIs('crm.customers.show') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
          <span class="nav-text">Customers</span>
        </a>
        <a href="{{ route('crm.drivers') }}" class="nav-link {{ request()->routeIs('crm.drivers') || request()->routeIs('crm.drivers.show') ? 'active' : '' }}">
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
      <button onclick="toggleGroup('grp-ledger')" class="nav-group-btn open">
        <span style="display:flex;align-items:center;gap:11px;">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
          <span class="nav-text">Ledgers &amp; Reports</span>
        </span>
        <svg class="chevron" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav open" style="padding-left:6px;margin-top:1px;">
        <a href="{{ route('ledger.van') }}" class="nav-link {{ request()->routeIs('ledger.van') || request()->routeIs('ledger.van.detail') ? 'active' : '' }}">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          <span class="nav-text">Van-Wise Ledger</span>
        </a>
        <a href="{{ route('ledger.customer') }}" class="nav-link {{ request()->routeIs('ledger.customer') || request()->routeIs('ledger.customer.statement') ? 'active' : '' }}">
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
      <button onclick="toggleGroup('grp-settings')" class="nav-group-btn open">
        <span style="display:flex;align-items:center;gap:11px;">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
          <span class="nav-text">System Settings</span>
        </span>
        <svg class="chevron" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
      </button>
      <div class="sub-nav open" style="padding-left:6px;margin-top:1px;">
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

  {{-- Tenant block at bottom --}}
  <div class="tenant-block" style="padding:12px;border-top:1px solid rgba(255,255,255,0.08);background:rgba(4,7,17,0.4);">
    <div style="background:rgba(56,189,248,0.10);border:1px solid rgba(56,189,248,0.25);border-radius:8px;padding:9px 12px;">
      <div style="font-size:9.5px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">Active Tenant</div>
      <div style="font-size:12.5px;font-weight:700;color:#38bdf8;margin-top:2px;">Acme Express Logistics</div>
      <div style="font-size:10.5px;color:#34d399;margin-top:2px;font-weight:600;">● Admin Operations</div>
    </div>
    <p class="nav-version" style="font-size:10px;color:#334155;margin:8px 0 0;text-align:center;">CX COURIER &bull; v2.0 Glass</p>
  </div>

</aside>

{{-- ════════════════ MAIN ════════════════ --}}
<div style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;position:relative;z-index:10;">

  {{-- Top Header Bar --}}
  <header style="background:rgba(4,7,17,0.28);backdrop-filter:blur(14px) saturate(180%);-webkit-backdrop-filter:blur(14px) saturate(180%);border-bottom:1px solid rgba(255,255,255,0.1);padding:10px 22px;display:flex;align-items:center;gap:14px;flex-shrink:0;">

    {{-- Sidebar toggle --}}
    <button onclick="toggleSidebar()" id="sidebarToggleBtn"
      style="width:36px;height:36px;border-radius:8px;border:1px solid rgba(255,255,255,0.12);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:all .15s;color:#cbd5e1;"
      title="Toggle Sidebar">
      <svg id="toggleIconOpen" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
      <svg id="toggleIconClose" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>

    <div style="flex:1;min-width:0;">
      <h1 style="font-size:15px;font-weight:700;color:#f8fafc;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:-0.01em;">@yield('page-title','Dashboard')</h1>
      <p style="font-size:11px;color:#64748b;margin:1px 0 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@yield('page-subtitle','Fleet Management &amp; Courier Operations')</p>
    </div>

    {{-- Search --}}
    <div style="position:relative;width:220px;">
      <input type="text" placeholder="Search dispatch, vans..." class="glass-input" style="padding-left:34px;padding-right:12px;height:34px;font-size:12px;" />
      <svg width="14" height="14" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;left:11px;top:10px;">
        <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
      </svg>
    </div>

    {{-- Action controls --}}
    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
      <button style="width:34px;height:34px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;color:#94a3b8;transition:all .15s;">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span style="position:absolute;top:6px;right:6px;width:6px;height:6px;background:#38bdf8;border-radius:50%;box-shadow:0 0 6px #38bdf8;"></span>
      </button>
      <button style="width:34px;height:34px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#94a3b8;transition:all .15s;">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
      <div style="width:34px;height:34px;background:linear-gradient(135deg,#38bdf8,#818cf8);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:800;flex-shrink:0;box-shadow:0 0 12px rgba(56,189,248,0.35);">
        AD
      </div>
    </div>
  </header>

  {{-- Main Content --}}
  <main style="flex:1;overflow-y:auto;padding:20px;">
    @yield('content')
  </main>
</div>
</div>

@yield('modals')

<script>
// ── Sidebar toggle ──
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

// ── All groups open by default ──
document.querySelectorAll('[id^="grp-"]').forEach(grp => {
  const menu = grp.querySelector('.sub-nav');
  const btn  = grp.querySelector('button');
  if (menu) menu.classList.add('open');
  if (btn) btn.classList.add('open');
});
</script>
@yield('scripts')
</body>
</html>
