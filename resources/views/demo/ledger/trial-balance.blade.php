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
        <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
        <input type="date" name="from" class="text-sm" value="{{ $from->format('Y-m-d') }}">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
        <input type="date" name="to" class="text-sm" value="{{ $to->format('Y-m-d') }}">
      </div>
      <button type="submit" class="btn-primary text-xs self-end px-3 py-2">Generate</button>
      <a href="{{ route('ledger.trial-balance') }}" class="btn-ghost text-xs self-end px-3 py-2">Reset</a>
    </div>
    <div class="self-end">
      @if($balanced)
        <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;"
              class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          Balanced — Debits = Credits
        </span>
      @else
        <span style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;"
              class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          Unbalanced — Discrepancy: ${{ number_format(abs($totalDebit - $totalCredit),2) }}
        </span>
      @endif
    </div>
  </form>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Debits</p>
    <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($totalDebit,2) }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Credits</p>
    <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($totalCredit,2) }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Difference</p>
    <p class="text-2xl font-bold mt-1" style="color:{{ $balanced ? '#16a34a' : '#ef4444' }};">
      ${{ number_format(abs($totalDebit - $totalCredit),2) }}
    </p>
    <p class="text-xs mt-0.5" style="color:{{ $balanced ? '#16a34a' : '#ef4444' }};">
      {{ $balanced ? '✓ Balanced' : '⚠ Discrepancy' }}
    </p>
  </div>
</div>

{{-- Trial balance table --}}
<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-gray-100">
    <h3 class="font-semibold text-gray-800 text-sm">Trial Balance — {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}</h3>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Account Name</th>
          <th class="px-5 py-3 text-left">Type</th>
          <th class="px-5 py-3 text-right">Debit ($)</th>
          <th class="px-5 py-3 text-right">Credit ($)</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @php
        $typeColors = [
          'Revenue'  => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
          'Asset'    => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;',
          'Expense'  => 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;',
          'Equity'   => 'background:#f5f3ff;color:#5b21b6;border:1px solid #ddd6fe;',
          'Liability'=> 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
        ];
        @endphp
        @forelse($accounts as $acc)
        <tr class="table-row">
          <td class="px-5 py-3.5 font-medium text-gray-800">{{ $acc['name'] }}</td>
          <td class="px-5 py-3.5">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="{{ $typeColors[$acc['type']] ?? 'background:#f3f4f6;color:#374151;' }}">
              {{ $acc['type'] }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-right font-mono {{ $acc['debit'] > 0 ? 'font-semibold text-gray-800' : 'text-gray-300' }}">
            {{ $acc['debit'] > 0 ? '$'.number_format($acc['debit'],2) : '—' }}
          </td>
          <td class="px-5 py-3.5 text-right font-mono {{ $acc['credit'] > 0 ? 'font-semibold text-gray-800' : 'text-gray-300' }}">
            {{ $acc['credit'] > 0 ? '$'.number_format($acc['credit'],2) : '—' }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">
            No financial activity found for the selected period.
          </td>
        </tr>
        @endforelse
      </tbody>

      {{-- Totals row --}}
      @if(count($accounts) > 0)
      <tfoot style="border-top:2px solid {{ $balanced ? '#bbf7d0' : '#fecaca' }};background:{{ $balanced ? '#f0fdf4' : '#fef2f2' }};">
        <tr>
          <td class="px-5 py-4 font-bold text-gray-800">TOTALS</td>
          <td class="px-5 py-4">
            @if($balanced)
              <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2 py-0.5 rounded-full">✓ Balanced</span>
            @else
              <span style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;" class="text-xs font-semibold px-2 py-0.5 rounded-full">⚠ Unbalanced</span>
            @endif
          </td>
          <td class="px-5 py-4 text-right font-extrabold text-lg text-gray-900 font-mono">
            ${{ number_format($totalDebit,2) }}
          </td>
          <td class="px-5 py-4 text-right font-extrabold text-lg text-gray-900 font-mono">
            ${{ number_format($totalCredit,2) }}
          </td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>

<p class="text-xs text-gray-400 mt-3">
  * This trial balance is computed from actual transaction records (trips, expenses, advances, wages, capital injections and distributions) for the selected period.
</p>
@endsection
