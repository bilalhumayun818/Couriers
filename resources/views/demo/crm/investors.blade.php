@extends('demo.layout')
@section('title','Investors & Directors')
@section('page-title','Stakeholders — Investors & Directors')
@section('page-subtitle','Capital contributions, profit distributions and retained balances')

@section('content')
@php
$investors = [
  ['I-001','Robert Kamau','Director','Equity Bank','KE1234567890','$50,000.00','$12,000.00','$38,000.00'],
  ['I-002','Susan Otieno','Investor','KCB Bank','KE0987654321','$30,000.00','$6,000.00','$24,000.00'],
  ['I-003','Michael Njoroge','Director','Equity Bank','KE1122334455','$40,000.00','$8,500.00','$31,500.00'],
  ['I-004','Patricia Waweru','Investor','Co-op Bank','KE5544332211','$20,000.00','$3,000.00','$17,000.00'],
];
@endphp

<div class="space-y-5">
  {{-- Summary Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card p-5">
      <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Capital</p>
      <p class="text-2xl font-bold text-slate-800 mt-1.5">$140,000</p>
      <p class="text-xs text-slate-400 mt-1">4 investors &amp; directors</p>
    </div>
    <div class="card p-5">
      <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Distributed</p>
      <p class="text-2xl font-bold text-red-600 mt-1.5">$29,500</p>
      <p class="text-xs text-slate-400 mt-1">Profit-sharing payouts</p>
    </div>
    <div class="card p-5">
      <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Retained Equity</p>
      <p class="text-2xl font-bold text-emerald-700 mt-1.5">$110,500</p>
      <p class="text-xs text-slate-400 mt-1">Net retained balance</p>
    </div>
  </div>

  {{-- Investor Table --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800 text-sm">Investor &amp; Director Ledger</h3>
      <button onclick="modal('invModal',true)" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add Investor
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-3 text-left">ID</th>
          <th class="px-5 py-3 text-left">Name</th>
          <th class="px-5 py-3 text-left">Role</th>
          <th class="px-5 py-3 text-left">Bank</th>
          <th class="px-5 py-3 text-left">Account No.</th>
          <th class="px-5 py-3 text-right">Capital</th>
          <th class="px-5 py-3 text-right">Distributed</th>
          <th class="px-5 py-3 text-right">Retained</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($investors as $inv)
          <tr class="table-row">
            <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $inv[0] }}</td>
            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $inv[1] }}</td>
            <td class="px-5 py-3.5">
              <span class="text-xs font-medium px-2.5 py-0.5 rounded-full border
                {{ $inv[2]==='Director' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                {{ $inv[2] }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $inv[3] }}</td>
            <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ $inv[4] }}</td>
            <td class="px-5 py-3.5 text-right font-semibold text-slate-800">{{ $inv[5] }}</td>
            <td class="px-5 py-3.5 text-right text-red-500">{{ $inv[6] }}</td>
            <td class="px-5 py-3.5 text-right font-bold text-emerald-700">{{ $inv[7] }}</td>
            <td class="px-5 py-3.5 text-right space-x-2">
              <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Inject</button>
              <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Distribute</button>
              <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View</button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('modals')
<div id="invModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Add Investor / Director</h2>
      <button onclick="modal('invModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name</label><input type="text" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Role</label>
          <select class="w-full"><option>Investor</option><option>Director</option></select></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Bank Name</label><input type="text" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Account Number</label><input type="text" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Account Name</label><input type="text" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Initial Capital ($)</label><input type="number" placeholder="0.00" class="w-full"></div>
      </div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('invModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
