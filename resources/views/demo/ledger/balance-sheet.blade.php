@extends('demo.layout')
@section('title','Balance Sheet')
@section('page-title','Financial Statements — Balance Sheet')
@section('page-subtitle','Assets = Liabilities + Equity as of selected date')

@section('content')
<div class="space-y-4">
  <div class="card px-5 py-4 flex flex-wrap gap-3 items-center">
    <label class="text-sm text-slate-300 font-medium">As of date:</label>
    <input type="date" class="text-sm" value="2025-06-30">
    <button class="btn-primary text-sm px-3 py-1.5">Generate</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export CSV</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export PDF</button>
    <span class="ml-auto badge-blue font-semibold">
      ✓ Balanced: Assets = Liabilities + Equity
    </span>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    {{-- Assets --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-3.5" style="background:rgba(56,189,248,0.15);border-bottom:1px solid rgba(56,189,248,0.3);">
        <h3 class="font-semibold text-sky-300 text-sm">Assets</h3>
      </div>
      <div>
        <div class="px-5 py-3" style="border-bottom:1px solid rgba(255,255,255,0.06);">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Current Assets</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Cash / Bank</span><span class="font-medium text-slate-200">$10,860.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Accounts Receivable</span><span class="font-medium text-slate-200">$400.00</span></div>
        </div>
        <div class="px-5 py-3" style="border-bottom:1px solid rgba(255,255,255,0.06);">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Non-Current Assets</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Fleet Vehicles (Net)</span><span class="font-medium text-slate-200">$136,000.00</span></div>
        </div>
        <div class="px-5 py-3.5" style="background:rgba(255,255,255,0.03);">
          <div class="flex justify-between font-bold"><span class="text-slate-200">Total Assets</span><span class="text-sky-400 text-base">$147,260.00</span></div>
        </div>
      </div>
    </div>

    {{-- Liabilities --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-3.5" style="background:rgba(148,163,184,0.15);border-bottom:1px solid rgba(148,163,184,0.3);">
        <h3 class="font-semibold text-slate-200 text-sm">Liabilities</h3>
      </div>
      <div>
        <div class="px-5 py-3" style="border-bottom:1px solid rgba(255,255,255,0.06);">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Current Liabilities</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Accounts Payable</span><span class="font-medium text-slate-200">$2,500.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Accrued Expenses</span><span class="font-medium text-slate-200">$2,200.00</span></div>
        </div>
        <div class="px-5 py-3" style="border-bottom:1px solid rgba(255,255,255,0.06);">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Non-Current Liabilities</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Long-term Lease</span><span class="font-medium text-slate-200">$0.00</span></div>
        </div>
        <div class="px-5 py-3.5" style="background:rgba(255,255,255,0.03);">
          <div class="flex justify-between font-bold"><span class="text-slate-200">Total Liabilities</span><span class="text-slate-300 text-base">$4,700.00</span></div>
        </div>
      </div>
    </div>

    {{-- Equity --}}
    <div class="card overflow-hidden">
      <div class="px-5 py-3.5" style="background:rgba(56,189,248,0.15);border-bottom:1px solid rgba(56,189,248,0.3);">
        <h3 class="font-semibold text-sky-300 text-sm">Equity</h3>
      </div>
      <div>
        <div class="px-5 py-3" style="border-bottom:1px solid rgba(255,255,255,0.06);">
          <p class="text-xs font-bold text-slate-400 uppercase mb-2">Shareholders' Equity</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Capital Contributions</span><span class="font-medium text-slate-200">$140,000.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Equity Distributions</span><span class="font-medium text-sky-400">−$29,500.00</span></div>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-400">Retained Earnings</span><span class="font-medium text-sky-400">$32,060.00</span></div>
        </div>
        <div class="px-5 py-3.5" style="background:rgba(255,255,255,0.03);">
          <div class="flex justify-between font-bold"><span class="text-slate-200">Total Equity</span><span class="text-sky-400 text-base">$142,560.00</span></div>
        </div>
      </div>
      <div class="px-5 py-4" style="background:rgba(56,189,248,0.08);border-top:1px solid rgba(56,189,248,0.2);">
        <div class="flex justify-between font-bold text-sky-300">
          <span>Liabilities + Equity</span>
          <span class="text-base">$147,260.00</span>
        </div>
        <p class="text-xs text-sky-400 mt-1 font-medium">✓ Matches Total Assets</p>
      </div>
    </div>
  </div>
</div>
@endsection

