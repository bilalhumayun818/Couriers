@extends('demo.layout')
@section('title','Customers')
@section('page-title','Stakeholders — Customers')
@section('page-subtitle','Customer directory, credit limits and outstanding balances')

@section('content')
@php
$customers = [
  ['C-001','Swift Retail Co','John Smith','john@swiftretail.com','+254 700 123456','$5,000.00','$4,200.00','$800.00'],
  ['C-002','Global Traders Ltd','Mary Kamau','mary@globaltraders.co.ke','+254 722 234567','$8,000.00','$6,500.00','$1,500.00'],
  ['C-003','Metro Supplies','Ali Hassan','ali@metrosupplies.com','+254 733 345678','$3,000.00','$2,800.00','$200.00'],
  ['C-004','Apex Importers','Jane Otieno','jane@apex.co.ke','+254 711 456789','$10,000.00','$9,800.00','$200.00'],
  ['C-005','Zenith Cargo','Tom Njoroge','tom@zenithcargo.com','+254 799 567890','$6,000.00','$3,200.00','$2,800.00'],
  ['C-006','ProFreight Ltd','Sarah Waweru','sarah@profreight.com','+254 755 678901','$4,500.00','$1,800.00','$2,700.00'],
];
@endphp
<div class="card overflow-hidden">
  <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
    <div class="flex gap-2 flex-wrap">
      <input type="text" placeholder="Search company or contact…" class="w-52 text-sm">
      <select class="text-sm"><option>All Balances</option><option>With Balance</option><option>No Balance</option></select>
    </div>
    <button onclick="modal('custModal',true)" class="btn-primary">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Customer
    </button>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead><tr class="table-header">
        <th class="px-5 py-3 text-left">ID</th>
        <th class="px-5 py-3 text-left">Company</th>
        <th class="px-5 py-3 text-left">Contact</th>
        <th class="px-5 py-3 text-left">Email</th>
        <th class="px-5 py-3 text-right">Credit Limit</th>
        <th class="px-5 py-3 text-right">Invoiced</th>
        <th class="px-5 py-3 text-right">Outstanding</th>
        <th class="px-5 py-3 text-right">Actions</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @foreach($customers as $c)
        <tr class="table-row">
          <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $c[0] }}</td>
          <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $c[1] }}</td>
          <td class="px-5 py-3.5 text-slate-600">{{ $c[2] }}</td>
          <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $c[3] }}</td>
          <td class="px-5 py-3.5 text-right text-slate-600">{{ $c[6] }}</td>
          <td class="px-5 py-3.5 text-right text-slate-600">{{ $c[7] }}</td>
          <td class="px-5 py-3.5 text-right">
            @php $bal = (float)str_replace(['$',','],'',$c[8]); @endphp
            <span class="font-semibold {{ $bal > 2000 ? 'text-red-600' : 'text-slate-800' }}">{{ $c[8] }}</span>
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
<div id="custModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Add New Customer</h2>
      <button onclick="modal('custModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Company Name</label><input type="text" placeholder="Acme Ltd" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Contact Name</label><input type="text" placeholder="John Doe" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label><input type="email" placeholder="contact@company.com" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Phone</label><input type="text" placeholder="+254 700 000000" class="w-full"></div>
      </div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Billing Address</label><textarea rows="2" class="w-full resize-none" placeholder="Full billing address…"></textarea></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Credit Limit ($)</label><input type="number" placeholder="5000.00" class="w-full"></div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('custModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Customer</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
