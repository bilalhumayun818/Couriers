@extends('demo.layout')
@section('title','Drivers')
@section('page-title','Stakeholders — Drivers')
@section('page-subtitle','Driver profiles, licence details and assignment history')

@section('content')
@php
$drivers = [
  ['D-001','James Mwangi','DL-KE-20190234','04 Jul 2025',18,'ABC-001','active',true],
  ['D-002','Peter Otieno','DL-KE-20180892','15 Nov 2025',150,'XYZ-202','active',false],
  ['D-003','Samuel Kamau','DL-KE-20211045','22 Mar 2026',280,'DEF-303','active',false],
  ['D-004','David Njoroge','DL-KE-20170563','08 Aug 2025',53,'KLM-505','active',false],
  ['D-005','John Waweru','DL-KE-20201287','30 Sep 2025',105,'NOP-606','active',false],
  ['D-006','Alice Wanjiku','DL-KE-20220341','12 Dec 2025',178,'QRS-707','active',false],
  ['D-007','Grace Achieng','DL-KE-20190789','20 Jun 2025',3,'STU-808','active',true],
];
@endphp

<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
    <div class="flex gap-2 flex-wrap">
      <input type="text" placeholder="Search name or licence…" class="w-52 text-sm">
    </div>
    <button onclick="modal('driverModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Driver
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead><tr class="table-header">
        <th class="px-5 py-3 text-left">ID</th>
        <th class="px-5 py-3 text-left">Full Name</th>
        <th class="px-5 py-3 text-left">Licence No.</th>
        <th class="px-5 py-3 text-left">Licence Expiry</th>
        <th class="px-5 py-3 text-left">Days Left</th>
        <th class="px-5 py-3 text-left">Assigned Van</th>
        <th class="px-5 py-3 text-center">Status</th>
        <th class="px-5 py-3 text-right">Actions</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($drivers as $d)
        <tr class="table-row">
          <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $d[0] }}</td>
          <td class="px-5 py-3.5 font-semibold text-slate-800">
            {{ $d[1] }}
            @if($d[7])<span class="ml-1.5 text-xs bg-amber-100 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded-full">⚠ Expiring</span>@endif
          </td>
          <td class="px-5 py-3.5 font-mono text-xs text-slate-600">{{ $d[2] }}</td>
          <td class="px-5 py-3.5 text-xs {{ $d[7] ? 'text-red-600 font-semibold' : 'text-slate-500' }}">{{ $d[3] }}</td>
          <td class="px-5 py-3.5 text-xs">
            <span class="font-semibold {{ $d[4] <= 30 ? 'text-red-600' : ($d[4] <= 60 ? 'text-amber-600' : 'text-emerald-700') }}">{{ $d[4] }} days</span>
          </td>
          <td class="px-5 py-3.5"><span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded text-slate-700">{{ $d[5] }}</span></td>
          <td class="px-5 py-3.5 text-center">
            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View</button>
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('modals')
<div id="driverModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Add New Driver</h2>
      <button onclick="modal('driverModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name</label><input type="text" placeholder="First Last" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">National ID / Passport</label><input type="text" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Licence Number</label><input type="text" placeholder="DL-KE-XXXXXXXX" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Licence Expiry</label><input type="date" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Contact Number</label><input type="text" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Emergency Contact</label><input type="text" class="w-full"></div>
      </div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('driverModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Driver</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
