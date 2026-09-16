@extends('demo.layout')
@section('title', $model . ' — Vehicles')
@section('page-title', $model)
@section('page-subtitle', 'All vehicles of this model')

@section('content')

@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif

{{-- Breadcrumb --}}
<div class="flex items-center gap-2 mb-5 text-sm">
  <a href="{{ route('fleet.vans') }}" class="text-sky-400 hover:text-sky-300 font-medium flex items-center gap-1">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
    </svg>
    All Models
  </a>
  <span class="text-slate-600">/</span>
  <span class="text-slate-200 font-semibold">{{ $model }}</span>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $stats['total'] }}</p>
    <p class="text-xs text-slate-500 mt-0.5">vehicles</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Active</p>
    <p class="text-2xl font-bold text-sky-400 mt-1">{{ $stats['active'] }}</p>
    <p class="text-xs text-slate-500 mt-0.5">in service</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Maintenance</p>
    <p class="text-2xl font-bold text-blue-400 mt-1">{{ $stats['maintenance'] }}</p>
    <p class="text-xs text-slate-500 mt-0.5">off road</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Leased</p>
    <p class="text-2xl font-bold text-sky-400 mt-1">{{ $stats['leased'] }}</p>
    <p class="text-xs text-slate-500 mt-0.5">on lease</p>
  </div>
</div>

@if($vans->isEmpty())
  <div class="card p-12 text-center">
    <svg width="48" height="48" fill="none" stroke="#475569" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM1 1h4l2.68 13.39a2 2 0 001.98 1.61h9.72a2 2 0 001.98-1.61L23 6H6"/>
    </svg>
    <p class="text-slate-400 font-medium">No vehicles found for "{{ $model }}"</p>
    <a href="{{ route('fleet.vans') }}" class="mt-3 inline-block text-sky-400 text-sm hover:underline">← Back to all models</a>
  </div>
@else
  <div class="card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <div>
        <h3 class="font-semibold text-slate-100 text-sm">{{ $model }} — Vehicle List</h3>
        <p class="text-xs text-slate-400 mt-0.5">{{ $vans->count() }} {{ $vans->count() === 1 ? 'vehicle' : 'vehicles' }} registered</p>
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
        <tbody>
          @foreach($vans as $van)
          @php
            $serviceDate  = $van->fixedCost?->next_service_date;
            $serviceSoon  = $serviceDate && $serviceDate->diffInDays(now(), false) >= -7 && $serviceDate->isFuture();
            $totalFixed   = $van->fixedCost?->total_monthly ?? 0;
          @endphp
          <tr class="table-row">
            <td class="px-5 py-3.5">
              <span class="font-mono font-semibold text-xs px-2.5 py-1 rounded-md" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">{{ $van->plate_number }}</span>
            </td>
            <td class="px-5 py-3.5 text-slate-200 font-medium">{{ $van->make_model }}</td>
            <td class="px-5 py-3.5 text-slate-400">{{ $van->year }}</td>
            <td class="px-5 py-3.5">
              @if($van->status === 'active')
                <span class="badge-blue capitalize">{{ $van->status }}</span>
              @elseif($van->status === 'maintenance')
                <span class="badge-amber capitalize">{{ $van->status }}</span>
              @else
                <span class="badge-blue capitalize">{{ $van->status }}</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-slate-300">
              {{ $van->driver?->full_name ?? '—' }}
            </td>
            <td class="px-5 py-3.5 text-xs {{ $serviceSoon ? 'font-semibold text-blue-400' : 'text-slate-400' }}">
              {{ $serviceDate ? $serviceDate->format('d M Y') : '—' }}
              @if($serviceSoon)
                <span class="ml-1 badge-amber">Due Soon</span>
              @endif
            </td>
            <td class="px-5 py-3.5 text-right font-semibold text-slate-200">
              {{ $totalFixed > 0 ? '$' . number_format($totalFixed, 2) : '—' }}
            </td>
            <td class="px-5 py-3.5 text-right space-x-3">
              <button class="text-xs text-sky-400 hover:text-sky-300 font-medium">Edit</button>
              <form method="POST" action="{{ route('fleet.vans.destroy', $van) }}" class="inline"
                    onsubmit="return confirm('Delete {{ $van->plate_number }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-slate-500 hover:text-sky-400 font-medium">Delete</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="px-5 py-3.5 flex items-center justify-between text-xs text-slate-400" style="border-top:1px solid rgba(255,255,255,0.07);">
      <span>Showing <strong>{{ $vans->count() }}</strong> vehicles</span>
      <a href="{{ route('fleet.vans') }}" class="text-sky-400 hover:text-sky-300 font-medium">← Back to all models</a>
    </div>
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
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Plate Number <span class="text-sky-400">*</span></label>
          <input type="text" name="plate_number" placeholder="e.g. KAA-123A" required class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Make / Model <span class="text-sky-400">*</span></label>
          <input type="text" name="make_model" value="{{ $model }}" required class="w-full">
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Year <span class="text-sky-400">*</span></label>
            <input type="number" name="year" placeholder="{{ date('Y') }}" min="1990" max="{{ date('Y') }}" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Status <span class="text-sky-400">*</span></label>
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

