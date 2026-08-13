@extends('demo.layout')
@section('title','Trial Balance')
@section('page-title','Financial Statements — Trial Balance')
@section('page-subtitle','Verify total debits equal total credits for the selected period')

@section('content')
@php
$accounts = [
  ['Revenue','Revenue','—','$48,320.00','48,320.00'],
  ['Accounts Receivable','Asset','$48,320.00','$47,920.00','400.00'],
  ['Cash / Bank','Asset','$47,920.00','$37,060.00','10,860.00'],
  ['Fuel','Expense','$11,240.00','—','11,240.00'],
  ['Tolls','Expense','$1,840.00','—','1,840.00'],
  ['Spare Parts','Expense','$3,200.00','—','3,200.00'],
  ['Maintenance/Repairs','Expense','$4,960.00','—','4,960.00'],
  ['Monthly Lease','Expense','$14,400.00','—','14,400.00'],
  ['Road Tax','Expense','$3,600.00','—','3,600.00'],
  ['Insurance','Expense','$920.00','—','920.00'],
  ['Driver Wages','Expense','$5,625.00','—','5,625.00'],
  ['Driver Advances','Expense','$305.00','—','305.00'],
  ['Capital','Equity','—','$140,000.00','140,000.00'],
  ['Equity Distributions','Equity','$29,500.00','—','29,500.00'],
];
@endphp

<div class="space-y-4">
  {{-- Filter bar --}}
  <div class="card px-5 py-4 flex flex-wrap gap-3 items-center">
    <input type="date" class="text-sm" value="2025-06-01">
    <span class="text-slate-400 text-sm">to</span>
    <input type="date" class="text-sm" value="2025-06-30">
    <button class="btn-primary text-sm px-3 py-1.5">Generate</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export CSV</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export PDF</button>
    <span class="ml-auto inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-200">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      Balanced — Debits = Credits
    </span>
  </div>

  <div class="card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-3 text-left">Account Name</th>
          <th class="px-5 py-3 text-left">Type</th>
          <th class="px-5 py-3 text-right">Total Debit</th>
          <th class="px-5 py-3 text-right">Total Credit</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($accounts as $a)
          <tr class="table-row">
            <td class="px-5 py-3.5 font-medium text-slate-800">{{ $a[0] }}</td>
            <td class="px-5 py-3.5">
              @php
              $tc=['Revenue'=>'bg-emerald-50 text-emerald-700 border-emerald-200','Asset'=>'bg-indigo-50 text-indigo-700 border-indigo-200','Expense'=>'bg-red-50 text-red-600 border-red-200','Equity'=>'bg-slate-100 text-slate-600 border-slate-200','Liability'=>'bg-amber-50 text-amber-700 border-amber-200'];
              @endphp
              <span class="text-xs font-medium px-2.5 py-0.5 rounded-full border {{ $tc[$a[1]] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">{{ $a[1] }}</span>
            </td>
            <td class="px-5 py-3.5 text-right font-mono text-slate-700">{{ $a[2] }}</td>
            <td class="px-5 py-3.5 text-right font-mono text-slate-700">{{ $a[3] }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-slate-50 border-t-2 border-slate-300">
          <tr>
            <td class="px-5 py-3.5 font-bold text-slate-800" colspan="2">TOTALS</td>
            <td class="px-5 py-3.5 text-right font-bold text-indigo-700 text-base">$166,110.00</td>
            <td class="px-5 py-3.5 text-right font-bold text-indigo-700 text-base">$166,110.00</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
