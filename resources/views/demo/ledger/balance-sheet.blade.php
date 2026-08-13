@extends('demo.layout')
@section('title','Balance Sheet')
@section('page-title','Financial Statements — Balance Sheet')
@section('page-subtitle','Assets = Liabilities + Equity as of selected date')

@section('content')
<div class="space-y-4">
  <div class="card px-5 py-4 flex flex-wrap gap-3 items-center">
    <label class="text-sm text-slate-600 font-medium">As of date:</label>
    <input type="date" class="text-sm" value="2025-06-30">
    <button class="btn-primary text-sm px-3 py-1.5">Generate</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export CSV</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export PDF</button>
    <span class="ml-auto inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-200">
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
      Balanced: Assets = Liabilities + Equity
    </span>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    {{-- Assets --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-3.5 bg-indigo-600">
        <h3 class="font-semibold text-white text-sm">Assets</h3>
      </div>
      <div class="divide-y divide-slate-50">
        <div class="px-5 py-3">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Current Assets</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Cash / Bank</span><span class="font-medium text-slate-800">$10,860.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Accounts Receivable</span><span class="font-medium text-slate-800">$400.00</span></div>
        </div>
        <div class="px-5 py-3">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Non-Current Assets</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Fleet Vehicles (Net)</span><span class="font-medium text-slate-800">$136,000.00</span></div>
        </div>
        <div class="px-5 py-3.5 bg-indigo-50">
          <div class="flex justify-between font-bold"><span class="text-slate-800">Total Assets</span><span class="text-indigo-700 text-base">$147,260.00</span></div>
        </div>
      </div>
    </div>

    {{-- Liabilities --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-3.5 bg-slate-700">
        <h3 class="font-semibold text-white text-sm">Liabilities</h3>
      </div>
      <div class="divide-y divide-slate-50">
        <div class="px-5 py-3">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Current Liabilities</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Accounts Payable</span><span class="font-medium text-slate-800">$2,500.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Accrued Expenses</span><span class="font-medium text-slate-800">$2,200.00</span></div>
        </div>
        <div class="px-5 py-3">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Non-Current Liabilities</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Long-term Lease</span><span class="font-medium text-slate-800">$0.00</span></div>
        </div>
        <div class="px-5 py-3.5 bg-slate-50">
          <div class="flex justify-between font-bold"><span class="text-slate-800">Total Liabilities</span><span class="text-slate-700 text-base">$4,700.00</span></div>
        </div>
      </div>
    </div>

    {{-- Equity --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-3.5 bg-emerald-700">
        <h3 class="font-semibold text-white text-sm">Equity</h3>
      </div>
      <div class="divide-y divide-slate-50">
        <div class="px-5 py-3">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Shareholders' Equity</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Capital Contributions</span><span class="font-medium text-slate-800">$140,000.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Equity Distributions</span><span class="font-medium text-red-600">−$29,500.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-600">Retained Earnings</span><span class="font-medium text-emerald-700">$32,060.00</span></div>
        </div>
        <div class="px-5 py-3.5 bg-emerald-50">
          <div class="flex justify-between font-bold"><span class="text-slate-800">Total Equity</span><span class="text-emerald-700 text-base">$142,560.00</span></div>
        </div>
      </div>
      <div class="px-5 py-4 bg-indigo-50 border-t-2 border-indigo-200">
        <div class="flex justify-between font-bold text-indigo-800">
          <span>Liabilities + Equity</span>
          <span class="text-base">$147,260.00</span>
        </div>
        <p class="text-xs text-emerald-600 mt-1 font-medium">✓ Matches Total Assets</p>
      </div>
    </div>
  </div>
</div>
@endsection
