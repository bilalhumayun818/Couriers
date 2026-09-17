@extends('demo.layout')
@section('title','Drivers')
@section('page-title','Stakeholders — Drivers')
@section('page-subtitle','Driver profiles, licence details and assignment history')

@section('content')

@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="card overflow-hidden">
  {{-- Toolbar --}}
  <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-end gap-3 justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <form method="GET" action="{{ route('crm.drivers') }}" id="filterForm" class="flex gap-2 flex-wrap items-end">
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Search</label>
        <input type="text" name="search" placeholder="Name or licence no…"
               value="{{ request('search') }}" class="text-sm w-52"
               onchange="filterForm.submit()">
      </div>
      @if(request('search'))
        <a href="{{ route('crm.drivers') }}" class="btn-ghost text-xs self-end" style="padding:6px 12px;">Clear</a>
      @endif
    </form>
    <button onclick="openModal(null)" class="btn-primary self-end">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Driver
    </button>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Driver</th>
          <th class="px-5 py-3 text-left">Licence No.</th>
          <th class="px-5 py-3 text-left">Licence Expiry</th>
          <th class="px-5 py-3 text-left">Days Left</th>
          <th class="px-5 py-3 text-left">Assigned Van</th>
          <th class="px-5 py-3 text-left">Contact</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($drivers as $driver)
        @php
          $expiry     = \Carbon\Carbon::parse($driver->licence_expiry_date);
          $daysLeft   = (int) now()->diffInDays($expiry, false);
          $expired    = $daysLeft < 0;
          $expiringSoon = !$expired && $daysLeft <= 30;
          $assignedVan  = $driver->vans->first();
        @endphp
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <a href="{{ route('crm.drivers.show', $driver) }}"
               class="font-semibold text-sky-400 hover:text-sky-300 hover:underline">
              {{ $driver->full_name }}
            </a>
            @if($expired)
              <span class="ml-1 badge-blue">Expired</span>
            @elseif($expiringSoon)
              <span class="ml-1 badge-blue">Expiring</span>
            @endif
          </td>
          <td class="px-5 py-3.5 font-mono text-xs text-slate-400">{{ $driver->licence_number }}</td>
          <td class="px-5 py-3.5 text-xs {{ $expired ? 'text-sky-400 font-semibold' : ($expiringSoon ? 'text-sky-400 font-semibold' : 'text-slate-500') }}">
            {{ $expiry->format('d M Y') }}
          </td>
          <td class="px-5 py-3.5 text-xs font-semibold {{ $expired ? 'text-sky-400' : ($expiringSoon ? 'text-sky-400' : ($daysLeft <= 90 ? 'text-slate-400' : 'text-sky-400')) }}">
            {{ $expired ? abs($daysLeft).' days ago' : $daysLeft.' days' }}
          </td>
          <td class="px-5 py-3.5">
            @if($assignedVan)
              <span class="font-mono text-xs px-2 py-0.5 rounded font-semibold" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;">{{ $assignedVan->plate_number }}</span>
              <span class="text-xs text-slate-500 ml-1">{{ $assignedVan->make_model }}</span>
            @else
              <span class="text-xs text-slate-600">Unassigned</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-xs text-slate-500">{{ $driver->contact_number ?? '—' }}</td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <a href="{{ route('crm.drivers.show', $driver) }}" class="text-xs text-sky-400 hover:text-sky-300 font-medium">View</a>
            <button onclick="openModal({{ json_encode(['id'=>$driver->id,'full_name'=>$driver->full_name,'national_id'=>$driver->national_id,'licence_number'=>$driver->licence_number,'licence_expiry_date'=>$driver->licence_expiry_date->format('Y-m-d'),'contact_number'=>$driver->contact_number,'emergency_contact'=>$driver->emergency_contact]) }})"
              class="text-xs text-sky-400 hover:text-sky-300 font-medium">Edit</button>
            <form method="POST" action="{{ route('crm.drivers.destroy',$driver) }}" class="inline"
                  onsubmit="return confirm('Delete driver {{ addslashes($driver->full_name) }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs text-slate-500 hover:text-sky-400 font-medium">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-5 py-12 text-center text-slate-500 text-sm">No drivers found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500" style="border-top:1px solid rgba(255,255,255,0.07);">
    <span>Showing <strong class="text-slate-300">{{ $drivers->firstItem() ?? 0 }}</strong>–<strong class="text-slate-300">{{ $drivers->lastItem() ?? 0 }}</strong> of <strong class="text-slate-300">{{ $drivers->total() }}</strong> drivers</span>
    @if($drivers->hasPages())
    <div class="flex gap-1">
      @if($drivers->onFirstPage())
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">‹</span>
      @else
        <a href="{{ $drivers->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">‹</a>
      @endif
      @foreach($drivers->getUrlRange(max(1,$drivers->currentPage()-2),min($drivers->lastPage(),$drivers->currentPage()+2)) as $page=>$url)
        @if($page==$drivers->currentPage())
          <span class="px-3 py-1.5 rounded-lg text-white font-semibold" style="background:linear-gradient(135deg,#0284c7,#2563eb);border:none;">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">{{ $page }}</a>
        @endif
      @endforeach
      @if($drivers->hasMorePages())
        <a href="{{ $drivers->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">›</a>
      @else
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">›</span>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

@section('modals')
<div id="driverModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-lg" style="max-height:90vh;overflow-y:auto;">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 id="dModalTitle" class="font-semibold text-slate-100 text-sm">Add Driver</h2>
      <button onclick="closeModal()" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="driverForm" method="POST" action="{{ route('crm.drivers.store') }}">
      @csrf
      <span id="dMethodSpan"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Full Name <span class="text-sky-400">*</span></label>
            <input type="text" name="full_name" id="df_name" required maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">National ID / Passport</label>
            <input type="text" name="national_id" id="df_nid" maxlength="100" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Licence Number <span class="text-sky-400">*</span></label>
            <input type="text" name="licence_number" id="df_licence" required maxlength="100" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Licence Expiry <span class="text-sky-400">*</span></label>
            <input type="date" name="licence_expiry_date" id="df_expiry" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Contact Number</label>
            <input type="text" name="contact_number" id="df_contact" maxlength="30" class="w-full">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Emergency Contact</label>
            <input type="text" name="emergency_contact" id="df_emergency" maxlength="255" placeholder="Name — phone" class="w-full">
          </div>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="closeModal()" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Driver</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openModal(data) {
  const form = document.getElementById('driverForm');
  const ms   = document.getElementById('dMethodSpan');
  if (data) {
    document.getElementById('dModalTitle').textContent = 'Edit Driver';
    form.action = '/crm/drivers/' + data.id;
    ms.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('df_name').value      = data.full_name       || '';
    document.getElementById('df_nid').value       = data.national_id     || '';
    document.getElementById('df_licence').value   = data.licence_number  || '';
    document.getElementById('df_expiry').value    = data.licence_expiry_date || '';
    document.getElementById('df_contact').value   = data.contact_number  || '';
    document.getElementById('df_emergency').value = data.emergency_contact || '';
  } else {
    document.getElementById('dModalTitle').textContent = 'Add Driver';
    form.action = '{{ route("crm.drivers.store") }}';
    ms.innerHTML = '';
    form.reset();
  }
  document.getElementById('driverModal').classList.remove('hidden');
}
function closeModal() { document.getElementById('driverModal').classList.add('hidden'); }
@if($errors->any()) document.addEventListener('DOMContentLoaded', () => openModal(null)); @endif
</script>
@endsection
