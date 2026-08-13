@extends('demo.layout')
@section('title','Customer Ledger')
@section('page-title','Ledgers — Customer Account Statement')
@section('page-subtitle','Bookings, payments and outstanding balances per customer')

@section('content')
@php
$summary = [
  ['Swift Retail Co','12','$5,200.00','$4,800.00','$400.00'],
  ['Global Traders Ltd','8','$3,600.00','$2,100.00','$1,500.00'],
  ['Metro Supplies','6','$2,800.00','$2,600.00','$200.00'],
  ['Apex Importers','10','$4,900.00','$4,700.00','$200.00'],
  ['Zenith Cargo','5','$2,100.00','-','$2,100.00'],
];
$txns = [
  ['17 Jun 2025','T-00124','Trip — Nairobi→Mombasa','$453.60','—','$400.00'],
  ['15 Jun 2025','T-00120','Trip — Mombasa→Nairobi','$432.00','—','$846.00'],
  ['10 Jun 2025','PMT-0041','Payment Received','—','$432.00','$414.00'],
  ['08 Jun 2025','T-00115','Trip — Nairobi→Kisumu','$378.00','—','$846.00'],
  ['05 Jun 2025','PMT-0038','Payment Received','—','$756.00','$468.00'],
  ['02 Jun 2025','T-00109','Trip — Kisumu→Nairobi','$421.20','—','$1,224.00'],
];
@endphp

<div class="space-y-4">
  {{-- Summary cards --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-wrap gap-3 items-center justify-between">
      <div class="flex gap-2">
        <select class="text-sm"><option>Swift Retail Co</option><option>Global Traders Ltd</option><option>Metro Supplies</option></select>
        <input type="date" class="text-sm" value="2025-06-01">
        <span class="text-slate-400 text-sm">to</span>
        <input type="date" class="text-sm" value="2025-06-30">
        <button class="btn-primary text-sm px-3 py-1.5">Apply</button>
      </div>
      <div class="flex gap-2">
        <button class="btn-ghost text-sm px-3 py-1.5">Export CSV</button>
        <button class="btn-ghost text-sm px-3 py-1.5">Export PDF</button>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-3 text-left">Customer</th>
          <th class="px-5 py-3 text-right">Trips</th>
          <th class="px-5 py-3 text-right">Invoiced</th>
          <th class="px-5 py-3 text-right">Paid</th>
          <th class="px-5 py-3 text-right">Outstanding</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($summary as $s)
          <tr class="table-row">
            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $s[0] }}</td>
            <td class="px-5 py-3.5 text-right text-slate-600">{{ $s[1] }}</td>
            <td class="px-5 py-3.5 text-right text-slate-600">{{ $s[2] }}</td>
            <td class="px-5 py-3.5 text-right text-emerald-700 font-medium">{{ $s[3] }}</td>
            <td class="px-5 py-3.5 text-right font-bold {{ $s[4] !== '$200.00' && $s[4] !== '$400.00' ? 'text-red-600' : 'text-slate-700' }}">{{ $s[4] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Transaction detail for selected customer --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-800 text-sm">Statement — Swift Retail Co &bull; June 2025</h3>
      <p class="text-xs text-slate-400 mt-0.5">Running balance shown right-to-left</p>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Reference</th>
          <th class="px-5 py-3 text-left">Description</th>
          <th class="px-5 py-3 text-right">Debit</th>
          <th class="px-5 py-3 text-right">Credit</th>
          <th class="px-5 py-3 text-right">Balance</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($txns as $tx)
          <tr class="table-row">
            <td class="px-5 py-3.5 text-xs text-slate-500">{{ $tx[0] }}</td>
            <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $tx[1] }}</td>
            <td class="px-5 py-3.5 text-slate-700">{{ $tx[2] }}</td>
            <td class="px-5 py-3.5 text-right {{ $tx[3] !== '—' ? 'text-slate-800 font-semibold' : 'text-slate-300' }}">{{ $tx[3] }}</td>
            <td class="px-5 py-3.5 text-right {{ $tx[4] !== '—' ? 'text-emerald-700 font-semibold' : 'text-slate-300' }}">{{ $tx[4] }}</td>
            <td class="px-5 py-3.5 text-right font-bold text-slate-800">{{ $tx[5] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
