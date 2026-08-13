@extends('demo.layout')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-subtitle','Real-time overview — Acme Logistics Ltd')

@section('content')

{{-- Alerts --}}
<div class="space-y-2 mb-5">
  <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 text-sm text-amber-800">
    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <span><strong>Service Due:</strong> Van GHJ-441 is due for scheduled maintenance on <strong>20 Jun 2025</strong> (3 days).</span>
  </div>
  <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-800">
    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
    <span><strong>Licence Expiry:</strong> Driver <strong>James Mwangi's</strong> licence expires in 18 days (04 Jul 2025).</span>
  </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

  @php
  $cards = [
    ['Active Fleet',    '12 Vehicles',  '+2 this month',    true,  'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6'],
    ['Monthly Revenue', '$48,320',      '+12% vs last month', true, 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
    ['Total Expenses',  '$31,140',      '−4% vs last month',  true, 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6M9 16h4'],
    ['Net Profit Margin','35.5%',       '+3.2pp vs last month', true,'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
  ];
  @endphp

  @foreach($cards as $i => $c)
  <div class="card p-5">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ $c[0] }}</p>
        <p class="text-2xl font-bold text-slate-800 mt-1.5 leading-none">{{ $c[1] }}</p>
      </div>
      <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4.5 h-4.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $c[3] }}"/></svg>
      </div>
    </div>
    <div class="mt-3 flex items-center gap-1.5">
      <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
      <span class="text-xs text-emerald-600 font-medium">{{ $c[2] }}</span>
    </div>
  </div>
  @endforeach
</div>

{{-- Charts row --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-5">

  {{-- Revenue vs Expenses bar chart --}}
  <div class="card xl:col-span-2 p-5">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-semibold text-slate-800 text-sm">Revenue vs Expenses — Per Van</h3>
        <p class="text-xs text-slate-400 mt-0.5">June 2025 &bull; All active vehicles</p>
      </div>
      <select class="text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-300">
        <option>This Month</option>
        <option>Last Month</option>
        <option>Last 3 Months</option>
      </select>
    </div>
    <canvas id="revenueChart" height="200"></canvas>
  </div>

  {{-- Fleet status doughnut --}}
  <div class="card p-5">
    <h3 class="font-semibold text-slate-800 text-sm mb-1">Fleet Status</h3>
    <p class="text-xs text-slate-400 mb-4">12 total vehicles</p>
    <canvas id="fleetChart" height="180"></canvas>
    <div class="mt-4 space-y-2.5">
      @php
      $status = [['Active','9','bg-indigo-500'],['Maintenance','2','bg-amber-400'],['Leased','1','bg-slate-400']];
      @endphp
      @foreach($status as $s)
      <div class="flex items-center justify-between text-sm">
        <div class="flex items-center gap-2">
          <span class="w-2.5 h-2.5 rounded-full {{ $s[2] }}"></span>
          <span class="text-slate-600">{{ $s[0] }}</span>
        </div>
        <span class="font-semibold text-slate-800">{{ $s[1] }}</span>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- Recent Trips table --}}
<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <div>
      <h3 class="font-semibold text-slate-800 text-sm">Recent Trips</h3>
      <p class="text-xs text-slate-400 mt-0.5">Last 5 trip entries</p>
    </div>
    <a href="{{ route('operations.trips') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all trips →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Customer</th>
          <th class="px-5 py-3 text-left">Route</th>
          <th class="px-5 py-3 text-right">Fare</th>
          <th class="px-5 py-3 text-center">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @php
        $trips = [
          ['17 Jun 2025','ABC-001','Swift Retail Co','Nairobi → Mombasa','$420.00','active'],
          ['17 Jun 2025','XYZ-202','Global Traders Ltd','Nakuru → Nairobi','$180.00','active'],
          ['16 Jun 2025','DEF-303','Metro Supplies','Kisumu → Nakuru','$260.00','active'],
          ['16 Jun 2025','GHJ-441','Apex Importers','Nairobi → Eldoret','$350.00','voided'],
          ['15 Jun 2025','ABC-001','Swift Retail Co','Mombasa → Nairobi','$400.00','active'],
        ];
        @endphp
        @foreach($trips as $t)
        <tr class="table-row">
          <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $t[0] }}</td>
          <td class="px-5 py-3.5"><span class="font-mono font-semibold text-slate-700 text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $t[1] }}</span></td>
          <td class="px-5 py-3.5 text-slate-700">{{ $t[2] }}</td>
          <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $t[3] }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-slate-800">{{ $t[4] }}</td>
          <td class="px-5 py-3.5 text-center">
            @if($t[5]==='active')
              <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-medium px-2 py-0.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
              </span>
            @else
              <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 border border-red-200 text-xs font-medium px-2 py-0.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Voided
              </span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('scripts')
<script>
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#64748b';

new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: ['ABC-001','XYZ-202','DEF-303','GHJ-441','KLM-505','NOP-606','QRS-707','STU-808'],
    datasets: [
      { label: 'Revenue ($)', data: [8200,6400,5100,4800,7300,6100,5500,4920],
        backgroundColor: '#6366f1', borderRadius: 4, borderSkipped: false },
      { label: 'Expenses ($)', data: [5100,4200,3800,3100,4600,3900,3400,3140],
        backgroundColor: '#e0e7ff', borderRadius: 4, borderSkipped: false }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 12 } } } },
    scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
  }
});

new Chart(document.getElementById('fleetChart'), {
  type: 'doughnut',
  data: {
    labels: ['Active','Maintenance','Leased'],
    datasets: [{ data: [9,2,1], backgroundColor: ['#6366f1','#fbbf24','#94a3b8'], borderWidth: 0 }]
  },
  options: {
    responsive: true,
    cutout: '68%',
    plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} vehicles` } } }
  }
});
</script>
@endsection
