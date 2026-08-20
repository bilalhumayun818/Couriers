@extends('demo.layout')
@section('title', $van->plate_number . ' — Ledger')
@section('page-title', $van->plate_number . ' — Detailed Ledger')
@section('page-subtitle', $van->make_model . ' · ' . $from->format('d M Y') . ' – ' . $to->format('d M Y'))

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('ledger.van', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
     class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
    </svg>
    Van-Wise Ledger
  </a>
  <span class="text-gray-300">/</span>
  <span class="text-gray-700 font-semibold">{{ $van->plate_number }}</span>
</div>

{{-- Date range filter --}}
<div class="card px-5 py-3.5 mb-5 flex flex-wrap gap-3 items-center">
  <form method="GET" action="{{ route('ledger.van.detail', $van) }}" class="flex gap-2 items-center flex-wrap">
    <label class="text-xs font-semibold text-gray-500">Period:</label>
    <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}">
    <span class="text-gray-400 text-sm">to</span>
    <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}">
    <button type="submit" class="btn-primary text-xs px-3 py-1.5">Apply</button>
  </form>
  <span class="text-xs text-gray-400">{{ $daysInRange }} days</span>
</div>

{{-- P&L Summary --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Revenue</p>
    <p class="text-2xl font-bold mt-1" style="color:#16a34a;">${{ number_format($revenue,2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $trips->count() }} trips</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Variable Expenses</p>
    <p class="text-2xl font-bold mt-1" style="color:#ef4444;">${{ number_format($variableTotal,2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $expenses->count() }} records</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Fixed (Prorated)</p>
    <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($fixedTotal,2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $daysInRange }}-day basis</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Net Profit / Loss</p>
    <p class="text-2xl font-bold mt-1" style="color:{{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};">
      {{ $netProfit < 0 ? '−' : '' }}${{ number_format(abs($netProfit),2) }}
    </p>
    @php $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0; @endphp
    <p class="text-xs text-gray-400 mt-0.5">Margin: {{ $margin }}%</p>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Left: Van info + Expense breakdown --}}
  <div class="space-y-5">

    {{-- Van profile --}}
    <div class="card p-5 space-y-3">
      <h3 class="font-semibold text-gray-800 text-sm">Vehicle Info</h3>
      @foreach([
        ['Plate','van','plate_number'],
        ['Model','van','make_model'],
        ['Year','van','year'],
        ['Status','van','status'],
      ] as [$label,$obj,$prop])
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">{{ $label }}</span>
        <span class="font-semibold text-gray-800 capitalize">{{ $van->$prop }}</span>
      </div>
      @endforeach
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Driver</span>
        <span class="font-semibold text-gray-800">{{ $van->driver?->full_name ?? '—' }}</span>
      </div>
    </div>

    {{-- Expense by category --}}
    <div class="card p-5">
      <h3 class="font-semibold text-gray-800 text-sm mb-3">Expenses by Category</h3>
      @php
      $catColors = ['Fuel'=>'#f59e0b','Tolls'=>'#6b7280','Spare Parts'=>'#6366f1','Maintenance/Repairs'=>'#ef4444'];
      $allCats = \App\Models\Expense::$categories;
      @endphp
      @foreach($allCats as $cat)
      @php $amt = (float)($expByCategory[$cat] ?? 0); @endphp
      <div class="mb-3">
        <div class="flex justify-between text-xs mb-1">
          <span class="font-medium text-gray-700">{{ $cat }}</span>
          <span class="font-semibold text-gray-800">${{ number_format($amt,2) }}</span>
        </div>
        @if($variableTotal > 0)
        <div style="height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
          <div style="height:100%;width:{{ min(100,round(($amt/$variableTotal)*100,1)) }}%;background:{{ $catColors[$cat] ?? '#6366f1' }};border-radius:4px;"></div>
        </div>
        @else
        <div style="height:6px;background:#f1f5f9;border-radius:4px;"></div>
        @endif
      </div>
      @endforeach
    </div>

    {{-- Fixed costs --}}
    <div class="card p-5">
      <h3 class="font-semibold text-gray-800 text-sm mb-3">Fixed Costs ({{ $daysInRange }}-day proration)</h3>
      @php $fc = $van->fixedCost; @endphp
      @foreach([
        ['Monthly Lease',     $proratedLease,     $fc?->monthly_lease ?? 0,     '/mo'],
        ['Road Tax',          $proratedRoadTax,   $fc?->road_tax_annual ?? 0,   '/yr'],
        ['Insurance',         $proratedInsurance, $fc?->insurance_monthly ?? 0, '/mo'],
      ] as [$label,$prorated,$full,$unit])
      <div class="flex justify-between text-sm mb-2">
        <div>
          <span class="text-gray-700">{{ $label }}</span>
          <span class="text-xs text-gray-400 ml-1">(${{''.number_format($full,0)}}{{ $unit }})</span>
        </div>
        <span class="font-semibold text-gray-800">${{ number_format($prorated,2) }}</span>
      </div>
      @endforeach
      <div class="flex justify-between text-sm font-bold border-t border-gray-100 pt-2 mt-1">
        <span class="text-gray-800">Total Fixed</span>
        <span class="text-gray-900">${{ number_format($fixedTotal,2) }}</span>
      </div>
    </div>
  </div>

  {{-- Right: Trip history + Expense log --}}
  <div class="xl:col-span-2 space-y-5">

    {{-- Trips --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Trip Revenue</h3>
        <p class="text-xs text-gray-400 mt-0.5">{{ $trips->count() }} active trips — ${{ number_format($revenue,2) }} total</p>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="table-header">
            <th class="px-4 py-3 text-left">Trip ID</th>
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Customer</th>
            <th class="px-4 py-3 text-left">Route</th>
            <th class="px-4 py-3 text-right">Fare</th>
          </tr></thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($trips as $trip)
            <tr class="table-row">
              <td class="px-4 py-3 font-mono text-xs text-indigo-600 font-semibold">T-{{ str_pad($trip->id,5,'0',STR_PAD_LEFT) }}</td>
              <td class="px-4 py-3 text-xs text-gray-500">{{ $trip->trip_date->format('d M Y') }}</td>
              <td class="px-4 py-3 text-gray-700 text-xs">{{ $trip->customer?->company_name ?? '—' }}</td>
              <td class="px-4 py-3 text-xs text-gray-400">{{ $trip->origin }} → {{ $trip->destination }}</td>
              <td class="px-4 py-3 text-right font-semibold text-emerald-700">${{ number_format($trip->fare_amount,2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">No trips in this period.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Expenses --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Variable Expenses</h3>
        <p class="text-xs text-gray-400 mt-0.5">{{ $expenses->count() }} records — ${{ number_format($variableTotal,2) }} total</p>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead><tr class="table-header">
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Category</th>
            <th class="px-4 py-3 text-left">Description</th>
            <th class="px-4 py-3 text-right">Amount</th>
          </tr></thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($expenses as $exp)
            @php
              $catStyle = match($exp->category) {
                'Fuel'                => 'background:#fffbeb;color:#b45309;',
                'Tolls'               => 'background:#f9fafb;color:#374151;',
                'Spare Parts'         => 'background:#eff6ff;color:#1d4ed8;',
                'Maintenance/Repairs' => 'background:#fef2f2;color:#991b1b;',
                default               => 'background:#f3f4f6;color:#374151;',
              };
            @endphp
            <tr class="table-row">
              <td class="px-4 py-3 text-xs text-gray-500">{{ $exp->expense_date->format('d M Y') }}</td>
              <td class="px-4 py-3">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="{{ $catStyle }}">{{ $exp->category }}</span>
              </td>
              <td class="px-4 py-3 text-xs text-gray-500">{{ $exp->description ?? '—' }}</td>
              <td class="px-4 py-3 text-right font-semibold text-red-500">${{ number_format($exp->amount,2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 text-xs">No expenses in this period.</td></tr>
            @endforelse
          </tbody>
          @if($expenses->isNotEmpty())
          <tfoot style="background:#f8fafc;border-top:1px solid #e5e7eb;">
            <tr>
              <td colspan="3" class="px-4 py-3 font-bold text-gray-700 text-xs">Total Variable Expenses</td>
              <td class="px-4 py-3 text-right font-bold text-red-500">${{ number_format($variableTotal,2) }}</td>
            </tr>
          </tfoot>
          @endif
        </table>
      </div>
    </div>

    {{-- P&L Summary box --}}
    <div class="card p-5">
      <h3 class="font-semibold text-gray-800 text-sm mb-3">P&L Summary — {{ $from->format('d M') }} to {{ $to->format('d M Y') }}</h3>
      <div class="space-y-2">
        <div class="flex justify-between text-sm"><span class="text-gray-500">Revenue</span><span class="font-semibold text-emerald-700">${{ number_format($revenue,2) }}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Variable Expenses</span><span class="font-semibold text-red-500">−${{ number_format($variableTotal,2) }}</span></div>
        <div class="flex justify-between text-sm"><span class="text-gray-500">Fixed Costs (Prorated)</span><span class="font-semibold text-gray-700">−${{ number_format($fixedTotal,2) }}</span></div>
        <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-2 mt-1">
          <span class="text-gray-800">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
          <span style="color:{{ $netProfit >= 0 ? '#16a34a' : '#ef4444' }};">
            {{ $netProfit < 0 ? '−' : '' }}${{ number_format(abs($netProfit),2) }}
          </span>
        </div>
        <div class="flex justify-between text-xs text-gray-400 pt-1">
          <span>Profit Margin</span>
          <span>{{ $margin }}%</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
