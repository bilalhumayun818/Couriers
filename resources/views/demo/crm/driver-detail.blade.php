@extends('demo.layout')
@section('title', $driver->full_name)
@section('page-title', $driver->full_name)
@section('page-subtitle', 'Driver profile, assignments, advances and wage history')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('crm.drivers') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
    Drivers
  </a>
  <span class="text-gray-300">/</span>
  <span class="text-gray-700 font-semibold">{{ $driver->full_name }}</span>
</div>

{{-- Alerts --}}
@if($licenceExpired)
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ⚠ Licence EXPIRED {{ abs($daysToExpiry) }} days ago ({{ \Carbon\Carbon::parse($driver->licence_expiry_date)->format('d M Y') }}) — renewal required.
</div>
@elseif($licenceExpiringSoon)
<div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ⚠ Licence expires in {{ abs($daysToExpiry) }} days ({{ \Carbon\Carbon::parse($driver->licence_expiry_date)->format('d M Y') }}).
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Profile card --}}
  <div class="card p-5 space-y-4">
    <div class="flex items-center gap-3">
      <div style="width:44px;height:44px;background:#eff6ff;border:1px solid #dbeafe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="22" height="22" fill="none" stroke="#3b82f6" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
      </div>
      <div>
        <h3 class="font-bold text-gray-900 text-base">{{ $driver->full_name }}</h3>
        <p class="text-xs text-gray-400 mt-0.5">Driver since {{ $driver->created_at->format('M Y') }}</p>
      </div>
    </div>

    <div class="space-y-2.5 text-sm">
      @foreach([
        ['National ID', $driver->national_id ?? '—'],
        ['Licence No.', $driver->licence_number],
        ['Licence Expiry', \Carbon\Carbon::parse($driver->licence_expiry_date)->format('d M Y')],
        ['Contact', $driver->contact_number ?? '—'],
        ['Emergency', $driver->emergency_contact ?? '—'],
      ] as [$label, $value])
      <div class="flex items-start gap-2">
        <span class="text-gray-400 w-24 flex-shrink-0 text-xs font-semibold uppercase">{{ $label }}</span>
        <span class="text-gray-700 text-xs">{{ $value }}</span>
      </div>
      @endforeach
    </div>

    {{-- Current van assignment --}}
    <div class="border-t border-gray-100 pt-4">
      <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Current Assignment</p>
      @if($currentAssignment)
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;">
          <p class="font-mono font-bold text-gray-800 text-sm">{{ $currentAssignment->van->plate_number }}</p>
          <p class="text-xs text-gray-500 mt-0.5">{{ $currentAssignment->van->make_model }}</p>
          <p class="text-xs text-gray-400 mt-1">Since {{ \Carbon\Carbon::parse($currentAssignment->start_date)->format('d M Y') }}</p>
        </div>
      @else
        <p class="text-xs text-gray-400">Not currently assigned to any van.</p>
      @endif
    </div>
  </div>

  {{-- Right column: assignment history + advances + payouts --}}
  <div class="xl:col-span-2 space-y-5">

    {{-- Assignment history --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Assignment History</h3>
      </div>
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-4 py-3 text-left">Van</th>
          <th class="px-4 py-3 text-left">Start Date</th>
          <th class="px-4 py-3 text-left">End Date</th>
          <th class="px-4 py-3 text-center">Status</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($driver->assignments as $asgn)
          <tr class="table-row {{ $asgn->end_date ? 'opacity-70' : '' }}">
            <td class="px-4 py-3"><span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $asgn->van->plate_number }}</span></td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ \Carbon\Carbon::parse($asgn->start_date)->format('d M Y') }}</td>
            <td class="px-4 py-3 text-xs text-gray-500">{{ $asgn->end_date ? \Carbon\Carbon::parse($asgn->end_date)->format('d M Y') : '—' }}</td>
            <td class="px-4 py-3 text-center">
              @if(!$asgn->end_date)
                <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
              @else
                <span style="background:#f9fafb;color:#6b7280;border:1px solid #e5e7eb;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Ended</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 text-xs">No assignment history.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Advances + Payouts side by side --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

      {{-- Advances --}}
      <div class="card overflow-hidden">
        <div class="px-4 py-3.5 border-b border-gray-100">
          <h3 class="font-semibold text-gray-800 text-sm">Recent Advances</h3>
        </div>
        <table class="w-full text-sm">
          <thead><tr class="table-header">
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Purpose</th>
            <th class="px-4 py-3 text-right">Amount</th>
          </tr></thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($advances as $adv)
            <tr class="table-row">
              <td class="px-4 py-3 text-xs text-gray-500">{{ $adv->advance_date->format('d M Y') }}</td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ $adv->purpose ?? '—' }}</td>
              <td class="px-4 py-3 text-right text-red-500 font-semibold text-xs">${{ number_format($adv->amount,2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400 text-xs">No advances.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Wage payouts --}}
      <div class="card overflow-hidden">
        <div class="px-4 py-3.5 border-b border-gray-100">
          <h3 class="font-semibold text-gray-800 text-sm">Wage Payout History</h3>
        </div>
        <table class="w-full text-sm">
          <thead><tr class="table-header">
            <th class="px-4 py-3 text-left">Period</th>
            <th class="px-4 py-3 text-right">Gross</th>
            <th class="px-4 py-3 text-right">Net</th>
          </tr></thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($payouts as $payout)
            <tr class="table-row">
              <td class="px-4 py-3 text-xs text-gray-600 font-medium">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $payout->pay_period)->format('M Y') }}
              </td>
              <td class="px-4 py-3 text-right text-xs text-gray-600">${{ number_format($payout->gross_wage,2) }}</td>
              <td class="px-4 py-3 text-right text-xs font-bold {{ $payout->is_negative ? 'text-red-600' : 'text-emerald-700' }}">
                {{ $payout->is_negative ? '−' : '' }}${{ number_format(abs($payout->net_wage),2) }}
              </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400 text-xs">No payouts yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
