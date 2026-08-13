@extends('demo.layout')
@section('title','Driver Assignments')
@section('page-title','Fleet — Driver Assignments')
@section('page-subtitle','Current van-to-driver mappings and assignment history')

@section('content')
@php
$assignments = [
  ['ABC-001','Toyota HiAce','James Mwangi','DL-KE-20190234','01 Jan 2025','—','active'],
  ['XYZ-202','Nissan NV350','Peter Otieno','DL-KE-20180892','15 Feb 2025','—','active'],
  ['DEF-303','Ford Transit','Samuel Kamau','DL-KE-20211045','01 Mar 2025','—','active'],
  ['GHJ-441','Toyota HiAce','—','—','—','—','unassigned'],
  ['KLM-505','Isuzu NPR','David Njoroge','DL-KE-20170563','10 Apr 2025','—','active'],
  ['NOP-606','Mitsubishi Canter','John Waweru','DL-KE-20201287','01 May 2025','—','active'],
  ['QRS-707','Hino 300','Alice Wanjiku','DL-KE-20220341','01 Jun 2025','—','active'],
  ['STU-808','Toyota Dyna','Grace Achieng','DL-KE-20190789','01 Jan 2025','—','active'],
];
$badge = ['active'=>'bg-emerald-50 text-emerald-700 border-emerald-200','unassigned'=>'bg-slate-100 text-slate-500 border-slate-200'];
@endphp

<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
    <p class="text-sm text-slate-500">Each van can have exactly one active primary driver at a time.</p>
    <button onclick="modal('assignModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      New Assignment
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Driver</th>
          <th class="px-5 py-3 text-left">Licence No.</th>
          <th class="px-5 py-3 text-left">Start Date</th>
          <th class="px-5 py-3 text-left">End Date</th>
          <th class="px-5 py-3 text-center">Status</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($assignments as $a)
        <tr class="table-row">
          <td class="px-5 py-3.5"><span class="font-mono font-semibold text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-800">{{ $a[0] }}</span><div class="text-xs text-slate-400 mt-0.5">{{ $a[1] }}</div></td>
          <td class="px-5 py-3.5 text-slate-700 font-medium">{{ $a[2] }}</td>
          <td class="px-5 py-3.5 text-slate-500 text-xs font-mono">{{ $a[3] }}</td>
          <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $a[4] }}</td>
          <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $a[5] }}</td>
          <td class="px-5 py-3.5 text-center">
            <span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full border capitalize {{ $badge[$a[6]] }}">{{ $a[6] }}</span>
          </td>
          <td class="px-5 py-3.5 text-right space-x-3">
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Reassign</button>
            <button class="text-xs text-slate-400 hover:text-red-600 font-medium">End</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('modals')
<div id="assignModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">New Driver Assignment</h2>
      <button onclick="modal('assignModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Select Van</label>
        <select class="w-full"><option>ABC-001 — Toyota HiAce</option><option>GHJ-441 — Toyota HiAce (Unassigned)</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Select Driver</label>
        <select class="w-full"><option>James Mwangi</option><option>Peter Otieno</option><option>Samuel Kamau</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Start Date</label>
        <input type="date" class="w-full"></div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('assignModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Assign Driver</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
