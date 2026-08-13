@extends('demo.layout')
@section('title','Users & Roles')
@section('page-title','System Settings — Users & Roles')
@section('page-subtitle','Manage user accounts and role-based permissions')

@section('content')
@php
$users = [
  ['U-001','Admin User','admin@acmelogistics.com','Admin','01 Jan 2025','active'],
  ['U-002','Fleet Manager','fleet@acmelogistics.com','Fleet Manager','15 Feb 2025','active'],
  ['U-003','Head Accountant','accounts@acmelogistics.com','Accountant','01 Mar 2025','active'],
  ['U-004','James Mwangi','james.mwangi@acmelogistics.com','Driver','10 Apr 2025','active'],
  ['U-005','Peter Otieno','peter.otieno@acmelogistics.com','Driver','15 Apr 2025','active'],
  ['U-006','Old Staff','old@acmelogistics.com','Fleet Manager','01 Jan 2024','inactive'],
];
$roleColor = ['Admin'=>'bg-indigo-50 text-indigo-700 border-indigo-200','Fleet Manager'=>'bg-emerald-50 text-emerald-700 border-emerald-200','Accountant'=>'bg-amber-50 text-amber-700 border-amber-200','Driver'=>'bg-slate-100 text-slate-600 border-slate-200'];
@endphp

<div class="space-y-4">
  {{-- Role permissions reference --}}
  <div class="card p-5">
    <h3 class="font-semibold text-slate-800 text-sm mb-3">Role Permissions Matrix</h3>
    <div class="overflow-x-auto">
      <table class="text-xs w-full">
        <thead><tr class="table-header">
          <th class="px-4 py-2 text-left">Module</th>
          <th class="px-4 py-2 text-center">Admin</th>
          <th class="px-4 py-2 text-center">Fleet Manager</th>
          <th class="px-4 py-2 text-center">Accountant</th>
          <th class="px-4 py-2 text-center">Driver</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @php
          $perms=[['Dashboard','Full','Read','Read','—'],['Fleet Management','Full','Full','Read','—'],['Operations','Full','Full','Read','Read (own)'],['Stakeholders / CRM','Full','Read','Read','—'],['Ledgers & Reports','Full','Read','Full','—'],['System Settings','Full','—','—','—']];
          @endphp
          @foreach($perms as $p)
          <tr class="table-row">
            <td class="px-4 py-2 font-medium text-slate-700">{{ $p[0] }}</td>
            @foreach(array_slice($p,1) as $val)
            <td class="px-4 py-2 text-center">
              @if($val==='Full')<span class="text-emerald-600 font-semibold">✓ Full</span>
              @elseif($val==='Read')<span class="text-indigo-600">Read</span>
              @elseif(str_contains($val,'own'))<span class="text-amber-600">Own Only</span>
              @else<span class="text-slate-300">—</span>
              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- User list --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-800 text-sm">User Accounts</h3>
      <button onclick="modal('userModal',true)" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Add User
      </button>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-3 text-left">ID</th>
          <th class="px-5 py-3 text-left">Name</th>
          <th class="px-5 py-3 text-left">Email</th>
          <th class="px-5 py-3 text-left">Role</th>
          <th class="px-5 py-3 text-left">Created</th>
          <th class="px-5 py-3 text-center">Status</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr></thead>
        <tbody class="divide-y divide-slate-50">
          @foreach($users as $u)
          <tr class="table-row {{ $u[5]==='inactive' ? 'opacity-60' : '' }}">
            <td class="px-5 py-3.5 font-mono text-xs text-indigo-600 font-semibold">{{ $u[0] }}</td>
            <td class="px-5 py-3.5 font-semibold text-slate-800">{{ $u[1] }}</td>
            <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $u[2] }}</td>
            <td class="px-5 py-3.5"><span class="text-xs font-medium px-2.5 py-0.5 rounded-full border {{ $roleColor[$u[3]] }}">{{ $u[3] }}</span></td>
            <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $u[4] }}</td>
            <td class="px-5 py-3.5 text-center">
              @if($u[5]==='active')
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
              @else
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200">Inactive</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-right space-x-2">
              <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
              @if($u[5]==='active')
              <button class="text-xs text-slate-400 hover:text-red-600 font-medium">Deactivate</button>
              @endif
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
<div id="userModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Add New User</h2>
      <button onclick="modal('userModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Full Name</label><input type="text" class="w-full"></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label><input type="email" class="w-full"></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Role</label>
        <select class="w-full"><option>Admin</option><option>Fleet Manager</option><option>Accountant</option><option>Driver</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label><input type="password" placeholder="••••••••" class="w-full"></div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('userModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Create User</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
