@extends('demo.layout')
@section('title','Fixed Costs')
@section('page-title','Fleet — Fixed Costs Configuration')
@section('page-subtitle','Monthly lease, insurance, road tax and service schedules per vehicle')

@section('content')
@php
$rows = [
  ['ABC-001','Toyota HiAce','1,800.00','450.00','120.00','2,258.00','20 Jul 2025',false],
  ['XYZ-202','Nissan NV350','1,600.00','400.00','110.00','2,033.00','15 Aug 2025',false],
  ['DEF-303','Ford Transit','2,000.00','500.00','140.00','2,458.00','10 Sep 2025',false],
  ['GHJ-441','Toyota HiAce','1,800.00','450.00','120.00','2,258.00','20 Jun 2025',true],
  ['KLM-505','Isuzu NPR','2,200.00','550.00','150.00','2,658.00','22 Jul 2025',false],
  ['NOP-606','Mitsubishi Canter','1,500.00','380.00','100.00','1,848.00','30 Jul 2025',false],
  ['QRS-707','Hino 300','2,400.00','600.00','160.00','2,950.00','05 Oct 2025',false],
  ['STU-808','Toyota Dyna','1,700.00','420.00','115.00','2,085.00','18 Aug 2025',false],
];
@endphp

<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <p class="text-sm text-slate-500">Fixed costs are prorated daily when computing van-wise ledger reports.</p>
    <button onclick="modal('costModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Update Costs
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-right">Monthly Lease</th>
          <th class="px-5 py-3 text-right">Road Tax (Annual)</th>
          <th class="px-5 py-3 text-right">Insurance / Mo</th>
          <th class="px-5 py-3 text-right">Total Fixed / Mo</th>
          <th class="px-5 py-3 text-left">Next Service Date</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($rows as $r)
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <span class="font-mono font-semibold text-slate-800 text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $r[0] }}</span>
          </td>
          <td class="px-5 py-3.5 text-right text-slate-600">${{ $r[2] }}</td>
          <td class="px-5 py-3.5 text-right text-slate-600">${{ $r[3] }}</td>
          <td class="px-5 py-3.5 text-right text-slate-600">${{ $r[4] }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-slate-800">${{ $r[5] }}</td>
          <td class="px-5 py-3.5 text-xs {{ $r[7] ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
            {{ $r[6] }}
            @if($r[7]) <span class="ml-1 bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full">⚠ Due Soon</span> @endif
          </td>
          <td class="px-5 py-3.5 text-right">
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot class="bg-slate-50 border-t border-slate-200">
        <tr>
          <td class="px-5 py-3 font-semibold text-slate-700 text-xs">Fleet Total / Month</td>
          <td class="px-5 py-3 text-right font-semibold text-slate-800">$15,000.00</td>
          <td class="px-5 py-3 text-right font-semibold text-slate-800">$3,750.00</td>
          <td class="px-5 py-3 text-right font-semibold text-slate-800">$1,015.00</td>
          <td class="px-5 py-3 text-right font-bold text-indigo-700">$19,765.00</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
@endsection

@section('modals')
<div id="costModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Update Fixed Costs</h2>
      <button onclick="modal('costModal',false)" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-5 space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Select Van</label>
        <select class="w-full">
          <option>ABC-001 — Toyota HiAce</option>
          <option>XYZ-202 — Nissan NV350</option>
          <option>DEF-303 — Ford Transit</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Monthly Lease ($)</label>
          <input type="number" placeholder="1800.00" class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Road Tax Annual ($)</label>
          <input type="number" placeholder="450.00" class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Insurance Monthly ($)</label>
          <input type="number" placeholder="120.00" class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Next Service Date</label>
          <input type="date" class="w-full">
        </div>
      </div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('costModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Changes</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
