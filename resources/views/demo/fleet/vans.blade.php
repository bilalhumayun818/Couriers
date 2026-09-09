@extends('demo.layout')
@section('title','Vehicles')
@section('page-title','Fleet Management — Vehicles')
@section('page-subtitle','Browse vehicles by model category')

@section('content')

@if(session('success'))
<div style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.35);color:#34d399;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div class="card px-5 py-4 mb-5 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
  <div>
    <p class="text-sm text-slate-300">
      <strong>{{ $totalModels }}</strong> vehicle {{ Str::plural('model', $totalModels) }}
      &bull; <strong>{{ $totalVans }}</strong> total vehicles
    </p>
    <p class="text-xs text-slate-500 mt-0.5">Click a model card to view all vehicles of that type</p>
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
    <svg width="48" height="48" fill="none" stroke="#334155" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
    </svg>
    <p class="text-slate-400 font-medium">No vehicles registered yet.</p>
    <button onclick="modal('vanModal',true)" class="btn-primary mt-4 mx-auto">Add First Vehicle</button>
  </div>
@else

{{-- Model cards grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
  @foreach($models as $m)
  @php $slug = rawurlencode($m['model']); @endphp
  <a href="/fleet/vans/model/{{ $slug }}"
     style="text-decoration:none;"
     class="card p-5 flex flex-col gap-4 cursor-pointer group" style="transition:all .2s;">

    {{-- Top: icon + model name --}}
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.25);">
          <svg width="22" height="22" fill="none" stroke="#38bdf8" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
          </svg>
        </div>
        <div>
          <h3 class="font-bold text-slate-100 text-sm leading-tight group-hover:text-sky-300 transition-colors">{{ $m['model'] }}</h3>
          <p class="text-xs text-slate-500 mt-0.5">{{ $m['total'] }} {{ $m['total'] === 1 ? 'vehicle' : 'vehicles' }}</p>
        </div>
      </div>
      <svg width="16" height="16" fill="none" stroke="#475569" stroke-width="2" viewBox="0 0 24 24"
           class="group-hover:stroke-sky-400 transition-colors mt-1 flex-shrink-0">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/>
      </svg>
    </div>

    {{-- Status pills --}}
    <div class="flex gap-2 flex-wrap">
      @if($m['active'] > 0)
        <span class="badge-green">● {{ $m['active'] }} Active</span>
      @endif
      @if($m['maintenance'] > 0)
        <span class="badge-amber">⚠ {{ $m['maintenance'] }} Maintenance</span>
      @endif
      @if($m['leased'] > 0)
        <span class="badge-blue">◆ {{ $m['leased'] }} Leased</span>
      @endif
    </div>

    {{-- Plate previews --}}
    <div class="flex flex-wrap gap-1.5">
      @foreach($m['plates'] as $plate)
        <span class="font-mono text-xs px-2 py-0.5 rounded" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;">{{ $plate }}</span>
      @endforeach
    </div>

    {{-- Footer --}}
    <div class="pt-3 flex items-center justify-between" style="border-top:1px solid rgba(255,255,255,0.07);">
      <span class="text-xs text-slate-500">View all vehicles →</span>
      <span class="text-xs font-semibold text-sky-400">{{ $m['total'] }} total</span>
    </div>
  </a>
  @endforeach
</div>
@endif

@endsection

@section('modals')
<div id="vanModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 class="font-semibold text-slate-100 text-sm">Add New Vehicle</h2>
      <button onclick="modal('vanModal',false)" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form method="POST" action="{{ route('fleet.vans.store') }}">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Plate Number <span class="text-red-400">*</span></label>
          <input type="text" name="plate_number" placeholder="e.g. KAA-123A" required class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Make / Model <span class="text-red-400">*</span></label>
          <input type="text" name="make_model" placeholder="e.g. Toyota HiAce" required class="w-full">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Year <span class="text-red-400">*</span></label>
            <input type="number" name="year" placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') }}" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Status <span class="text-red-400">*</span></label>
            <select name="status" required class="w-full">
              <option value="active">Active</option>
              <option value="maintenance">Maintenance</option>
              <option value="leased">Leased</option>
            </select>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
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
