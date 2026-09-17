@extends('demo.layout')
@section('title', $driver->full_name)
@section('page-title', $driver->full_name)
@section('page-subtitle', 'Driver profile, assignments, advances and wage history')

@section('content')

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('crm.drivers') }}" class="text-sky-400 hover:text-sky-300 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
    Drivers
  </a>
  <span class="text-slate-600">/</span>
  <span class="text-slate-200 font-semibold">{{ $driver->full_name }}</span>
</div>

{{-- Alerts --}}
@if($licenceExpired)
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  Licence EXPIRED {{ abs($daysToExpiry) }} days ago ({{ \Carbon\Carbon::parse($driver->licence_expiry_date)->format('d M Y') }}) — renewal required.
</div>
@elseif($licenceExpiringSoon)
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.3);color:#7dd3fc;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  Licence expires in {{ abs($daysToExpiry) }} days ({{ \Carbon\Carbon::parse($driver->licence_expiry_date)->format('d M Y') }}).
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

  {{-- Profile card --}}
  <div class="card p-5 space-y-4">
    <div class="flex items-center gap-3">
      <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.25);">
        <svg width="22" height="22" fill="none" stroke="#38bdf8" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
      </div>
      <div>
        <h3 class="font-bold text-slate-100 text-base">{{ $driver->full_name }}</h3>
        <p class="text-xs text-slate-400 mt-0.5">Driver since {{ $driver->created_at->format('M Y') }}</p>
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
        <span class="text-slate-500 w-24 flex-shrink-0 text-xs font-semibold uppercase">{{ $label }}</span>
        <span class="text-slate-200 text-xs">{{ $value }}</span>
      </div>
      @endforeach
    </div>

    {{-- Current van assignment --}}
    <div class="pt-4" style="border-top:1px solid rgba(255,255,255,0.07);">
      <p class="text-xs font-semibold text-slate-400 uppercase mb-2">Current Assignment</p>
      @if($currentAssignment)
        <div style="background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.25);border-radius:8px;padding:10px 12px;">
          <p class="font-mono font-bold text-slate-100 text-sm">{{ $currentAssignment->van->plate_number }}</p>
          <p class="text-xs text-slate-300 mt-0.5">{{ $currentAssignment->van->make_model }}</p>
          <p class="text-xs text-slate-400 mt-1">Since {{ \Carbon\Carbon::parse($currentAssignment->start_date)->format('d M Y') }}</p>
        </div>
      @else
        <p class="text-xs text-slate-500">Not currently assigned to any van.</p>
      @endif
    </div>
  </div>

  {{-- Right column: assignment history + advances + payouts --}}
  <div class="xl:col-span-2 space-y-5">

    {{-- Assignment history --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
        <h3 class="font-semibold text-slate-100 text-sm">Assignment History</h3>
      </div>
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-4 py-3 text-left">Van</th>
          <th class="px-4 py-3 text-left">Start Date</th>
          <th class="px-4 py-3 text-left">End Date</th>
          <th class="px-4 py-3 text-center">Status</th>
        </tr></thead>
        <tbody>
          @forelse($driver->assignments as $asgn)
          <tr class="table-row {{ $asgn->end_date ? 'opacity-60' : '' }}">
            <td class="px-4 py-3"><span class="font-mono text-xs px-2 py-0.5 rounded" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;">{{ $asgn->van->plate_number }}</span></td>
            <td class="px-4 py-3 text-xs text-slate-400">{{ \Carbon\Carbon::parse($asgn->start_date)->format('d M Y') }}</td>
            <td class="px-4 py-3 text-xs text-slate-400">{{ $asgn->end_date ? \Carbon\Carbon::parse($asgn->end_date)->format('d M Y') : '—' }}</td>
            <td class="px-4 py-3 text-center">
              @if(!$asgn->end_date)
                <span class="badge-blue">Active</span>
              @else
                <span class="badge-slate">Ended</span>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 text-xs">No assignment history.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Advances + Payouts side by side --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

      {{-- Advances --}}
      <div class="card overflow-hidden">
        <div class="px-4 py-3.5" style="border-bottom:1px solid rgba(255,255,255,0.07);">
          <h3 class="font-semibold text-slate-100 text-sm">Recent Advances</h3>
        </div>
        <table class="w-full text-sm">
          <thead><tr class="table-header">
            <th class="px-4 py-3 text-left">Date</th>
            <th class="px-4 py-3 text-left">Purpose</th>
            <th class="px-4 py-3 text-right">Amount</th>
          </tr></thead>
          <tbody>
            @forelse($advances as $adv)
            <tr class="table-row">
              <td class="px-4 py-3 text-xs text-slate-400">{{ $adv->advance_date->format('d M Y') }}</td>
              <td class="px-4 py-3 text-xs text-slate-300">{{ $adv->purpose ?? '—' }}</td>
              <td class="px-4 py-3 text-right text-red-400 font-semibold text-xs">{{ $currencySymbol }}{{ number_format($adv->amount,2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500 text-xs">No advances.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Wage payouts --}}
      <div class="card overflow-hidden">
        <div class="px-4 py-3.5" style="border-bottom:1px solid rgba(255,255,255,0.07);">
          <h3 class="font-semibold text-slate-100 text-sm">Wage Payout History</h3>
        </div>
        <table class="w-full text-sm">
          <thead><tr class="table-header">
            <th class="px-4 py-3 text-left">Period</th>
            <th class="px-4 py-3 text-right">Gross</th>
            <th class="px-4 py-3 text-right">Net</th>
          </tr></thead>
          <tbody>
            @forelse($payouts as $payout)
            <tr class="table-row">
              <td class="px-4 py-3 text-xs text-slate-300 font-medium">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $payout->pay_period)->format('M Y') }}
              </td>
              <td class="px-4 py-3 text-right text-xs text-slate-400">{{ $currencySymbol }}{{ number_format($payout->gross_wage,2) }}</td>
              <td class="px-4 py-3 text-right text-xs font-bold {{ $payout->is_negative ? 'text-red-400' : 'text-emerald-400' }}">
                {{ $payout->is_negative ? '−' : '' }}{{ $currencySymbol }}{{ number_format(abs($payout->net_wage),2) }}
              </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500 text-xs">No payouts yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

