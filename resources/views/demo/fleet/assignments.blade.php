@extends('demo.layout')
@section('title','Driver Assignments')
@section('page-title','Fleet — Driver Assignments')
@section('page-subtitle','Manage van-to-driver mappings and view assignment history')

@section('content')

{{-- Success / Error banners --}}
@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

{{-- Reassign confirmation banner --}}
@if(session('confirm_reassign'))
@php $cr = session('confirm_reassign'); @endphp
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.3);border-radius:10px;padding:14px 18px;margin-bottom:16px;">
  <p class="text-sm font-semibold text-blue-300 mb-2">
    {{ $cr['driver_name'] }} is currently assigned to <strong>{{ $cr['current_van'] }}</strong>.
    Reassigning will end that assignment. Confirm?
  </p>
  <form method="POST" action="{{ route('fleet.assignments.assign') }}" class="inline">
    @csrf
    <input type="hidden" name="van_id"     value="{{ $cr['van_id'] }}">
    <input type="hidden" name="driver_id"  value="{{ $cr['driver_id'] }}">
    <input type="hidden" name="start_date" value="{{ $cr['start_date'] }}">
    <input type="hidden" name="force"      value="1">
    <button type="submit" class="btn-primary text-xs px-3 py-1.5 mr-2">Yes, Reassign to {{ $cr['target_van'] }}</button>
    <a href="{{ route('fleet.assignments') }}" class="btn-ghost text-xs px-3 py-1.5">Cancel</a>
  </form>
</div>
@endif

{{-- Summary stats --}}
@php
  $activeCount    = $vans->filter(fn($v) => $v->assignments->where('end_date', null)->count() > 0)->count();
  $unassignedCount = $vans->count() - $activeCount;
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Vans</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $vans->count() }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Assigned</p>
    <p class="text-2xl font-bold text-sky-400 mt-1">{{ $activeCount }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Unassigned</p>
    <p class="text-2xl font-bold text-blue-400 mt-1">{{ $unassignedCount }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Available Drivers</p>
    <p class="text-2xl font-bold text-sky-400 mt-1">{{ $availableDrivers->count() }}</p>
  </div>
</div>

{{-- Current Assignments table --}}
<div class="card overflow-hidden mb-5">
  <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <div>
      <h3 class="font-semibold text-slate-100 text-sm">Current Assignments</h3>
      <p class="text-xs text-slate-400 mt-0.5">One active driver per van at any time</p>
    </div>
    <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="btn-primary">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      New Assignment
    </button>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Van Status</th>
          <th class="px-5 py-3 text-left">Assigned Driver</th>
          <th class="px-5 py-3 text-left">Licence No.</th>
          <th class="px-5 py-3 text-left">Start Date</th>
          <th class="px-5 py-3 text-left">Duration</th>
          <th class="px-5 py-3 text-center">Assignment</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($vans as $van)
        @php
          $active = $van->assignments->firstWhere('end_date', null);
          $duration = $active
            ? \Carbon\Carbon::parse($active->start_date)->diffForHumans(now(), \Carbon\CarbonInterface::DIFF_ABSOLUTE)
            : null;
        @endphp
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <span class="font-mono font-semibold text-xs px-2.5 py-1 rounded-md" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">{{ $van->plate_number }}</span>
            <span class="block text-xs text-slate-400 mt-0.5">{{ $van->make_model }}</span>
          </td>
          <td class="px-5 py-3.5">
            @if($van->status === 'active')
              <span class="badge-blue capitalize">{{ $van->status }}</span>
            @elseif($van->status === 'maintenance')
              <span class="badge-amber capitalize">{{ $van->status }}</span>
            @else
              <span class="badge-blue capitalize">{{ $van->status }}</span>
            @endif
          </td>
          <td class="px-5 py-3.5 font-semibold text-slate-200">
            {{ $active?->driver?->full_name ?? '—' }}
          </td>
          <td class="px-5 py-3.5 font-mono text-xs text-slate-400">
            {{ $active?->driver?->licence_number ?? '—' }}
          </td>
          <td class="px-5 py-3.5 text-xs text-slate-400">
            {{ $active ? \Carbon\Carbon::parse($active->start_date)->format('d M Y') : '—' }}
          </td>
          <td class="px-5 py-3.5 text-xs text-slate-400">
            {{ $duration ?? '—' }}
          </td>
          <td class="px-5 py-3.5 text-center">
            @if($active)
              <span class="badge-blue">Active</span>
            @else
              <span class="badge-slate">Unassigned</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <button onclick="openAssignForVan({{ $van->id }}, '{{ $van->plate_number }}')"
              class="text-xs text-sky-400 hover:text-sky-300 font-medium">
              {{ $active ? 'Reassign' : 'Assign' }}
            </button>
            @if($active)
            <form method="POST" action="{{ route('fleet.assignments.end', $active->id) }}" class="inline"
                  onsubmit="return confirm('End assignment for {{ $active->driver->full_name }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs text-slate-500 hover:text-sky-400 font-medium">End</button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Assignment History --}}
<div class="card overflow-hidden">
  <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <h3 class="font-semibold text-slate-100 text-sm">Assignment History</h3>
    <p class="text-xs text-slate-400 mt-0.5">All past and current assignments across the fleet</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Driver</th>
          <th class="px-5 py-3 text-left">Start Date</th>
          <th class="px-5 py-3 text-left">End Date</th>
          <th class="px-5 py-3 text-center">Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($vans as $van)
          @foreach($van->assignments as $asgn)
          <tr class="table-row {{ $asgn->end_date ? 'opacity-60' : '' }}">
            <td class="px-5 py-3">
              <span class="font-mono text-xs px-2 py-0.5 rounded" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;">{{ $van->plate_number }}</span>
            </td>
            <td class="px-5 py-3 text-slate-200 font-medium">{{ $asgn->driver?->full_name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-slate-400">{{ \Carbon\Carbon::parse($asgn->start_date)->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-slate-400">
              {{ $asgn->end_date ? \Carbon\Carbon::parse($asgn->end_date)->format('d M Y') : '—' }}
            </td>
            <td class="px-5 py-3 text-center">
              @if(!$asgn->end_date)
                <span class="badge-blue">Active</span>
              @else
                <span class="badge-slate">Ended</span>
              @endif
            </td>
          </tr>
          @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('modals')
{{-- Assign Driver Modal --}}
<div id="assignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h2 class="font-semibold text-slate-100 text-sm">Assign Driver to Van</h2>
        <p id="assignModalLabel" class="text-xs text-slate-400 mt-0.5">Select van and driver</p>
      </div>
      <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form method="POST" action="{{ route('fleet.assignments.assign') }}">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Van <span class="text-sky-400">*</span></label>
          <select name="van_id" id="f_van" required class="w-full">
            <option value="">— Select Van —</option>
            @foreach($vans as $van)
              <option value="{{ $van->id }}">{{ $van->plate_number }} — {{ $van->make_model }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Driver <span class="text-sky-400">*</span></label>
          <select name="driver_id" required class="w-full">
            <option value="">— Select Driver —</option>
            @foreach($allDrivers as $driver)
              <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Start Date <span class="text-sky-400">*</span></label>
          <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full">
        </div>
        <div style="background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.2);border-radius:8px;padding:10px 12px;">
          <p class="text-xs text-sky-300">
            <strong>Note:</strong> If the selected driver is currently assigned to another van,
            you will be asked to confirm the reassignment.
          </p>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Assign Driver</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openAssignForVan(vanId, plate) {
  document.getElementById('f_van').value = vanId;
  document.getElementById('assignModalLabel').textContent = 'Assigning to ' + plate;
  document.getElementById('assignModal').classList.remove('hidden');
}
</script>
@endsection

