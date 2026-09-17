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
$roleColor = [
  'Admin'         => 'background:rgba(56,189,248,0.15);color:#60a5fa;border:1px solid rgba(56,189,248,0.3);',
  'Fleet Manager' => 'background:rgba(56,189,248,0.15);color:#7dd3fc;border:1px solid rgba(56,189,248,0.3);',
  'Accountant'    => 'background:rgba(56,189,248,0.15);color:#38bdf8;border:1px solid rgba(56,189,248,0.3);',
  'Driver'        => 'background:rgba(255,255,255,0.06);color:#cbd5e1;border:1px solid rgba(255,255,255,0.1);',
];
@endphp

<div class="space-y-4">
  {{-- Role permissions reference --}}
  <div class="card p-5">
    <h3 class="font-semibold text-slate-100 text-sm mb-3">Role Permissions Matrix</h3>
    <div class="overflow-x-auto">
      <table class="text-xs w-full">
        <thead><tr class="table-header">
          <th class="px-4 py-2 text-left">Module</th>
          <th class="px-4 py-2 text-center">Admin</th>
          <th class="px-4 py-2 text-center">Fleet Manager</th>
          <th class="px-4 py-2 text-center">Accountant</th>
          <th class="px-4 py-2 text-center">Driver</th>
        </tr></thead>
        <tbody>
          @php
          $perms=[['Dashboard','Full','Read','Read','—'],['Fleet Management','Full','Full','Read','—'],['Operations','Full','Full','Read','Read (own)'],['Stakeholders / CRM','Full','Read','Read','—'],['Ledgers & Reports','Full','Read','Full','—'],['System Settings','Full','—','—','—']];
          @endphp
          @foreach($perms as $p)
          <tr class="table-row">
            <td class="px-4 py-2 font-medium text-slate-300">{{ $p[0] }}</td>
            @foreach(array_slice($p,1) as $val)
            <td class="px-4 py-2 text-center">
              @if($val==='Full')<span class="text-sky-400 font-semibold">Full</span>
              @elseif($val==='Read')<span class="text-sky-400">Read</span>
              @elseif(str_contains($val,'own'))<span class="text-sky-400">Own Only</span>
              @else<span class="text-slate-600">—</span>
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
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <h3 class="font-semibold text-slate-100 text-sm">User Accounts</h3>
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
        <tbody>
          @foreach($users as $u)
          <tr class="table-row {{ $u[5]==='inactive' ? 'opacity-50' : '' }}">
            <td class="px-5 py-3.5 font-mono text-xs text-sky-400 font-semibold">{{ $u[0] }}</td>
            <td class="px-5 py-3.5 font-semibold text-slate-100">{{ $u[1] }}</td>
            <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $u[2] }}</td>
            <td class="px-5 py-3.5"><span class="text-xs font-medium px-2.5 py-0.5 rounded-full" style="{{ $roleColor[$u[3]] ?? '' }}">{{ $u[3] }}</span></td>
            <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $u[4] }}</td>
            <td class="px-5 py-3.5 text-center">
              @if($u[5]==='active')
                <span class="badge-blue">Active</span>
              @else
                <span class="badge-slate">Inactive</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-right space-x-2">
              <button class="text-xs text-sky-400 hover:text-sky-300 font-medium">Edit</button>
              @if($u[5]==='active')
              <button class="text-xs text-slate-400 hover:text-sky-400 font-medium">Deactivate</button>
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
<div id="userModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <h2 class="font-semibold text-slate-100">Add New User</h2>
      <button onclick="modal('userModal',false)" class="text-slate-400 hover:text-slate-200"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div><label class="block text-xs font-semibold text-slate-400 mb-1.5">Full Name</label><input type="text" class="w-full"></div>
      <div><label class="block text-xs font-semibold text-slate-400 mb-1.5">Email</label><input type="email" class="w-full"></div>
      <div><label class="block text-xs font-semibold text-slate-400 mb-1.5">Role</label>
        <select class="w-full"><option>Admin</option><option>Fleet Manager</option><option>Accountant</option><option>Driver</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-400 mb-1.5">Password</label><input type="password" placeholder="••••••••" class="w-full"></div>
    </div>
    <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.07);">
      <button onclick="modal('userModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Create User</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>@endsection
