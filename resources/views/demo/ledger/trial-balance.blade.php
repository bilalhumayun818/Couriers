@extends('demo.layout')
@section('title','Trial Balance')
@section('page-title','Financial Statements — Trial Balance')
@section('page-subtitle','Verify total debits equal total credits for the selected period')

@section('content')

{{-- Filter bar --}}
<div class="card px-5 py-4 mb-5">
  <form method="GET" action="{{ route('ledger.trial-balance') }}"
        class="flex flex-wrap gap-3 items-end justify-between">
    <div class="flex flex-wrap gap-2 items-end">
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">From</label>
        <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-400 mb-1">To</label>
        <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}">
      </div>
      <button type="submit" class="btn-primary text-xs self-end px-3 py-2">Generate</button>
      <a href="{{ route('ledger.trial-balance') }}" class="btn-ghost text-xs self-end px-3 py-2">Reset</a>
    </div>
    <div class="self-end">
      @if($balanced)
        <span style="background:rgba(56,189,248,0.12);color:#7dd3fc;border:1px solid rgba(56,189,248,0.3);"
              class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          Balanced — Debits = Credits
        </span>
      @else
        <span style="background:rgba(56,189,248,0.12);color:#38bdf8;border:1px solid rgba(56,189,248,0.3);"
              class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          Unbalanced — Discrepancy: {{ $currencySymbol }}{{ number_format(abs($totalDebit - $totalCredit),2) }}
        </span>
      @endif
    </div>
  </form>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Debits</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $currencySymbol }}{{ number_format($totalDebit,2) }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Credits</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $currencySymbol }}{{ number_format($totalCredit,2) }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Difference</p>
    <p class="text-2xl font-bold mt-1 {{ $balanced ? 'text-sky-400' : 'text-sky-400' }}">
      {{ $currencySymbol }}{{ number_format(abs($totalDebit - $totalCredit),2) }}
    </p>
    <p class="text-xs mt-0.5 {{ $balanced ? 'text-sky-400' : 'text-sky-400' }}">
      {{ $balanced ? '✓ Balanced' : '⚠ Discrepancy' }}
    </p>
  </div>
</div>

{{-- Trial balance table --}}
<div class="card overflow-hidden">
  <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <h3 class="font-semibold text-slate-100 text-sm">Trial Balance — {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Account Name</th>
          <th class="px-5 py-3 text-left">Type</th>
          <th class="px-5 py-3 text-right">Debit ({{ $currencySymbol }})</th>
          <th class="px-5 py-3 text-right">Credit ({{ $currencySymbol }})</th>
        </tr>
      </thead>
      <tbody>
        @php
        $typeColors = [
          'Revenue'  => 'background:rgba(56,189,248,0.15);color:#7dd3fc;border:1px solid rgba(56,189,248,0.3);',
          'Asset'    => 'background:rgba(56,189,248,0.15);color:#38bdf8;border:1px solid rgba(56,189,248,0.3);',
          'Expense'  => 'background:rgba(56,189,248,0.15);color:#38bdf8;border:1px solid rgba(56,189,248,0.3);',
          'Equity'   => 'background:rgba(56,189,248,0.15);color:#93c5fd;border:1px solid rgba(56,189,248,0.3);',
          'Liability'=> 'background:rgba(56,189,248,0.15);color:#7dd3fc;border:1px solid rgba(56,189,248,0.3);',
        ];
        @endphp
        @forelse($accounts as $acc)
        <tr class="table-row">
          <td class="px-5 py-3.5 font-medium text-slate-200">{{ $acc['name'] }}</td>
          <td class="px-5 py-3.5">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="{{ $typeColors[$acc['type']] ?? 'background:rgba(255,255,255,0.06);color:#cbd5e1;' }}">
              {{ $acc['type'] }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-right font-mono {{ $acc['debit'] > 0 ? 'font-semibold text-slate-100' : 'text-slate-600' }}">
            {{ $acc['debit'] > 0 ? '$'.number_format($acc['debit'],2) : '—' }}
          </td>
          <td class="px-5 py-3.5 text-right font-mono {{ $acc['credit'] > 0 ? 'font-semibold text-slate-100' : 'text-slate-600' }}">
            {{ $acc['credit'] > 0 ? '$'.number_format($acc['credit'],2) : '—' }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-5 py-12 text-center text-slate-400 text-sm">
            No financial activity found for the selected period.
          </td>
        </tr>
        @endforelse
      </tbody>

      {{-- Totals row --}}
      @if(count($accounts) > 0)
      <tfoot style="border-top:2px solid {{ $balanced ? 'rgba(56,189,248,0.4)' : 'rgba(56,189,248,0.4)' }};background:{{ $balanced ? 'rgba(56,189,248,0.1)' : 'rgba(56,189,248,0.1)' }};">
        <tr>
          <td class="px-5 py-4 font-bold text-slate-100">TOTALS</td>
          <td class="px-5 py-4">
            @if($balanced)
              <span style="background:rgba(56,189,248,0.15);color:#7dd3fc;border:1px solid rgba(56,189,248,0.3);" class="text-xs font-semibold px-2 py-0.5 rounded-full">✓ Balanced</span>
            @else
              <span style="background:rgba(56,189,248,0.15);color:#38bdf8;border:1px solid rgba(56,189,248,0.3);" class="text-xs font-semibold px-2 py-0.5 rounded-full">⚠ Unbalanced</span>
            @endif
          </td>
          <td class="px-5 py-4 text-right font-extrabold text-lg text-slate-100 font-mono">
            {{ $currencySymbol }}{{ number_format($totalDebit,2) }}
          </td>
          <td class="px-5 py-4 text-right font-extrabold text-lg text-slate-100 font-mono">
            {{ $currencySymbol }}{{ number_format($totalCredit,2) }}
          </td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>

<p class="text-xs text-slate-400 mt-3">
  * This trial balance is computed from actual transaction records (trips, expenses, advances, wages, capital injections and distributions) for the selected period.
</p>
@endsection
