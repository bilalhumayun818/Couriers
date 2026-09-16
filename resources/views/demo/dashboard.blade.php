@extends('demo.layout')
@section('title','Fleet Royale Dashboard')
@section('page-title','Ocean Royale • Courier Fleet Dashboard')
@section('page-subtitle','AI-Powered Dispatch & Fleet Telematics Engine')

@section('content')

{{-- ════════════════ TOP GRID ════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

  {{-- ── LEFT MAIN WORKSPACE (8 COLUMNS) ── --}}
  <div class="lg:col-span-8 space-y-5">

    {{-- ── AI DISPATCH ANALYSIS CARD ── --}}
    <div class="glass-panel p-5 relative overflow-hidden group">
      {{-- Glow accent --}}
      <div style="position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(56,189,248,0.15) 0%,transparent 70%);pointer-events:none;"></div>

      {{-- Card Header --}}
      <div class="flex items-center justify-between mb-4">
        <div>
          <h2 class="text-sm font-bold text-slate-100 tracking-wide">AI Charter Analysis &bull; Express Dispatch</h2>
          <p class="text-xs text-slate-400 mt-0.5">Live telematics feed • Mercedes Sprinter EV</p>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;">
          <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span> LIVE DISPATCH
        </span>
      </div>

      {{-- Card Content Grid --}}
      <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">

        {{-- Van Image --}}
        <div class="md:col-span-6 relative">
          <div class="relative rounded-xl overflow-hidden border border-white/10 shadow-2xl bg-slate-900/70 group-hover:border-sky-400/40 transition-all duration-300">
            <img src="{{ asset('images/van_ai_card.png') }}" alt="AI Van Analysis" class="w-full h-48 object-cover object-center transform group-hover:scale-105 transition-transform duration-500" />
            <div class="absolute inset-0" style="background:linear-gradient(to top, rgba(4,7,17,0.85) 0%, transparent 60%);"></div>
            <div class="absolute bottom-3 left-3">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold" style="background:rgba(56,189,248,0.2);backdrop-filter:blur(10px);border:1px solid rgba(56,189,248,0.4);color:#38bdf8;">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                Mercedes Sprinter EV
              </span>
            </div>
          </div>
        </div>

        {{-- Telematics Specs --}}
        <div class="md:col-span-6 space-y-2.5 text-xs">
          <div class="flex justify-between py-2" style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <span class="text-slate-400 font-medium">Departure</span>
            <span class="text-slate-100 font-semibold">Monaco Marina (London Hub)</span>
          </div>
          <div class="flex justify-between py-2" style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <span class="text-slate-400 font-medium">Destination</span>
            <span class="text-slate-100 font-semibold">Ibiza Coast (Manchester)</span>
          </div>
          <div class="flex justify-between py-2" style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <span class="text-slate-400 font-medium">Duration</span>
            <span class="text-slate-100 font-semibold">6 Hours</span>
          </div>
          <div class="flex justify-between py-2" style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <span class="text-slate-400 font-medium">Distance</span>
            <span class="text-slate-100 font-semibold">185 Miles</span>
          </div>
          <div class="flex justify-between py-2">
            <span class="text-slate-400 font-medium">Battery &amp; Energy</span>
            <span class="text-sky-400 font-semibold">320 L (42 kWh)</span>
          </div>
        </div>
      </div>

      {{-- Bottom Glass Control Bar --}}
      <div class="mt-5 grid grid-cols-3 gap-3 glass-card p-3 px-5 items-center">
        <div class="flex items-center gap-3">
          <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sky-400" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </span>
          <div>
            <span class="block text-xs font-bold text-slate-100">Optimal</span>
            <span class="block text-[10px] text-slate-400">Road Condition</span>
          </div>
        </div>

        <div class="flex items-center justify-center gap-2.5">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sky-400" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
          </div>
          <div>
            <span class="block text-xs font-bold text-slate-100">12 Vans</span>
            <span class="block text-[10px] text-sky-400 font-bold uppercase tracking-wider">Active Fleet</span>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3">
          <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sky-400" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </span>
          <div>
            <span class="block text-xs font-bold text-slate-100">98%</span>
            <span class="block text-[10px] text-slate-400">AI Efficiency</span>
          </div>
        </div>
      </div>
    </div>

    {{-- ── BOTTOM ROW: 2 CHART CARDS ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

      {{-- Booking Scatter Matrix --}}
      <div class="glass-panel p-4 flex flex-col">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-bold text-slate-100 text-xs tracking-wide">Booking Distribution</h3>
          <select class="glass-input text-[11px] px-2 py-1" style="width:auto;padding:4px 8px;">
            <option>This Month</option>
            <option>Last Month</option>
          </select>
        </div>
        <div class="flex items-center gap-3 text-[11px] mb-3 text-slate-400">
          <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-400"></span><span><b class="text-slate-200">64%</b> Charters</span></div>
          <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-400"></span><span><b class="text-slate-200">22%</b> Express</span></div>
          <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-slate-500"></span><span><b class="text-slate-200">14%</b> Direct</span></div>
        </div>
        <div class="relative flex-1" style="height:160px;">
          <canvas id="matrixScatterChart" class="w-full h-full"></canvas>
        </div>
      </div>

      {{-- Analytics Waveform --}}
      <div class="glass-panel p-4 flex flex-col">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-bold text-slate-100 text-xs tracking-wide">Booking Analytics</h3>
          <select class="glass-input text-[11px] px-2 py-1" style="width:auto;padding:4px 8px;">
            <option>This Year</option>
            <option>All Time</option>
          </select>
        </div>
        <div class="flex justify-between items-center text-[10px] text-slate-400 px-1 mb-2">
          <span class="px-2 py-0.5 rounded font-mono" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">118 <span class="text-slate-400">±0%</span></span>
          <span class="px-2 py-0.5 rounded font-mono" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.3);color:#38bdf8;">76 <span>-8%</span></span>
          <span class="px-2 py-0.5 rounded font-mono" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">118 <span class="text-slate-400">±0%</span></span>
          <span class="px-2 py-0.5 rounded font-mono" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.3);color:#38bdf8;">68 <span>-12%</span></span>
        </div>
        <div class="relative flex-1" style="height:148px;">
          <canvas id="analyticsChart" class="w-full h-full"></canvas>
        </div>
      </div>

    </div>
  </div>

  {{-- ── RIGHT SIDEBAR COLUMN (4 COLUMNS) ── --}}
  <div class="lg:col-span-4 space-y-5">

    {{-- Fleet Status --}}
    <div class="glass-panel p-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-slate-100 text-sm">Fleet Status</h3>
        <a href="{{ route('fleet.vans') }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-sky-400 transition-colors" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
      </div>

      @php
      $fleet = [
        ['Ocean Majesty','Luxury Motor Van','Available','text-sky-400','bg-sky-400','/images/van_fleet_1.png'],
        ['Blue Horizon','Sport Cargo Van','In Transit','text-sky-400','bg-sky-400','/images/van_hero_bg.png'],
        ['Royal Escape','Super Cargo Van','Maintenance','text-sky-400','bg-sky-400','/images/van_fleet_2.png'],
        ['Sea Pearl','Luxury Courier Van','In Transit','text-sky-400','bg-sky-400','/images/van_ai_card.png'],
        ['Azure Dream','Luxury EV Cargo','Available','text-sky-400','bg-sky-400','/images/van_fleet_1.png'],
      ];
      @endphp

      <div class="space-y-2">
        @foreach($fleet as $v)
        <div class="glass-card p-2.5 flex items-center justify-between group hover:border-sky-400/40">
          <div class="flex items-center gap-3">
            <div class="w-14 h-10 rounded-lg overflow-hidden flex-shrink-0" style="border:1px solid rgba(255,255,255,0.1);background:#040711;">
              <img src="{{ asset($v[5]) }}" alt="{{ $v[0] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" />
            </div>
            <div>
              <h4 class="text-xs font-bold text-slate-100 group-hover:text-sky-300 transition-colors">{{ $v[0] }}</h4>
              <p class="text-[10px] text-slate-400">{{ $v[1] }}</p>
              <div class="flex items-center gap-1.5 mt-0.5">
                <span class="w-1.5 h-1.5 rounded-full {{ $v[4] }}"></span>
                <span class="text-[10px] {{ $v[3] }} font-semibold">{{ $v[2] }}</span>
              </div>
            </div>
          </div>
          <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-sky-400 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </div>
        @endforeach
      </div>
    </div>

    {{-- KPI Stats --}}
    <div class="glass-panel p-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-slate-100 text-sm">Performance Metrics</h3>
        <select class="glass-input text-[10px]" style="width:auto;padding:3px 8px;">
          <option>Today</option>
          <option>This Week</option>
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3">

        <div class="glass-card p-3">
          <span class="block text-[10px] font-semibold text-slate-400">Today's Bookings</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-xl font-extrabold text-slate-100">124</span>
            <span class="text-[10px] font-bold text-sky-400">+15%</span>
          </div>
          <div style="height:28px;margin-top:6px;"><canvas id="sparkline1" class="w-full h-full"></canvas></div>
        </div>

        <div class="glass-card p-3">
          <span class="block text-[10px] font-semibold text-slate-400">Active Charters</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-xl font-extrabold text-slate-100">86</span>
            <span class="text-[10px] font-bold text-sky-400">+8%</span>
          </div>
          <div style="height:28px;margin-top:6px;"><canvas id="sparkline2" class="w-full h-full"></canvas></div>
        </div>

        <div class="glass-card p-3">
          <span class="block text-[10px] font-semibold text-slate-400">Pending Requests</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-xl font-extrabold text-slate-100">32</span>
            <span class="text-[10px] font-bold text-sky-400">-2%</span>
          </div>
          <div style="height:28px;margin-top:6px;"><canvas id="sparkline3" class="w-full h-full"></canvas></div>
        </div>

        <div class="glass-card p-3">
          <span class="block text-[10px] font-semibold text-slate-400">Available Vans</span>
          <div class="flex items-baseline justify-between mt-1">
            <span class="text-xl font-extrabold text-slate-100">412</span>
            <span class="text-[10px] font-bold text-sky-400">+5%</span>
          </div>
          <div style="height:28px;margin-top:6px;"><canvas id="sparkline4" class="w-full h-full"></canvas></div>
        </div>

      </div>
    </div>

  </div>

</div>

{{-- ════════════════ BOTTOM TABLES SECTION ════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-5">

  {{-- ── RECENT TRIPS TABLE (8 cols) ── --}}
  <div class="lg:col-span-8 glass-panel overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h3 class="text-sm font-bold text-slate-100">Recent Trip Entries</h3>
        <p class="text-[11px] text-slate-400 mt-0.5">Last 10 dispatched routes</p>
      </div>
      <a href="{{ route('operations.trips') }}" class="btn-ghost text-xs" style="padding:5px 14px;">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead>
          <tr class="table-header">
            <th class="px-5 py-3 text-left">Trip ID</th>
            <th class="px-4 py-3 text-left">Van</th>
            <th class="px-4 py-3 text-left">Driver</th>
            <th class="px-4 py-3 text-left">Route</th>
            <th class="px-4 py-3 text-right">Revenue</th>
            <th class="px-4 py-3 text-right">Expense</th>
            <th class="px-4 py-3 text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          @php
          $trips = [
            ['TR-2401','Ocean Majesty','James Carter','London → Manchester',$currencySymbol . ' 1,240',$currencySymbol . ' 380','Completed'],
            ['TR-2402','Blue Horizon','Liam Hassan','Birmingham → Leeds',$currencySymbol . ' 890',$currencySymbol . ' 270','In Transit'],
            ['TR-2403','Royal Escape','Sofia Patel','Edinburgh → Glasgow',$currencySymbol . ' 560',$currencySymbol . ' 190','Completed'],
            ['TR-2404','Sea Pearl','David Nguyen','Bristol → Cardiff',$currencySymbol . ' 420',$currencySymbol . ' 145','Completed'],
            ['TR-2405','Azure Dream','Amara Osei','Sheffield → Nottm',$currencySymbol . ' 710',$currencySymbol . ' 230','In Transit'],
            ['TR-2406','Ocean Majesty','James Carter','Liverpool → Hull',$currencySymbol . ' 980',$currencySymbol . ' 310','Pending'],
            ['TR-2407','Blue Horizon','Liam Hassan','London → Brighton',$currencySymbol . ' 370',$currencySymbol . ' 125','Completed'],
            ['TR-2408','Royal Escape','Sofia Patel','Manchester → Leeds',$currencySymbol . ' 640',$currencySymbol . ' 200','In Transit'],
            ['TR-2409','Sea Pearl','David Nguyen','Newcastle → Durham',$currencySymbol . ' 280',$currencySymbol . ' 95','Completed'],
            ['TR-2410','Azure Dream','Amara Osei','London → Oxford',$currencySymbol . ' 530',$currencySymbol . ' 175','Pending'],
          ];
          $statusCls = ['Completed'=>'badge-blue','In Transit'=>'badge-blue','Pending'=>'badge-amber'];
          @endphp
          @foreach($trips as $i => $t)
          <tr class="table-row">
            <td class="px-5 py-3 font-mono font-bold text-sky-400">{{ $t[0] }}</td>
            <td class="px-4 py-3 text-slate-200 font-medium">{{ $t[1] }}</td>
            <td class="px-4 py-3 text-slate-300">{{ $t[2] }}</td>
            <td class="px-4 py-3 text-slate-400">{{ $t[3] }}</td>
            <td class="px-4 py-3 text-right text-sky-400 font-bold">{{ $t[4] }}</td>
            <td class="px-4 py-3 text-right text-sky-400 font-semibold">{{ $t[5] }}</td>
            <td class="px-4 py-3 text-center"><span class="{{ $statusCls[$t[6]] }}">{{ $t[6] }}</span></td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background:rgba(56,189,248,0.05);border-top:1px solid rgba(56,189,248,0.2);">
            <td colspan="4" class="px-5 py-3 text-xs font-bold text-slate-300">Total (10 Trips)</td>
            <td class="px-4 py-3 text-right text-sky-400 font-extrabold text-sm">{{ $currencySymbol }} 6,620</td>
            <td class="px-4 py-3 text-right text-sky-400 font-bold text-sm">{{ $currencySymbol }} 2,120</td>
            <td class="px-4 py-3 text-center text-sky-400 font-bold text-xs">Net: {{ $currencySymbol }} 4,500</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- ── TOP CUSTOMERS (4 cols) ── --}}
  <div class="lg:col-span-4 glass-panel overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h3 class="text-sm font-bold text-slate-100">Top Customers</h3>
        <p class="text-[11px] text-slate-400 mt-0.5">By revenue this month</p>
      </div>
      <a href="{{ route('crm.customers') }}" class="btn-ghost text-xs" style="padding:5px 14px;">All</a>
    </div>
    <div class="p-4 space-y-3">
      @php
      $customers = [
        ['Apex Logistics Ltd','34 trips',$currencySymbol . ' 18,450',92,'bg-sky-400'],
        ['BlueStar Retail','28 trips',$currencySymbol . ' 14,220',78,'bg-sky-400'],
        ['NovaMed Supplies','21 trips',$currencySymbol . ' 10,800',60,'bg-sky-400'],
        ['Horizon Foods','18 trips',$currencySymbol . ' 8,960',50,'bg-sky-400'],
        ['Sterling Motors','15 trips',$currencySymbol . ' 7,340',41,'bg-sky-400'],
        ['Prime Express UK','12 trips',$currencySymbol . ' 5,670',32,'bg-slate-500'],
      ];
      @endphp
      @foreach($customers as $c)
      <div class="glass-card p-3">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-slate-100">{{ $c[0] }}</span>
          <span class="text-xs font-bold text-sky-400">{{ $c[2] }}</span>
        </div>
        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1.5">
          <span>{{ $c[1] }}</span>
          <span>{{ $c[3] }}%</span>
        </div>
        <div class="h-1 rounded-full" style="background:rgba(255,255,255,0.08);">
          <div class="h-1 rounded-full {{ $c[4] }}" style="width:{{ $c[3] }}%;"></div>
        </div>
      </div>
      @endforeach
    </div>
  </div>

</div>

{{-- ════════════════ THIRD ROW ════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mt-5">

  {{-- ── RECENT EXPENSES TABLE (6 cols) ── --}}
  <div class="lg:col-span-6 glass-panel overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h3 class="text-sm font-bold text-slate-100">Recent Expenses</h3>
        <p class="text-[11px] text-slate-400 mt-0.5">Daily operational costs</p>
      </div>
      <a href="{{ route('operations.expenses') }}" class="btn-ghost text-xs" style="padding:5px 14px;">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead>
          <tr class="table-header">
            <th class="px-5 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Van</th>
            <th class="px-4 py-3 text-left">Category</th>
            <th class="px-4 py-3 text-right">Amount</th>
          </tr>
        </thead>
        <tbody>
          @php
          $expenses = [
            ['09 Sep 2026','Ocean Majesty','Fuel',$currencySymbol . ' 145'],
            ['09 Sep 2026','Blue Horizon','Motorway Toll',$currencySymbol . ' 38'],
            ['08 Sep 2026','Royal Escape','Maintenance',$currencySymbol . ' 310'],
            ['08 Sep 2026','Sea Pearl','Fuel',$currencySymbol . ' 128'],
            ['08 Sep 2026','Azure Dream','Driver Wage',$currencySymbol . ' 220'],
            ['07 Sep 2026','Ocean Majesty','Parking',$currencySymbol . ' 24'],
            ['07 Sep 2026','Blue Horizon','Fuel',$currencySymbol . ' 152'],
            ['07 Sep 2026','Royal Escape','Motorway Toll',$currencySymbol . ' 45'],
          ];
          @endphp
          @foreach($expenses as $e)
          <tr class="table-row">
            <td class="px-5 py-3 text-slate-400">{{ $e[0] }}</td>
            <td class="px-4 py-3 text-slate-200 font-medium">{{ $e[1] }}</td>
            <td class="px-4 py-3">
              @php
              $catColor = match($e[2]) {
                'Fuel' => 'badge-blue',
                'Maintenance' => 'badge-blue',
                'Driver Wage' => 'badge-blue',
                default => 'badge-slate'
              };
              @endphp
              <span class="{{ $catColor }}">{{ $e[2] }}</span>
            </td>
            <td class="px-4 py-3 text-right text-sky-400 font-bold">{{ $e[3] }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background:rgba(56,189,248,0.05);border-top:1px solid rgba(56,189,248,0.2);">
            <td colspan="3" class="px-5 py-3 text-xs font-bold text-slate-300">Total Expenses</td>
            <td class="px-4 py-3 text-right text-sky-400 font-extrabold">{{ $currencySymbol }} 1,062</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  {{-- ── DRIVER PERFORMANCE TABLE (6 cols) ── --}}
  <div class="lg:col-span-6 glass-panel overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h3 class="text-sm font-bold text-slate-100">Driver Performance</h3>
        <p class="text-[11px] text-slate-400 mt-0.5">Efficiency & earnings this month</p>
      </div>
      <a href="{{ route('crm.drivers') }}" class="btn-ghost text-xs" style="padding:5px 14px;">All Drivers</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-xs">
        <thead>
          <tr class="table-header">
            <th class="px-5 py-3 text-left">Driver</th>
            <th class="px-4 py-3 text-left">Van</th>
            <th class="px-4 py-3 text-center">Trips</th>
            <th class="px-4 py-3 text-right">Revenue</th>
            <th class="px-4 py-3 text-center">Rating</th>
          </tr>
        </thead>
        <tbody>
          @php
          $drivers = [
            ['James Carter','Ocean Majesty','34',$currencySymbol . ' 18,450','★★★★★','text-sky-400'],
            ['Liam Hassan','Blue Horizon','28',$currencySymbol . ' 14,220','★★★★☆','text-sky-400'],
            ['Sofia Patel','Royal Escape','21',$currencySymbol . ' 10,800','★★★★★','text-sky-400'],
            ['David Nguyen','Sea Pearl','18',$currencySymbol . ' 8,960','★★★★☆','text-sky-400'],
            ['Amara Osei','Azure Dream','15',$currencySymbol . ' 7,340','★★★☆☆','text-slate-400'],
            ['Raj Mehta','Blue Horizon','12',$currencySymbol . ' 5,670','★★★★☆','text-sky-400'],
          ];
          @endphp
          @foreach($drivers as $d)
          <tr class="table-row">
            <td class="px-5 py-3">
              <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-extrabold flex-shrink-0 {{ $d[5] }}" style="background:rgba(56,189,248,0.15);border:1px solid rgba(56,189,248,0.25);">
                  {{ strtoupper(substr($d[0],0,1).substr(explode(' ',$d[0])[1],0,1)) }}
                </div>
                <span class="font-semibold text-slate-200">{{ $d[0] }}</span>
              </div>
            </td>
            <td class="px-4 py-3 text-slate-400">{{ $d[1] }}</td>
            <td class="px-4 py-3 text-center font-bold text-slate-100">{{ $d[2] }}</td>
            <td class="px-4 py-3 text-right text-sky-400 font-bold">{{ $d[3] }}</td>
            <td class="px-4 py-3 text-center text-sky-400 text-[11px] tracking-wider">{{ $d[4] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>

@endsection

@section('scripts')
<script>
Chart.defaults.font.family = 'Plus Jakarta Sans, sans-serif';
Chart.defaults.color = '#94a3b8';

// 1. Scatter bubble grid
const ctxMatrix = document.getElementById('matrixScatterChart').getContext('2d');
const scatterData = [];
for (let x = 1; x <= 14; x++) {
  for (let y = 1; y <= 7; y++) {
    const r = Math.random();
    if (r > 0.38) {
      scatterData.push({ x, y, r: r > 0.8 ? 5 : (r > 0.6 ? 3.5 : 2) });
    }
  }
}
new Chart(ctxMatrix, {
  type: 'bubble',
  data: { datasets: [{ data: scatterData,
    backgroundColor: ctx => {
      const v = ctx.raw ? ctx.raw.r : 3;
      return v > 4 ? '#38bdf8' : (v > 3 ? '#60a5fa' : 'rgba(255,255,255,0.22)');
    },
    borderColor: 'transparent'
  }]},
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { enabled: false } },
    scales: {
      x: { display: false, min: 0, max: 15 },
      y: { display: false, min: 0, max: 8 }
    }
  }
});

// 2. Analytics waveform
const ctxA = document.getElementById('analyticsChart').getContext('2d');
const grad = ctxA.createLinearGradient(0, 0, 0, 140);
grad.addColorStop(0, 'rgba(56,189,248,0.25)');
grad.addColorStop(1, 'rgba(56,189,248,0)');
new Chart(ctxA, {
  type: 'line',
  data: {
    labels: ['Jan','Feb','Mar','Apr','May','Jun'],
    datasets: [
      { data: [118,76,127,118,68,118], borderColor: '#f8fafc', borderWidth: 2.5,
        pointBackgroundColor: '#38bdf8', pointBorderColor: '#ffffff', pointRadius: 3.5, tension: 0.1 },
      { data: [100,110,85,125,90,115], borderColor: '#38bdf8', borderWidth: 1.5,
        fill: true, backgroundColor: grad, pointRadius: 0, tension: 0.45 }
    ]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { color: '#94a3b8', font: { size: 9 } } },
      y: { beginAtZero: false, min: 40, max: 140, grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { color: '#94a3b8', font: { size: 9 } } }
    }
  }
});

// 3. Sparklines
function sparkline(id, data, color) {
  new Chart(document.getElementById(id).getContext('2d'), {
    type: 'line',
    data: { labels: data.map((_,i) => i), datasets: [{ data, borderColor: color, borderWidth: 1.8, pointRadius: 0, tension: 0.4 }]},
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false }, tooltip: { enabled: false } },
      scales: { x: { display: false }, y: { display: false } }
    }
  });
}
sparkline('sparkline1', [12,19,14,25,22,30,28,35], '#38bdf8');
sparkline('sparkline2', [15,12,20,18,24,22,29,31], '#38bdf8');
sparkline('sparkline3', [25,22,18,20,16,14,15,12], '#38bdf8');
sparkline('sparkline4', [30,32,28,35,38,42,40,45], '#38bdf8');
</script>
@endsection
