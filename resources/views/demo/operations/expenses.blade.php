@extends('demo.layout')
@section('title','Daily Expenses')
@section('page-title','Operations — Daily Expenses')
@section('page-subtitle','Log and track daily operating expenses per van')

@section('content')
@php
$expenses = [
  ['E-00312','17 Jun 2025','ABC-001','Fuel','Petrol refill — full tank','$85.00','active'],
  ['E-00311','17 Jun 2025','XYZ-202','Tolls','Nairobi Expressway — round trip','$12.00','active'],
  ['E-00310','16 Jun 2025','DEF-303','Maintenance/Repairs','Replace brake pads','$140.00','active'],
  ['E-00309','16 Jun 2025','GHJ-441','Spare Parts','Oil filter + engine oil','$65.00','active'],
  ['E-00308','15 Jun 2025','ABC-001','Fuel','Petrol refill','$90.00','active'],
  ['E-00307','15 Jun 2025','KLM-505','Tolls','Thika Superhighway','$8.00','deleted'],
  ['E-00306','14 Jun 2025','STU-808','Fuel','Diesel refill','$110.00','active'],
];
$catColor = [
  'Fuel'=>'bg-amber-50 text-amber-700 border-amber-200',
  'Tolls'=>'bg-slate-100 text-slate-600 border-slate-200',
  'Maintenance/Repairs'=>'bg-red-50 text-red-600 border-red-200',
  'Spare Parts'=>'bg-indigo-50 text-indigo-700 border-indigo-200',
];
@endphp

<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
    <div class="flex gap-2 flex-wrap">
      <select class="text-sm"><option>All Vans</option><option>ABC-001</option><option>XYZ-202</option></select>
      <select class="text-sm"><option>All Categories</option><option>Fuel</option><option>Tolls</option><option>Maintenance/Repairs</option><option>Spare Parts</option></select>
      <input type="date" class="text-sm" value="2025-06-17">
    </div>
    <button onclick="modal('expModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Log Expense
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Expense ID</th>
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Category</th>
          <th class="px-5 py-3 text-left">Description</th>
          <th class="px-5 py-3 text-right">Amount</th>
          <th class="px-5 py-3 text-center">Status</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($expenses as $e)
        <tr class="table-row {{ $e[6]==='deleted' ? 'opacity-50' : '' }}">
          <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $e[0] }}</td>
          <td class="px-5 py-3.5 text-xs text-slate-500">{{ $e[1] }}</td>
          <td class="px-5 py-3.5"><span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded font-semibold text-slate-800">{{ $e[2] }}</span></td>
          <td class="px-5 py-3.5">
            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full border {{ $catColor[$e[3]] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">{{ $e[3] }}</span>
          </td>
          <td class="px-5 py-3.5 text-slate-600 text-xs">{{ $e[4] }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-slate-800">{{ $e[5] }}</td>
          <td class="px-5 py-3.5 text-center">
            @if($e[6]==='active')
              <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
            @else
              <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200">Deleted</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            @if($e[6]==='active')
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
            <button class="text-xs text-slate-400 hover:text-red-600 font-medium">Delete</button>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
    <span>Showing <strong>7</strong> of <strong>88</strong> expenses</span>
    <div class="flex gap-1">
      <button class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50">‹</button>
      <button class="px-3 py-1.5 border border-indigo-600 bg-indigo-600 text-white rounded-lg">1</button>
      <button class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50">2</button>
      <button class="px-3 py-1.5 border border-slate-200 rounded-lg hover:bg-slate-50">›</button>
    </div>
  </div>
</div>
@endsection

@section('modals')
<div id="expModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Log Expense</h2>
      <button onclick="modal('expModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Van</label>
          <select class="w-full"><option>ABC-001</option><option>XYZ-202</option><option>DEF-303</option></select></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Expense Date</label>
          <input type="date" class="w-full" value="2025-06-17"></div>
      </div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Category</label>
        <select class="w-full"><option>Fuel</option><option>Tolls</option><option>Spare Parts</option><option>Maintenance/Repairs</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Amount ($)</label>
        <input type="number" placeholder="0.00" class="w-full"></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Description (optional)</label>
        <textarea rows="2" placeholder="Brief note…" class="w-full resize-none"></textarea></div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('expModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Expense</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
