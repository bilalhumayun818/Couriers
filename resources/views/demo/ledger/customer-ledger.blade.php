@extends('demo.layout')
@section('title','Customer Ledger')
@section('page-title','Ledgers — Customer Ledger')
@section('page-subtitle','Account statements showing bookings and outstanding balances per customer')

@section('content')

{{-- Filter bar --}}
<div class="card px-5 py-4 mb-5">
  <form method="GET" action="{{ route('ledger.customer') }}" id="filterForm"
        class="flex flex-wrap gap-3 items-end justify-between">
    <div class="flex flex-wrap gap-2 items-end">
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Search Customer</label>
        <input type="text" name="search" placeholder="Company name…"
               value="{{ $search }}" class="text-sm w-48">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">From</label>
        <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}" onchange="filterForm.submit()">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">To</label>
        <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}" onchange="filterForm.submit()">
      </div>
      <button type="submit" class="btn-primary text-xs self-end px-3 py-2">Apply</button>
      @if($search || request()->hasAny(['from','to']))
        <a href="{{ route('ledger.customer') }}" class="btn-ghost text-xs self-end px-3 py-2">✕ Reset</a>
      @endif
    </div>
    <div class="text-xs text-slate-400 self-end">
      {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}
    </div>
  </form>
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Invoiced</p>
    <p class="text-2xl font-bold text-emerald-400 mt-1">${{ number_format($grandTotal,2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">{{ $grandTrips }} active trips</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Active Customers</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $customers->where('total_trips','>', 0)->count() }}</p>
    <p class="text-xs text-slate-500 mt-0.5">with trips in period</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Customers</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $customers->count() }}</p>
    <p class="text-xs text-slate-500 mt-0.5">registered</p>
  </div>
</div>

{{-- Customer table --}}
<div class="card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Customer</th>
          <th class="px-5 py-3 text-left">Contact</th>
          <th class="px-5 py-3 text-right">Trips</th>
          <th class="px-5 py-3 text-right">Total Invoiced</th>
          <th class="px-5 py-3 text-right">Credit Limit</th>
          <th class="px-5 py-3 text-left">Last Trip</th>
          <th class="px-5 py-3 text-right">Statement</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $row)
        @php
          $c = $row['customer'];
          $utilizationPct = $c->credit_limit > 0
            ? min(100, round(($row['total_invoiced'] / $c->credit_limit) * 100, 1))
            : 0;
        @endphp
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <div class="font-semibold text-slate-100">{{ $c->company_name }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ $c->email ?? '—' }}</div>
          </td>
          <td class="px-5 py-3.5 text-slate-300 text-xs">{{ $c->contact_name ?? '—' }}</td>
          <td class="px-5 py-3.5 text-right text-slate-300">{{ $row['total_trips'] }}</td>
          <td class="px-5 py-3.5 text-right">
            <div class="font-semibold text-slate-100">${{ number_format($row['total_invoiced'],2) }}</div>
            @if($c->credit_limit > 0)
            <div class="mt-1" style="height:4px;background:rgba(255,255,255,0.08);border-radius:4px;width:80px;margin-left:auto;">
              <div style="height:100%;width:{{ $utilizationPct }}%;background:{{ $utilizationPct>80?'#f87171':($utilizationPct>50?'#fbbf24':'#38bdf8') }};border-radius:4px;"></div>
            </div>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right text-slate-400 text-xs">${{ number_format($c->credit_limit,2) }}</td>
          <td class="px-5 py-3.5 text-xs text-slate-400">
            {{ $row['last_trip_date'] ? $row['last_trip_date']->format('d M Y') : '—' }}
          </td>
          <td class="px-5 py-3.5 text-right">
            <a href="{{ route('ledger.customer.statement', ['customer' => $c->id, 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
               class="text-xs text-sky-400 hover:text-sky-300 font-semibold">
              View →
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-5 py-12 text-center text-slate-500 text-sm">
            No customers found.
          </td>
        </tr>
        @endforelse
      </tbody>
      @if($customers->isNotEmpty())
      <tfoot style="background:rgba(15,23,42,0.7);border-top:1px solid rgba(255,255,255,0.12);">
        <tr>
          <td colspan="2" class="px-5 py-3 font-bold text-slate-300 text-xs">TOTALS</td>
          <td class="px-5 py-3 text-right font-bold text-slate-200">{{ $grandTrips }}</td>
          <td class="px-5 py-3 text-right font-bold text-emerald-400">${{ number_format($grandTotal,2) }}</td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection

