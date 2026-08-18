@extends('demo.layout')
@section('title','Vehicles')
@section('page-title','Fleet Management — Vehicles')
@section('page-subtitle','Browse vehicles by model category')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="card px-5 py-4 mb-5 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
  <div>
    <p class="text-sm text-gray-600">
      <strong>{{ $totalModels }}</strong> vehicle {{ Str::plural('model', $totalModels) }}
      &bull; <strong>{{ $totalVans }}</strong> total vehicles
    </p>
    <p class="text-xs text-gray-400 mt-0.5">Click a model card to view all vehicles of that type</p>
  </div>
  <button onclick="modal('vanModal',true)" class="btn-primary">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    Add Vehicle
  </button>
</div>

@if($models->isEmpty())
  <div class="card p-12 text-center">
    <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
    </svg>
    <p class="text-gray-500 font-medium">No vehicles registered yet.</p>
    <button onclick="modal('vanModal',true)" class="btn-primary mt-4 mx-auto">Add First Vehicle</button>
  </div>
@else

{{-- Model cards grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
  @foreach($models as $m)
  @php $slug = rawurlencode($m['model']); @endphp
  <a href="/fleet/vans/model/{{ $slug }}"
     style="text-decoration:none;"
     class="card p-5 flex flex-col gap-4 hover:shadow-md hover:border-indigo-200 transition-all duration-150 cursor-pointer group">

    {{-- Top: icon + model name --}}
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-100 transition-colors flex-shrink-0">
          <svg width="22" height="22" fill="none" stroke="#6366f1" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-gray-900 text-sm leading-tight">{{ $m['model'] }}</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $m['total'] }} {{ $m['total'] === 1 ? 'vehicle' : 'vehicles' }}</p>
        </div>
      </div>
      <svg width="16" height="16" fill="none" stroke="#9ca3af" stroke-width="2" viewBox="0 0 24 24"
           class="group-hover:stroke-indigo-500 transition-colors mt-1 flex-shrink-0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
      </svg>
    </div>

    {{-- Status pills --}}
    <div class="flex gap-2 flex-wrap">
      @if($m['active'] > 0)
        <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2.5 py-1 rounded-full">
          ● {{ $m['active'] }} Active
        </span>
      @endif
      @if($m['maintenance'] > 0)
        <span style="background:#fffbeb;color:#b45309;border:1px solid #fde68a;" class="text-xs font-semibold px-2.5 py-1 rounded-full">
          ⚠ {{ $m['maintenance'] }} Maintenance
        </span>
      @endif
      @if($m['leased'] > 0)
        <span style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;" class="text-xs font-semibold px-2.5 py-1 rounded-full">
          ◆ {{ $m['leased'] }} Leased
        </span>
      @endif
    </div>

    {{-- Plate previews --}}
    <div class="flex flex-wrap gap-1.5">
      @foreach($m['plates'] as $plate)
        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">{{ $plate }}</span>
      @endforeach
    </div>

    {{-- Footer --}}
    <div class="border-t border-gray-100 pt-3 flex items-center justify-between">
      <span class="text-xs text-gray-400">View all vehicles →</span>
      <span class="text-xs font-semibold text-indigo-600">{{ $m['total'] }} total</span>
    </div>
  </a>
  @endforeach
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
          <input type="text" name="make_model" placeholder="e.g. Toyota HiAce" required class="w-full">
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
