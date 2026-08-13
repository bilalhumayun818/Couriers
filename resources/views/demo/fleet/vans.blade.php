@extends('demo.layout')
@section('title','Vehicles')
@section('page-title','Fleet Management — Vehicles')
@section('page-subtitle','Manage registered vans, status and assignments')

@section('content')
@php
$vans = [
  ['ABC-001','Toyota HiAce','2022','active','James Mwangi','20 Jul 2025',false],
  ['XYZ-202','Nissan NV350','2021','active','Peter Otieno','15 Aug 2025',false],
  ['DEF-303','Ford Transit','2023','active','Samuel Kamau','10 Sep 2025',false],
  ['GHJ-441','Toyota HiAce','2020','maintenance','—','20 Jun 2025',true],
  ['KLM-505','Isuzu NPR','2022','active','David Njoroge','22 Jul 2025',false],
  ['NOP-606','Mitsubishi Canter','2021','active','John Waweru','30 Jul 2025',false],
  ['QRS-707','Hino 300','2023','leased','Alice Wanjiku','05 Oct 2025',false],
  ['STU-808','Toyota Dyna','2022','active','Grace Achieng','18 Aug 2025',false],
];
$badge = [
  'active'      => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
  'maintenance' => 'bg-amber-50 text-amber-700 border border-amber-200',
  'leased'      => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
];
@endphp

<div class="card overflow-hidden">
  {{-- Toolbar --}}
  <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
    <div class="flex gap-2 flex-wrap">
      <input type="text" placeholder="Search plate or model…"
             class="w-52 text-sm">
      <select class="text-sm">
        <option>All Status</option>
        <option>Active</option>
        <option>Maintenance</option>
        <option>Leased</option>
      </select>
    </div>
    <button onclick="modal('vanModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Vehicle
    </button>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Plate Number</th>
          <th class="px-5 py-3 text-left">Make / Model</th>
          <th class="px-5 py-3 text-left">Year</th>
          <th class="px-5 py-3 text-left">Status</th>
          <th class="px-5 py-3 text-left">Assigned Driver</th>
          <th class="px-5 py-3 text-left">Next Service</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($vans as $v)
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <span class="font-mono font-semibold text-slate-800 text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $v[0] }}</span>
          </td>
          <td class="px-5 py-3.5 text-slate-700">{{ $v[1] }}</td>
          <td class="px-5 py-3.5 text-slate-500">{{ $v[2] }}</td>
          <td class="px-5 py-3.5">
            <span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full {{ $badge[$v[3]] }} capitalize">{{ $v[3] }}</span>
          </td>
          <td class="px-5 py-3.5 text-slate-600">{{ $v[4] }}</td>
          <td class="px-5 py-3.5 {{ $v[6] ? 'text-red-600 font-semibold' : 'text-slate-500' }} text-xs">
            {{ $v[5] }}
            @if($v[6]) <span class="ml-1 bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full text-xs">Soon</span> @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-3">
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
            <button class="text-xs text-slate-400 hover:text-red-600 font-medium">Delete</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
    <span>Showing <strong>8</strong> of <strong>12</strong> vehicles</span>
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
<div id="vanModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Add New Vehicle</h2>
      <button onclick="modal('vanModal',false)" class="text-slate-400 hover:text-slate-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-5 space-y-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Plate Number <span class="text-red-500">*</span></label>
        <input type="text" placeholder="e.g. KAA-123A" class="w-full">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Make / Model <span class="text-red-500">*</span></label>
        <input type="text" placeholder="e.g. Toyota HiAce" class="w-full">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Year <span class="text-red-500">*</span></label>
          <input type="number" placeholder="2024" class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status <span class="text-red-500">*</span></label>
          <select class="w-full"><option>Active</option><option>Maintenance</option><option>Leased</option></select>
        </div>
      </div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('vanModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Vehicle</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function modal(id, show) {
  document.getElementById(id).classList.toggle('hidden', !show);
}
</script>
@endsection
