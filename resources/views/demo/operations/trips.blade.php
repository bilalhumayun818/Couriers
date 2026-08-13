@extends('demo.layout')
@section('title','Trip Entries')
@section('page-title','Operations — Trip Entries')
@section('page-subtitle','Log and manage daily courier trips')

@section('content')
@php
$trips = [
  ['T-00124','17 Jun 2025','ABC-001','Swift Retail Co','Nairobi','Mombasa','$420.00','$33.60','$453.60','active'],
  ['T-00123','17 Jun 2025','XYZ-202','Global Traders Ltd','Nakuru','Nairobi','$180.00','$14.40','$194.40','active'],
  ['T-00122','16 Jun 2025','DEF-303','Metro Supplies','Kisumu','Nakuru','$260.00','$20.80','$280.80','active'],
  ['T-00121','16 Jun 2025','GHJ-441','Apex Importers','Nairobi','Eldoret','$350.00','$28.00','$378.00','voided'],
  ['T-00120','15 Jun 2025','ABC-001','Swift Retail Co','Mombasa','Nairobi','$400.00','$32.00','$432.00','active'],
  ['T-00119','15 Jun 2025','KLM-505','Zenith Cargo','Nairobi','Nakuru','$210.00','$16.80','$226.80','active'],
  ['T-00118','14 Jun 2025','STU-808','ProFreight Ltd','Kisumu','Nairobi','$480.00','$38.40','$518.40','active'],
];
@endphp

<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
    <div class="flex gap-2 flex-wrap">
      <input type="text" placeholder="Search trip or customer…" class="w-48 text-sm">
      <select class="text-sm"><option>All Vans</option><option>ABC-001</option><option>XYZ-202</option><option>DEF-303</option></select>
      <input type="date" class="text-sm" value="2025-06-17">
    </div>
    <button onclick="modal('tripModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Log Trip
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Trip ID</th>
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Customer</th>
          <th class="px-5 py-3 text-left">Route</th>
          <th class="px-5 py-3 text-right">Fare</th>
          <th class="px-5 py-3 text-right">Tax (8%)</th>
          <th class="px-5 py-3 text-right">Total</th>
          <th class="px-5 py-3 text-center">Status</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($trips as $t)
        <tr class="table-row {{ $t[9]==='voided' ? 'opacity-60' : '' }}">
          <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $t[0] }}</td>
          <td class="px-5 py-3.5 text-xs text-slate-500">{{ $t[1] }}</td>
          <td class="px-5 py-3.5"><span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded font-semibold text-slate-800">{{ $t[2] }}</span></td>
          <td class="px-5 py-3.5 text-slate-700">{{ $t[3] }}</td>
          <td class="px-5 py-3.5 text-xs text-slate-500">{{ $t[4] }} → {{ $t[5] }}</td>
          <td class="px-5 py-3.5 text-right text-slate-600">{{ $t[6] }}</td>
          <td class="px-5 py-3.5 text-right text-slate-400 text-xs">{{ $t[7] }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-slate-800">{{ $t[8] }}</td>
          <td class="px-5 py-3.5 text-center">
            @if($t[9]==='active')
              <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Active</span>
            @else
              <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Voided</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
            @if($t[9]==='active')
            <button class="text-xs text-slate-400 hover:text-red-600 font-medium">Void</button>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
    <span>Showing <strong>7</strong> of <strong>124</strong> trips</span>
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
<div id="tripModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Log New Trip</h2>
      <button onclick="modal('tripModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Van (Active only)</label>
          <select class="w-full"><option>ABC-001 — Toyota HiAce</option><option>XYZ-202 — Nissan NV350</option></select></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Trip Date</label>
          <input type="date" class="w-full" value="2025-06-17"></div>
      </div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Customer</label>
        <select class="w-full"><option>Swift Retail Co</option><option>Global Traders Ltd</option><option>Metro Supplies</option></select></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Origin</label>
          <input type="text" placeholder="e.g. Nairobi" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Destination</label>
          <input type="text" placeholder="e.g. Mombasa" class="w-full"></div>
      </div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Fare Amount ($)</label>
        <input type="number" placeholder="0.00" class="w-full" oninput="calcTax(this)">
        <p class="text-xs text-slate-400 mt-1">Tax (8%): <span id="taxAmt">$0.00</span> &bull; Total: <span id="totalAmt">$0.00</span></p>
      </div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('tripModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Trip</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}
function calcTax(el){
  const fare=parseFloat(el.value)||0;
  const tax=fare*0.08;
  document.getElementById('taxAmt').textContent='$'+tax.toFixed(2);
  document.getElementById('totalAmt').textContent='$'+(fare+tax).toFixed(2);
}
</script>
@endsection
