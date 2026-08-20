@extends('demo.layout')
@section('title', $customer->company_name)
@section('page-title', $customer->company_name)
@section('page-subtitle', 'Customer profile and trip history')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('crm.customers') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
    Customers
  </a>
  <span class="text-gray-300">/</span>
  <span class="text-gray-700 font-semibold">{{ $customer->company_name }}</span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Profile card --}}
  <div class="card p-5 space-y-4">
    <div class="flex items-center gap-3">
      <div style="width:44px;height:44px;background:#eff6ff;border:1px solid #dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="22" height="22" fill="none" stroke="#3b82f6" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
      </div>
      <div>
        <h3 class="font-bold text-gray-900 text-base">{{ $customer->company_name }}</h3>
        <p class="text-xs text-gray-400 mt-0.5">Customer since {{ $customer->created_at->format('M Y') }}</p>
      </div>
    </div>

    <div class="space-y-2.5 text-sm">
      <div class="flex items-start gap-2">
        <span class="text-gray-400 w-20 flex-shrink-0 text-xs font-semibold uppercase">Contact</span>
        <span class="text-gray-700">{{ $customer->contact_name ?? '—' }}</span>
      </div>
      <div class="flex items-start gap-2">
        <span class="text-gray-400 w-20 flex-shrink-0 text-xs font-semibold uppercase">Email</span>
        <span class="text-gray-700 break-all">{{ $customer->email ?? '—' }}</span>
      </div>
      <div class="flex items-start gap-2">
        <span class="text-gray-400 w-20 flex-shrink-0 text-xs font-semibold uppercase">Phone</span>
        <span class="text-gray-700">{{ $customer->phone ?? '—' }}</span>
      </div>
      <div class="flex items-start gap-2">
        <span class="text-gray-400 w-20 flex-shrink-0 text-xs font-semibold uppercase">Address</span>
        <span class="text-gray-700 text-xs">{{ $customer->billing_address ?? '—' }}</span>
      </div>
    </div>

    <div class="border-t border-gray-100 pt-4 space-y-2">
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Credit Limit</span>
        <span class="font-semibold text-gray-800">${{ number_format($customer->credit_limit, 2) }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Total Invoiced</span>
        <span class="font-semibold text-gray-800">${{ number_format($totalInvoiced, 2) }}</span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="text-gray-500">Total Trips</span>
        <span class="font-semibold text-gray-800">{{ $totalTrips }}</span>
      </div>
      @php $utilisation = $customer->credit_limit > 0 ? min(100, ($totalInvoiced / $customer->credit_limit) * 100) : 0; @endphp
      <div class="mt-2">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
          <span>Credit Utilisation</span>
          <span>{{ number_format($utilisation, 1) }}%</span>
        </div>
        <div style="height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
          <div style="height:100%;width:{{ $utilisation }}%;background:{{ $utilisation > 80 ? '#ef4444' : ($utilisation > 60 ? '#f59e0b' : '#6366f1') }};border-radius:4px;transition:width .3s;"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- Trip history --}}
  <div class="xl:col-span-2 card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
      <h3 class="font-semibold text-gray-800 text-sm">Trip History</h3>
      <p class="text-xs text-gray-400 mt-0.5">Last 20 trips</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-4 py-3 text-left">Trip ID</th>
          <th class="px-4 py-3 text-left">Date</th>
          <th class="px-4 py-3 text-left">Van</th>
          <th class="px-4 py-3 text-left">Route</th>
          <th class="px-4 py-3 text-right">Total</th>
          <th class="px-4 py-3 text-center">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($customer->trips as $trip)
          <tr class="table-row {{ $trip->status==='voided'?'opacity-60':'' }}">
            <td class="px-4 py-3 font-mono text-xs text-indigo-600 font-semibold">T-{{ str_pad($trip->id,5,'0',STR_PAD_LEFT) }}</td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ $trip->trip_date->format('d M Y') }}</td>
            <td class="px-4 py-3">
              <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $trip->van->plate_number ?? '—' }}</span>
            </td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ $trip->origin }} → {{ $trip->destination }}</td>
            <td class="px-4 py-3 text-right font-semibold text-gray-800">${{ number_format($trip->total_amount,2) }}</td>
            <td class="px-4 py-3 text-center">
              @if($trip->status==='active')
                <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
              @else
                <span style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Voided</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 text-xs">No trips recorded yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-5 py-3.5 border-t border-gray-100 flex justify-between text-xs text-gray-400">
      <span>Showing last {{ $customer->trips->count() }} trips</span>
      <a href="{{ route('operations.trips', ['customer_id' => $customer->id]) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View all trips →</a>
    </div>
  </div>
</div>
@endsection
