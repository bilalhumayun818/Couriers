@extends('demo.layout')
@section('title','Van-Wise Ledger')
@section('page-title','Ledgers — Van-Wise Profit & Loss')
@section('page-subtitle','Revenue vs expenses per vehicle for the selected period')

@section('content')

{{-- Filter bar --}}
<div class="card px-5 py-4 mb-5">
  <form method="GET" action="{{ route('ledger.van') }}" id="filterForm"
        class="flex flex-wrap gap-3 items-end justify-between">
    <div class="flex flex-wrap gap-2 items-end">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">Van</label>
        <select name="van_id" class="text-sm" onchange="filterForm.submit()">
          <option value="">All Vans</option>
          @foreach($vans as $van)
            <option value="{{ $van->id }}" {{ $vanId == $van->id ? 'selected' : '' }}>{{ $van->plate_number }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">Expense Category</label>
        <select name="category" class="text-sm" onchange="filterForm.submit()">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">From</label>
        <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}" onchange="filterForm.submit()">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">To</label>
        <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}" onchange="filterForm.submit()">
      </div>
      @if(request()->hasAny(['van_id','category','from','to']))
        <a href="{{ route('ledger.van') }}" class="btn-ghost text-xs self-end" style="padding:6px 12px;">✕ Reset</a>
      @endif
    </div>
    <div class="text-xs text-slate-400 self-end">
      Period: <strong class="text-slate-200">{{ $daysInRange }}</strong> days &bull; {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}
    </div>
  </form>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Revenue</p>
    <p class="text-2xl font-bold mt-1 text-emerald-400">${{ number_format($grandRevenue,2) }}</p>
    <p class="text-xs text-slate-400 mt-0.5">All active trips</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Variable Expenses</p>
    <p class="text-2xl font-bold mt-1 text-red-400">${{ number_format($grandVariable,2) }}</p>
    <p class="text-xs text-slate-400 mt-0.5">Fuel, tolls, parts, repairs</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Fixed Costs (Prorated)</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">${{ number_format($grandFixed,2) }}</p>
    <p class="text-xs text-slate-400 mt-0.5">{{ $daysInRange }}-day proration</p>
  </div>
  <div class="card p-4">
    @php $margin = $grandRevenue > 0 ? round(($grandNet / $grandRevenue) * 100, 1) : 0; @endphp
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Net Profit / Loss</p>
    <p class="text-2xl font-bold mt-1 {{ $grandNet >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
      {{ $grandNet < 0 ? '−' : '' }}${{ number_format(abs($grandNet),2) }}
    </p>
    <p class="text-xs text-slate-400 mt-0.5">Margin: {{ $margin }}%</p>
  </div>
</div>

{{-- Main table --}}
<div class="card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-right">Revenue</th>
          <th class="px-5 py-3 text-right">Trips</th>
          <th class="px-5 py-3 text-right">Variable Exp.</th>
          @if(!$category)
            <th class="px-5 py-3 text-right">Fixed (Prorated)</th>
          @endif
          <th class="px-5 py-3 text-right">Total Exp.</th>
          <th class="px-5 py-3 text-right">Net P/L</th>
          <th class="px-5 py-3 text-center">Result</th>
          <th class="px-5 py-3 text-right">Detail</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $row)
          <tr class="table-row">
            <td class="px-5 py-3.5">
              <span class="font-mono font-semibold text-xs px-2.5 py-1 rounded-md" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">{{ $row['van']->plate_number }}</span>
              <span class="text-xs text-slate-400 ml-1.5">{{ $row['van']->make_model }}</span>
            </td>
            <td class="px-5 py-3.5 text-right font-semibold text-emerald-400">
              ${{ number_format($row['revenue'],2) }}
            </td>
            <td class="px-5 py-3.5 text-right text-slate-400">{{ $row['trip_count'] }}</td>
            <td class="px-5 py-3.5 text-right text-red-400">${{ number_format($row['variable_expenses'],2) }}</td>
            @if(!$category)
              <td class="px-5 py-3.5 text-right text-slate-300 text-xs">
                <div>${{ number_format($row['fixed_total'],2) }}</div>
                <div class="text-slate-400 mt-0.5">
                  L:${{ number_format($row['fixed_lease'],0) }}
                  T:${{ number_format($row['fixed_road_tax'],0) }}
                  I:${{ number_format($row['fixed_insurance'],0) }}
                </div>
              </td>
            @endif
            <td class="px-5 py-3.5 text-right font-semibold text-slate-200">
              ${{ number_format($row['total_expenses'],2) }}
            </td>
            <td class="px-5 py-3.5 text-right font-bold {{ $row['is_profitable'] ? 'text-emerald-400' : 'text-red-400' }}">
              {{ $row['net_profit'] < 0 ? '−' : '' }}${{ number_format(abs($row['net_profit']),2) }}
            </td>
            <td class="px-5 py-3.5 text-center">
              @if($row['is_profitable'])
                <span style="background:rgba(16,185,129,0.15);color:#4ade80;border:1px solid rgba(16,185,129,0.3);" class="text-xs font-semibold px-2.5 py-1 rounded-full">Profit</span>
              @else
                <span style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);" class="text-xs font-semibold px-2.5 py-1 rounded-full">Loss</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-right">
              <a href="{{ route('ledger.van.detail', ['van' => $row['van']->id, 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                 class="text-xs text-sky-400 hover:text-sky-300 font-semibold">
                View →
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ $category ? 8 : 9 }}" class="px-5 py-12 text-center text-slate-400 text-sm">
              No activity found for the selected period and filters.
            </td>
          </tr>
        @endforelse
      </tbody>

      @if(count($rows) > 0)
      <tfoot style="background:rgba(15,23,42,0.7);border-top:1px solid rgba(255,255,255,0.12);">
        <tr>
          <td class="px-5 py-3.5 font-bold text-slate-200 text-xs">
            TOTALS — {{ $from->format('d M') }} to {{ $to->format('d M Y') }}
          </td>
          <td class="px-5 py-3.5 text-right font-bold text-emerald-400">
            ${{ number_format($grandRevenue,2) }}
          </td>
          <td class="px-5 py-3.5 text-right font-bold text-slate-300">
            {{ collect($rows)->sum('trip_count') }}
          </td>
          <td class="px-5 py-3.5 text-right font-bold text-red-400">
            ${{ number_format($grandVariable,2) }}
          </td>
          @if(!$category)
            <td class="px-5 py-3.5 text-right font-bold text-slate-300">
              ${{ number_format($grandFixed,2) }}
            </td>
          @endif
          <td class="px-5 py-3.5 text-right font-bold text-slate-100">
            ${{ number_format($grandVariable + $grandFixed,2) }}
          </td>
          <td class="px-5 py-3.5 text-right font-extrabold text-base {{ $grandNet >= 0 ? 'text-emerald-400' : 'text-red-400' }}">
            {{ $grandNet < 0 ? '−' : '' }}${{ number_format(abs($grandNet),2) }}
          </td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>

@if(!$category)
<p class="text-xs text-slate-400 mt-3">
  * Fixed costs prorated as: <code class="text-slate-300">(monthly_cost ÷ days_in_month) × {{ $daysInRange }} days</code>
</p>
@endif

@endsection
