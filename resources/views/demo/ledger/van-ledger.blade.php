@extends('demo.layout')
@section('title','Van-Wise Ledger')
@section('page-title','Ledgers — Van-Wise Profit & Loss')
@section('page-subtitle','Revenue vs expenses breakdown per vehicle for the selected period')

@section('content')
@php
$vans = [
  ['ABC-001','Toyota HiAce','$8,200.00','$5,100.00','$2,258.00','$7,358.00','$842.00',true],
  ['XYZ-202','Nissan NV350','$6,400.00','$4,200.00','$2,033.00','$6,233.00','$167.00',true],
  ['DEF-303','Ford Transit','$5,100.00','$3,800.00','$2,458.00','$6,258.00','-$1,158.00',false],
  ['GHJ-441','Toyota HiAce','$4,800.00','$3,100.00','$2,258.00','$5,358.00','-$558.00',false],
  ['KLM-505','Isuzu NPR','$7,300.00','$4,600.00','$2,658.00','$7,258.00','$42.00',true],
  ['NOP-606','Mitsubishi Canter','$6,100.00','$3,900.00','$1,848.00','$5,748.00','$352.00',true],
  ['QRS-707','Hino 300','$5,500.00','$3,400.00','$2,950.00','$6,350.00','-$850.00',false],
  ['STU-808','Toyota Dyna','$4,920.00','$3,140.00','$2,085.00','$5,225.00','-$305.00',false],
];
@endphp

<div class="space-y-4">
  {{-- Filters --}}
  <div class="card px-5 py-4 flex flex-wrap gap-3 items-center">
    <select class="text-sm"><option>All Vans</option><option>ABC-001</option><option>XYZ-202</option></select>
    <input type="date" class="text-sm" value="2025-06-01">
    <span class="text-slate-400 text-sm">to</span>
    <input type="date" class="text-sm" value="2025-06-30">
    <button class="btn-primary text-sm px-3 py-1.5">Apply Filter</button>
    <button class="btn-ghost text-sm px-3 py-1.5">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Export CSV
    </button>
  </div>

  {{-- Table --}}
  <div class="card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-right">Revenue</th>
          <th class="px-5 py-3 text-right">Variable Expenses</th>
          <th class="px-5 py-3 text-right">Fixed Costs (Prorated)</th>
          <th class="px-5 py-3 text-right">Total Expenses</th>
          <th class="px-5 py-3 text-right">Net Profit / Loss</th>
          <th class="px-5 py-3 text-center">Result</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($vans as $v)
          <tr class="table-row">
            <td class="px-5 py-3.5">
              <span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded font-semibold text-slate-800">{{ $v[0] }}</span>
              <span class="text-xs text-slate-400 ml-2">{{ $v[1] }}</span>
            </td>
            <td class="px-5 py-3.5 text-right font-semibold text-emerald-700">{{ $v[2] }}</td>
            <td class="px-5 py-3.5 text-right text-slate-600">{{ $v[3] }}</td>
            <td class="px-5 py-3.5 text-right text-slate-500 text-xs">{{ $v[4] }}</td>
            <td class="px-5 py-3.5 text-right text-slate-700">{{ $v[5] }}</td>
            <td class="px-5 py-3.5 text-right font-bold {{ $v[7] ? 'text-emerald-700' : 'text-red-600' }}">{{ $v[6] }}</td>
            <td class="px-5 py-3.5 text-center">
              @if($v[7])
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Profit</span>
              @else
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200">Loss</span>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="bg-slate-50 border-t-2 border-slate-200">
          <tr>
            <td class="px-5 py-3 font-bold text-slate-700 text-xs">TOTALS — June 2025</td>
            <td class="px-5 py-3 text-right font-bold text-emerald-700">$48,320.00</td>
            <td class="px-5 py-3 text-right font-bold text-slate-700">$31,240.00</td>
            <td class="px-5 py-3 text-right font-bold text-slate-600 text-xs">$19,548.00</td>
            <td class="px-5 py-3 text-right font-bold text-slate-700">$44,990.00</td>
            <td class="px-5 py-3 text-right font-bold text-indigo-700">$3,330.00</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
