@extends('demo.layout')
@section('title', $customer->company_name . ' — Statement')
@section('page-title', $customer->company_name)
@section('page-subtitle', 'Customer account statement — full service history')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('ledger.customer', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
     class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
    </svg>
    Customer Ledger
  </a>
  <span class="text-gray-300">/</span>
  <span class="text-gray-700 font-semibold">{{ $customer->company_name }}</span>
</div>

{{-- Date range filter --}}
<div class="card px-5 py-3.5 mb-5 flex flex-wrap gap-3 items-center">
  <form method="GET" action="{{ route('ledger.customer.statement', $customer) }}" class="flex gap-2 items-center flex-wrap">
    <label class="text-xs font-semibold text-gray-500">Period:</label>
    <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}">
    <span class="text-gray-400 text-sm">to</span>
    <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}">
    <button type="submit" class="btn-primary text-xs px-3 py-1.5">Apply</button>
  </form>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Left: Profile + stats --}}
  <div class="space-y-5">

    {{-- Customer profile --}}
    <div class="card p-5 space-y-3">
      <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
        <div style="width:40px;height:40px;background:#eff6ff;border:1px solid #dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="20" height="20" fill="none" stroke="#3b82f6" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <div>
          <p class="font-bold text-gray-900 text-sm">{{ $customer->company_name }}</p>
          <p class="text-xs text-gray-400">Customer since {{ $customer->created_at->format('M Y') }}</p>
        </div>
      </div>
      @foreach([
        ['Contact', $customer->contact_name ?? '—'],
        ['Email',   $customer->email ?? '—'],
        ['Phone',   $customer->phone ?? '—'],
      ] as [$label,$value])
      <div class="flex justify-between text-sm">
        <span class="text-gray-500 text-xs">{{ $label }}</span>
        <span class="text-gray-700 text-xs font-medium">{{ $value }}</span>
      </div>
      @endforeach
      <div class="flex justify-between text-sm border-t border-gray-100 pt-2 mt-1">
        <span class="text-gray-500 text-xs">Credit Limit</span>
        <span class="font-semibold text-gray-800">${{ number_format($customer->credit_limit,2) }}</span>
      </div>
    </div>

    {{-- Period summary --}}
    <div class="card p-5 space-y-3">
      <h3 class="font-semibold text-gray-800 text-sm">Period Summary</h3>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Active Trips</span>
        <span class="font-semibold text-gray-800">{{ $totalActive }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Voided Trips</span>
        <span class="font-semibold text-gray-400">{{ $totalVoided }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Total Fare</span>
        <span class="font-semibold text-gray-800">${{ number_format($totalFare,2) }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Tax (8%)</span>
        <span class="font-semibold text-gray-600">${{ number_format($totalTax,2) }}</span>
      </div>
      <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-2">
        <span class="text-gray-800">Total Invoiced</span>
        <span style="color:#16a34a;">${{ number_format($totalInvoiced,2) }}</span>
      </div>
    </div>

    {{-- Monthly breakdown --}}
    @if($byMonth->isNotEmpty())
    <div class="card p-5">
      <h3 class="font-semibold text-gray-800 text-sm mb-3">Monthly Breakdown</h3>
      @php $maxMonth = $byMonth->max('total'); @endphp
      @foreach($byMonth as $m)
      <div class="mb-3">
        <div class="flex justify-between text-xs mb-1">
          <span class="font-medium text-gray-700">{{ $m['label'] }}</span>
          <span class="text-gray-600">{{ $m['count'] }} trips · ${{ number_format($m['total'],2) }}</span>
        </div>
        <div style="height:5px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
          <div style="height:100%;width:{{ $maxMonth>0?round(($m['total']/$maxMonth)*100):0 }}%;background:#6366f1;border-radius:4px;"></div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>

  {{-- Right: Transaction ledger --}}
  <div class="xl:col-span-2 card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-gray-800 text-sm">Trip Statement</h3>
        <p class="text-xs text-gray-400 mt-0.5">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }} &bull; {{ $trips->count() }} records</p>
      </div>
      <a href="{{ route('operations.trips', ['customer_id' => $customer->id]) }}"
         class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View in Operations →</a>
    </div>

    @if($ledger->isEmpty())
    <div class="p-12 text-center text-gray-400 text-sm">
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
        <tbody class="divide-y divide-gray-50">
          @foreach($ledger as $row)
          @php $trip = $row['trip']; @endphp
          <tr class="table-row {{ $trip->status==='voided'?'opacity-60':'' }}">
            <td class="px-4 py-3 font-mono text-xs text-indigo-600 font-semibold"
                style="{{ $trip->status==='voided'?'text-decoration:line-through;':'' }}">
              T-{{ str_pad($trip->id,5,'0',STR_PAD_LEFT) }}
            </td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ $trip->trip_date->format('d M Y') }}</td>
            <td class="px-4 py-3">
              <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">
                {{ $trip->van?->plate_number ?? '—' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ $trip->origin }} → {{ $trip->destination }}</td>
            <td class="px-4 py-3 text-right text-xs text-gray-600">${{ number_format($trip->fare_amount,2) }}</td>
            <td class="px-4 py-3 text-right text-xs text-gray-400">${{ number_format($trip->tax_amount,2) }}</td>
            <td class="px-4 py-3 text-right text-xs font-semibold text-gray-800">${{ number_format($trip->total_amount,2) }}</td>
            <td class="px-4 py-3 text-center">
              @if($trip->status==='active')
                <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
              @else
                <span style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Voided</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right text-xs font-bold text-gray-800">
              {{ $trip->status==='active' ? '$'.number_format($row['balance'],2) : '—' }}
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot style="background:#f8fafc;border-top:2px solid #e5e7eb;">
          <tr>
            <td colspan="6" class="px-4 py-3 font-bold text-gray-700 text-xs">PERIOD TOTAL</td>
            <td class="px-4 py-3 text-right font-bold text-emerald-700">${{ number_format($totalInvoiced,2) }}</td>
            <td></td>
            <td class="px-4 py-3 text-right font-bold text-emerald-700">${{ number_format($totalInvoiced,2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
    @endif
  </div>
</div>
@endsection
