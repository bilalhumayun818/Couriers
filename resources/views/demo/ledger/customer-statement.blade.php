@extends('demo.layout')
@section('title', $customer->company_name . ' — Statement')
@section('page-title', $customer->company_name)
@section('page-subtitle', 'Customer account statement — full service history')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('ledger.customer', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
     class="text-sky-400 hover:text-sky-300 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
    </svg>
    Customer Ledger
  </a>
  <span class="text-slate-600">/</span>
  <span class="text-slate-200 font-semibold">{{ $customer->company_name }}</span>
</div>

{{-- Date range filter --}}
<div class="card px-5 py-3.5 mb-5 flex flex-wrap gap-3 items-center">
  <form method="GET" action="{{ route('ledger.customer.statement', $customer) }}" class="flex gap-2 items-center flex-wrap">
    <label class="text-xs font-semibold text-slate-400">Period:</label>
    <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}">
    <span class="text-slate-500 text-sm">to</span>
    <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}">
    <button type="submit" class="btn-primary text-xs px-3 py-1.5">Apply</button>
  </form>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Left: Profile + stats --}}
  <div class="space-y-5">

    {{-- Customer profile --}}
    <div class="card p-5 space-y-3">
      <div class="flex items-center gap-3 pb-3" style="border-bottom:1px solid rgba(255,255,255,0.07);">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.25);">
          <svg width="20" height="20" fill="none" stroke="#38bdf8" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <div>
          <p class="font-bold text-slate-100 text-sm">{{ $customer->company_name }}</p>
          <p class="text-xs text-slate-400">Customer since {{ $customer->created_at->format('M Y') }}</p>
        </div>
      </div>
      @foreach([
        ['Contact', $customer->contact_name ?? '—'],
        ['Email',   $customer->email ?? '—'],
        ['Phone',   $customer->phone ?? '—'],
      ] as [$label,$value])
      <div class="flex justify-between text-sm">
        <span class="text-slate-400 text-xs">{{ $label }}</span>
        <span class="text-slate-200 text-xs font-medium">{{ $value }}</span>
      </div>
      @endforeach
      <div class="flex justify-between text-sm pt-2 mt-1" style="border-top:1px solid rgba(255,255,255,0.07);">
        <span class="text-slate-400 text-xs">Credit Limit</span>
        <span class="font-semibold text-slate-200">{{ $currencySymbol }}{{ number_format($customer->credit_limit,2) }}</span>
      </div>
    </div>

    {{-- Period summary --}}
    <div class="card p-5 space-y-3">
      <h3 class="font-semibold text-slate-100 text-sm">Period Summary</h3>
      <div class="flex justify-between text-sm">
        <span class="text-slate-400">Active Trips</span>
        <span class="font-semibold text-slate-200">{{ $totalActive }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-slate-400">Voided Trips</span>
        <span class="font-semibold text-slate-500">{{ $totalVoided }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-slate-400">Total Fare</span>
        <span class="font-semibold text-slate-200">{{ $currencySymbol }}{{ number_format($totalFare,2) }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-slate-400">{{ $tenantSettings->tax_label }}</span>
        <span class="font-semibold text-slate-400">{{ $currencySymbol }}{{ number_format($totalTax,2) }}</span>
      </div>
      <div class="flex justify-between text-base font-bold pt-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <span class="text-slate-100">Total Invoiced</span>
        <span class="text-emerald-400">{{ $currencySymbol }}{{ number_format($totalInvoiced,2) }}</span>
      </div>
    </div>

    {{-- Monthly breakdown --}}
    @if($byMonth->isNotEmpty())
    <div class="card p-5">
      <h3 class="font-semibold text-slate-100 text-sm mb-3">Monthly Breakdown</h3>
      @php $maxMonth = $byMonth->max('total'); @endphp
      @foreach($byMonth as $m)
      <div class="mb-3">
        <div class="flex justify-between text-xs mb-1">
          <span class="font-medium text-slate-200">{{ $m['label'] }}</span>
          <span class="text-slate-400">{{ $m['count'] }} trips · {{ $currencySymbol }}{{ number_format($m['total'],2) }}</span>
        </div>
        <div style="height:5px;background:rgba(255,255,255,0.08);border-radius:4px;overflow:hidden;">
          <div style="height:100%;width:{{ $maxMonth>0?round(($m['total']/$maxMonth)*100):0 }}%;background:linear-gradient(135deg,#0284c7,#2563eb);border-radius:4px;"></div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Right: Transaction ledger --}}
  <div class="xl:col-span-2 card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <div>
        <h3 class="font-semibold text-slate-100 text-sm">Trip Statement</h3>
        <p class="text-xs text-slate-400 mt-0.5">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }} &bull; {{ $trips->count() }} records</p>
      </div>
      <a href="{{ route('operations.trips', ['customer_id' => $customer->id]) }}"
         class="text-xs text-sky-400 hover:text-sky-300 font-medium">View in Operations →</a>
    </div>

    @if($ledger->isEmpty())
    <div class="p-12 text-center text-slate-500 text-sm">
      No trips found for this customer in the selected period.
    </div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="table-header">
            <th class="px-4 py-3 text-left">Trip ID</th>
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Van</th>
            <th class="px-4 py-3 text-left">Route</th>
            <th class="px-4 py-3 text-right">Fare</th>
            <th class="px-4 py-3 text-right">Tax</th>
            <th class="px-4 py-3 text-right">Total</th>
            <th class="px-4 py-3 text-center">Status</th>
            <th class="px-4 py-3 text-right">Running</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ledger as $row)
          @php $trip = $row['trip']; @endphp
          <tr class="table-row {{ $trip->status==='voided'?'opacity-60':'' }}">
            <td class="px-4 py-3 font-mono text-xs text-sky-400 font-semibold"
                style="{{ $trip->status==='voided'?'text-decoration:line-through;':'' }}">
              T-{{ str_pad($trip->id,5,'0',STR_PAD_LEFT) }}
            </td>
            <td class="px-4 py-3 text-xs text-slate-400">{{ $trip->trip_date->format('d M Y') }}</td>
            <td class="px-4 py-3">
              <span class="font-mono text-xs px-2 py-0.5 rounded" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;">
                {{ $trip->van?->plate_number ?? '—' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-400">{{ $trip->origin }} → {{ $trip->destination }}</td>
            <td class="px-4 py-3 text-right text-xs text-slate-300">{{ $currencySymbol }}{{ number_format($trip->fare_amount,2) }}</td>
            <td class="px-4 py-3 text-right text-xs text-slate-400">{{ $currencySymbol }}{{ number_format($trip->tax_amount,2) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-slate-100">{{ $currencySymbol }}{{ number_format($trip->total_amount,2) }}</td>
            <td class="px-4 py-3 text-center">
              @if($trip->status==='active')
                <span class="badge-blue">Active</span>
              @else
                <span class="badge-blue">Voided</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right text-xs font-bold text-slate-200">
              {{ $trip->status==='active' ? '$'.number_format($row['balance'],2) : '—' }}
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot style="background:rgba(15,23,42,0.7);border-top:1px solid rgba(255,255,255,0.12);">
          <tr>
            <td colspan="6" class="px-4 py-3 font-bold text-slate-300 text-xs">PERIOD TOTAL</td>
            <td class="px-4 py-3 text-right font-bold text-emerald-400">{{ $currencySymbol }}{{ number_format($totalInvoiced,2) }}</td>
            <td></td>
            <td class="px-4 py-3 text-right font-bold text-emerald-400">{{ $currencySymbol }}{{ number_format($totalInvoiced,2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection

