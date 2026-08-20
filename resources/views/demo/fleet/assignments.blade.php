@extends('demo.layout')
@section('title','Driver Assignments')
@section('page-title','Fleet — Driver Assignments')
@section('page-subtitle','Manage van-to-driver mappings and view assignment history')

@section('content')

{{-- Success / Error banners --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

{{-- Reassign confirmation banner --}}
@if(session('confirm_reassign'))
@php $cr = session('confirm_reassign'); @endphp
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;margin-bottom:16px;">
  <p class="text-sm font-semibold text-amber-800 mb-2">
    ⚠ {{ $cr['driver_name'] }} is currently assigned to <strong>{{ $cr['current_van'] }}</strong>.
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
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Vans</p>
    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $vans->count() }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Assigned</p>
    <p class="text-2xl font-bold mt-1" style="color:#16a34a;">{{ $activeCount }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Unassigned</p>
    <p class="text-2xl font-bold mt-1" style="color:#b45309;">{{ $unassignedCount }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Available Drivers</p>
    <p class="text-2xl font-bold mt-1" style="color:#1d4ed8;">{{ $availableDrivers->count() }}</p>
  </div>
</div>

{{-- Current Assignments table --}}
<div class="card overflow-hidden mb-5">
  <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
    <div>
      <h3 class="font-semibold text-gray-800 text-sm">Current Assignments</h3>
      <p class="text-xs text-gray-400 mt-0.5">One active driver per van at any time</p>
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
      <tbody class="divide-y divide-gray-50">
        @foreach($vans as $van)
        @php
          $active = $van->assignments->firstWhere('end_date', null);
          $vanStatusStyle = match($van->status) {
            'active'      => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
            'maintenance' => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
            'leased'      => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;',
            default       => '',
          };
          $duration = $active
            ? \Carbon\Carbon::parse($active->start_date)->diffForHumans(now(), \Carbon\CarbonInterface::DIFF_ABSOLUTE)
            : null;
        @endphp
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <span class="font-mono font-semibold text-xs bg-gray-100 text-gray-800 px-2.5 py-1 rounded-md">{{ $van->plate_number }}</span>
            <span class="block text-xs text-gray-400 mt-0.5">{{ $van->make_model }}</span>
          </td>
          <td class="px-5 py-3.5">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize" style="{{ $vanStatusStyle }}">
              {{ ucfirst($van->status) }}
            </span>
          </td>
          <td class="px-5 py-3.5 font-semibold text-gray-800">
            {{ $active?->driver?->full_name ?? '—' }}
          </td>
          <td class="px-5 py-3.5 font-mono text-xs text-gray-500">
            {{ $active?->driver?->licence_number ?? '—' }}
          </td>
          <td class="px-5 py-3.5 text-xs text-gray-500">
            {{ $active ? \Carbon\Carbon::parse($active->start_date)->format('d M Y') : '—' }}
          </td>
          <td class="px-5 py-3.5 text-xs text-gray-500">
            {{ $duration ?? '—' }}
          </td>
          <td class="px-5 py-3.5 text-center">
            @if($active)
              <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2.5 py-1 rounded-full">Active</span>
            @else
              <span style="background:#f9fafb;color:#6b7280;border:1px solid #e5e7eb;" class="text-xs font-semibold px-2.5 py-1 rounded-full">Unassigned</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <button onclick="openAssignForVan({{ $van->id }}, '{{ $van->plate_number }}')"
              class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
              {{ $active ? 'Reassign' : 'Assign' }}
            </button>
            @if($active)
            <form method="POST" action="{{ route('fleet.assignments.end', $active->id) }}" class="inline"
                  onsubmit="return confirm('End assignment for {{ $active->driver->full_name }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium">End</button>
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
  <div class="px-5 py-4 border-b border-gray-100">
    <h3 class="font-semibold text-gray-800 text-sm">Assignment History</h3>
    <p class="text-xs text-gray-400 mt-0.5">All past and current assignments across the fleet</p>
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
      <tbody class="divide-y divide-gray-50">
        @foreach($vans as $van)
          @foreach($van->assignments as $asgn)
          <tr class="table-row {{ $asgn->end_date ? 'opacity-70' : '' }}">
            <td class="px-5 py-3">
              <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $van->plate_number }}</span>
            </td>
            <td class="px-5 py-3 text-gray-700 font-medium">{{ $asgn->driver?->full_name ?? '—' }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">{{ \Carbon\Carbon::parse($asgn->start_date)->format('d M Y') }}</td>
            <td class="px-5 py-3 text-xs text-gray-500">
              {{ $asgn->end_date ? \Carbon\Carbon::parse($asgn->end_date)->format('d M Y') : '—' }}
            </td>
            <td class="px-5 py-3 text-center">
              @if(!$asgn->end_date)
                <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
              @else
                <span style="background:#f9fafb;color:#6b7280;border:1px solid #e5e7eb;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Ended</span>
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
<div id="assignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <div>
        <h2 class="font-semibold text-gray-800 text-sm">Assign Driver to Van</h2>
        <p id="assignModalLabel" class="text-xs text-gray-400 mt-0.5">Select van and driver</p>
      </div>
      <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form method="POST" action="{{ route('fleet.assignments.assign') }}">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Van <span class="text-red-500">*</span></label>
          <select name="van_id" id="f_van" required class="w-full">
            <option value="">— Select Van —</option>
            @foreach($vans as $van)
              <option value="{{ $van->id }}">{{ $van->plate_number }} — {{ $van->make_model }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Driver <span class="text-red-500">*</span></label>
          <select name="driver_id" required class="w-full">
            <option value="">— Select Driver —</option>
            @foreach($allDrivers as $driver)
              <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Start Date <span class="text-red-500">*</span></label>
          <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full">
        </div>
        <div style="background:#eff6ff;border:1px solid #dbeafe;border-radius:8px;padding:10px 12px;">
          <p class="text-xs text-blue-700">
            <strong>Note:</strong> If the selected driver is currently assigned to another van,
            you will be asked to confirm the reassignment.
          </p>
        </div>
      </div>
      <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
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
