@extends('demo.layout')
@section('title', $model . ' — Vehicles')
@section('page-title', $model)
@section('page-subtitle', 'All vehicles of this model')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('fleet.vans') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
    </svg>
    All Models
  </a>
  <span class="text-gray-300">/</span>
  <span class="text-gray-700 font-semibold">{{ $model }}</span>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total</p>
    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
    <p class="text-xs text-gray-400 mt-0.5">vehicles</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Active</p>
    <p class="text-2xl font-bold mt-1" style="color:#16a34a;">{{ $stats['active'] }}</p>
    <p class="text-xs text-gray-400 mt-0.5">in service</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Maintenance</p>
    <p class="text-2xl font-bold mt-1" style="color:#b45309;">{{ $stats['maintenance'] }}</p>
    <p class="text-xs text-gray-400 mt-0.5">off road</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Leased</p>
    <p class="text-2xl font-bold mt-1" style="color:#1d4ed8;">{{ $stats['leased'] }}</p>
    <p class="text-xs text-gray-400 mt-0.5">on lease</p>
  </div>
</div>

@if($vans->isEmpty())
  <div class="card p-12 text-center">
    <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
    </svg>
    <p class="text-gray-500 font-medium">No vehicles found for "{{ $model }}"</p>
    <a href="{{ route('fleet.vans') }}" class="mt-3 inline-block text-indigo-600 text-sm hover:underline">← Back to all models</a>
  </div>
@else
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-gray-800 text-sm">{{ $model }} — Vehicle List</h3>
        <p class="text-xs text-gray-400 mt-0.5">{{ $vans->count() }} {{ $vans->count() === 1 ? 'vehicle' : 'vehicles' }} registered</p>
      </div>
      <button onclick="modal('vanModal',true)" class="btn-primary text-xs px-3 py-1.5">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Add Van
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="table-header">
            <th class="px-5 py-3 text-left">Plate Number</th>
            <th class="px-5 py-3 text-left">Model</th>
            <th class="px-5 py-3 text-left">Year</th>
            <th class="px-5 py-3 text-left">Status</th>
            <th class="px-5 py-3 text-left">Assigned Driver</th>
            <th class="px-5 py-3 text-left">Next Service</th>
            <th class="px-5 py-3 text-right">Monthly Fixed Cost</th>
            <th class="px-5 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @foreach($vans as $van)
          @php
            $statusStyle = match($van->status) {
              'active'      => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
              'maintenance' => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
              'leased'      => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;',
              default       => 'background:#f3f4f6;color:#374151;',
            };
            $serviceDate  = $van->fixedCost?->next_service_date;
            $serviceSoon  = $serviceDate && $serviceDate->diffInDays(now(), false) >= -7 && $serviceDate->isFuture();
            $totalFixed   = $van->fixedCost?->total_monthly ?? 0;
          @endphp
          <tr class="table-row">
            <td class="px-5 py-3.5">
              <span class="font-mono font-semibold text-xs bg-gray-100 text-gray-800 px-2.5 py-1 rounded-md">{{ $van->plate_number }}</span>
            </td>
            <td class="px-5 py-3.5 text-gray-700 font-medium">{{ $van->make_model }}</td>
            <td class="px-5 py-3.5 text-gray-500">{{ $van->year }}</td>
            <td class="px-5 py-3.5">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-full capitalize" style="{{ $statusStyle }}">
                {{ ucfirst($van->status) }}
              </span>
            </td>
            <td class="px-5 py-3.5 text-gray-600">
              {{ $van->driver?->full_name ?? '—' }}
            </td>
            <td class="px-5 py-3.5 text-xs {{ $serviceSoon ? 'font-semibold' : 'text-gray-500' }}" style="{{ $serviceSoon ? 'color:#b45309;' : '' }}">
              {{ $serviceDate ? $serviceDate->format('d M Y') : '—' }}
              @if($serviceSoon)
                <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full" style="background:#fef9c3;color:#92400e;border:1px solid #fde68a;">Due Soon</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
              {{ $totalFixed > 0 ? '$' . number_format($totalFixed, 2) : '—' }}
            </td>
            <td class="px-5 py-3.5 text-right space-x-3">
              <button class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
              <form method="POST" action="{{ route('fleet.vans.destroy', $van) }}" class="inline"
                    onsubmit="return confirm('Delete {{ $van->plate_number }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="px-5 py-3.5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
      <span>Showing <strong>{{ $vans->count() }}</strong> vehicles</span>
      <a href="{{ route('fleet.vans') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">← Back to all models</a>
    </div>
  </div>
@endif

@endsection

@section('modals')
<div id="vanModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 class="font-semibold text-gray-800 text-sm">Add New Vehicle</h2>
      <button onclick="modal('vanModal',false)" class="text-gray-400 hover:text-gray-600">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form method="POST" action="{{ route('fleet.vans.store') }}">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Plate Number <span class="text-red-500">*</span></label>
          <input type="text" name="plate_number" placeholder="e.g. KAA-123A" required class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Make / Model <span class="text-red-500">*</span></label>
          <input type="text" name="make_model" value="{{ $model }}" required class="w-full">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Year <span class="text-red-500">*</span></label>
            <input type="number" name="year" placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') }}" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
            <select name="status" required class="w-full">
              <option value="active">Active</option>
              <option value="maintenance">Maintenance</option>
              <option value="leased">Leased</option>
            </select>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
        <button type="button" onclick="modal('vanModal',false)" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Vehicle</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}</script>
@endsection
