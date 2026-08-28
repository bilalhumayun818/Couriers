@extends('demo.layout')
@section('title','Profit & Loss')
@section('page-title','Financial Statements — Profit & Loss')
@section('page-subtitle','Revenue vs expenses for the selected period')

@section('content')

{{-- Filter --}}
<div class="card px-5 py-4 mb-5">
  <form method="GET" action="{{ route('ledger.profit-loss') }}"
        class="flex flex-wrap gap-3 items-end justify-between">
    <div class="flex flex-wrap gap-2 items-end">
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
        <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
        <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}">
      </div>
      <button type="submit" class="btn-primary text-xs self-end px-3 py-2">Generate</button>
      <a href="{{ route('ledger.profit-loss') }}" class="btn-ghost text-xs self-end px-3 py-2">Reset</a>
    </div>
    <div class="text-xs text-gray-400 self-end">
      {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }} &bull; {{ $daysInRange }} days
    </div>
  </form>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Gross Revenue</p>
    <p class="text-2xl font-bold mt-1" style="color:#16a34a;">${{ number_format($tripRevenue,2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $tripCount }} active trips</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Expenses</p>
    <p class="text-2xl font-bold mt-1" style="color:#ef4444;">${{ number_format($totalExpenses,2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">All cost categories</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</p>
    <p class="text-2xl font-bold mt-1" style="color:{{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};">
      {{ $netProfit < 0 ? '−' : '' }}${{ number_format(abs($netProfit),2) }}
    </p>
    <p class="text-xs text-gray-400 mt-0.5">Margin: {{ $margin }}%</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Profit Margin</p>
    <p class="text-2xl font-bold mt-1" style="color:{{ $margin >= 0 ? '#6366f1' : '#ef4444' }};">{{ $margin }}%</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $voidedCount }} voided trips</p>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- P&L Statement --}}
  <div class="xl:col-span-2 card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
      <h3 class="font-semibold text-gray-800 text-sm">Profit & Loss Statement</h3>
      <p class="text-xs text-gray-400 mt-0.5">{{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</p>
    </div>

    <div class="divide-y divide-gray-50">

      {{-- Revenue --}}
      <div class="px-5 py-4" style="background:#f0fdf4;">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Revenue</p>
        <div class="flex justify-between text-sm mb-2">
          <span class="text-gray-700">Trip Revenue ({{ $tripCount }} trips)</span>
          <span class="font-semibold text-gray-800">${{ number_format($tripRevenue,2) }}</span>
        </div>
        <div class="flex justify-between text-sm font-bold border-t border-green-200 pt-2 mt-1">
          <span class="text-gray-800">Gross Revenue</span>
          <span style="color:#16a34a;" class="text-base">${{ number_format($tripRevenue,2) }}</span>
        </div>
      </div>

      {{-- Variable Expenses --}}
      <div class="px-5 py-4">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Variable Expenses</p>
        @php
        $varItems = [
          ['Fuel',                 $fuelAmt],
          ['Tolls',                $tollsAmt],
          ['Spare Parts',          $sparePartsAmt],
          ['Maintenance / Repairs',$maintenanceAmt],
        ];
        @endphp
        @foreach($varItems as [$label, $amt])
        @if($amt > 0)
        <div class="flex justify-between text-sm mb-1.5">
          <span class="text-gray-600">{{ $label }}</span>
          <span class="text-red-500">${{ number_format($amt,2) }}</span>
        </div>
        @endif
        @endforeach
        @if($totalVariable == 0)
        <p class="text-xs text-gray-400">No variable expenses in this period.</p>
        @endif
        <div class="flex justify-between text-sm font-semibold border-t border-gray-200 pt-2 mt-1">
          <span class="text-gray-700">Total Variable</span>
          <span class="text-red-500">${{ number_format($totalVariable,2) }}</span>
        </div>
      </div>

      {{-- Fixed Expenses --}}
      <div class="px-5 py-4">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Fixed Costs ({{ $daysInRange }}-day proration)</p>
        @foreach([
          ['Monthly Lease (prorated)',     $totalLease],
          ['Road Tax (prorated)',          $totalRoadTax],
          ['Insurance (prorated)',         $totalInsurance],
        ] as [$label, $amt])
        @if($amt > 0)
        <div class="flex justify-between text-sm mb-1.5">
          <span class="text-gray-600">{{ $label }}</span>
          <span class="text-red-500">${{ number_format($amt,2) }}</span>
        </div>
        @endif
        @endforeach
        <div class="flex justify-between text-sm font-semibold border-t border-gray-200 pt-2 mt-1">
          <span class="text-gray-700">Total Fixed</span>
          <span class="text-red-500">${{ number_format($totalFixed,2) }}</span>
        </div>
      </div>

      {{-- Wages & Advances --}}
      @if($totalWages > 0 || $totalAdvances > 0)
      <div class="px-5 py-4">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Staff Costs</p>
        @if($totalWages > 0)
        <div class="flex justify-between text-sm mb-1.5">
          <span class="text-gray-600">Driver Wages (Gross)</span>
          <span class="text-red-500">${{ number_format($totalWages,2) }}</span>
        </div>
        @endif
        @if($totalAdvances > 0)
        <div class="flex justify-between text-sm mb-1.5">
          <span class="text-gray-600">Driver Advances</span>
          <span class="text-red-500">${{ number_format($totalAdvances,2) }}</span>
        </div>
        @endif
        <div class="flex justify-between text-sm font-semibold border-t border-gray-200 pt-2 mt-1">
          <span class="text-gray-700">Total Staff Costs</span>
          <span class="text-red-500">${{ number_format($totalWages + $totalAdvances,2) }}</span>
        </div>
      </div>
      @endif

      {{-- Net result --}}
      <div class="px-5 py-5" style="background:{{ $netProfit >= 0 ? '#f0fdf4' : '#fef2f2' }};">
        <div class="flex justify-between text-sm mb-2">
          <span class="text-gray-600">Gross Revenue</span>
          <span class="font-semibold text-gray-800">${{ number_format($tripRevenue,2) }}</span>
        </div>
        <div class="flex justify-between text-sm mb-2">
          <span class="text-gray-600">Total Expenses</span>
          <span class="font-semibold text-red-500">−${{ number_format($totalExpenses,2) }}</span>
        </div>
        <div class="flex justify-between font-bold text-base border-t-2 pt-3 mt-2"
             style="border-color:{{ $netProfit >= 0 ? '#bbf7d0' : '#fecaca' }};">
          <span class="text-gray-900">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
          <span style="color:{{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};" class="text-xl">
            {{ $netProfit < 0 ? '−' : '' }}${{ number_format(abs($netProfit),2) }}
          </span>
        </div>
        <p class="text-xs mt-1" style="color:{{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};">
          Profit margin: {{ $margin }}%
        </p>
      </div>
    </div>
  </div>

  {{-- Right column: charts and breakdown --}}
  <div class="space-y-5">

    {{-- Expense pie chart --}}
    <div class="card p-5">
      <h3 class="font-semibold text-gray-800 text-sm mb-3">Expense Breakdown</h3>
      <canvas id="expPie" height="200"></canvas>
    </div>

    {{-- 6-month trend --}}
    <div class="card p-5">
      <h3 class="font-semibold text-gray-800 text-sm mb-3">6-Month Trend</h3>
      <canvas id="trendChart" height="200"></canvas>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
Chart.defaults.font.family = 'Inter, sans-serif';
Chart.defaults.color = '#6b7280';

// Expense breakdown pie
new Chart(document.getElementById('expPie'), {
  type: 'doughnut',
  data: {
    labels: ['Fuel','Tolls','Spare Parts','Maintenance','Wages','Advances','Lease','Road Tax','Insurance'],
    datasets: [{
      data: [
        {{ $fuelAmt }}, {{ $tollsAmt }}, {{ $sparePartsAmt }},
        {{ $maintenanceAmt }}, {{ $totalWages }}, {{ $totalAdvances }},
        {{ $totalLease }}, {{ $totalRoadTax }}, {{ $totalInsurance }}
      ],
      backgroundColor: [
        '#f59e0b','#6b7280','#6366f1','#ef4444',
        '#3b82f6','#8b5cf6','#10b981','#14b8a6','#f97316'
      ],
      borderWidth: 0,
    }]
  },
  options: {
    responsive: true, cutout: '60%',
    plugins: {
      legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
    }
  }
});

// 6-month trend bar chart
new Chart(document.getElementById('trendChart'), {
  type: 'bar',
  data: {
    labels: {!! json_encode(collect($trend)->pluck('label')) !!},
    datasets: [
      { label: 'Revenue', data: {!! json_encode(collect($trend)->pluck('revenue')) !!}, backgroundColor: '#6366f1', borderRadius: 4 },
      { label: 'Expenses', data: {!! json_encode(collect($trend)->pluck('expenses')) !!}, backgroundColor: '#f3f4f6', borderRadius: 4, borderColor: '#e5e7eb', borderWidth: 1 },
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 11 } } } },
    scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
  }
});
</script>
@endsection
