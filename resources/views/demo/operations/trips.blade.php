@extends('demo.layout')
@section('title','Trip Entries')
@section('page-title','Operations — Trip Entries')
@section('page-subtitle','Log and manage daily courier trips')

@section('content')

{{-- ── Success / Error banners ── --}}
@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  {{ session('error') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  <strong>Please fix the following errors:</strong>
  <ul style="margin:6px 0 0 18px;list-style:disc;">
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="card overflow-hidden">

  {{-- ── Filter bar + Log Trip button ── --}}
  <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-end gap-3 flex-wrap justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <form method="GET" action="{{ route('operations.trips') }}" id="filterForm"
          class="flex flex-col sm:flex-row sm:items-end gap-3 flex-wrap justify-between w-full">

      <div class="flex gap-2 flex-wrap items-end">
        {{-- Van filter --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Van</label>
          <select name="van_id" class="text-sm" onchange="document.getElementById('filterForm').submit()">
            <option value="">All Vans</option>
            @foreach($vans as $van)
              <option value="{{ $van->id }}" {{ request('van_id') == $van->id ? 'selected' : '' }}>
                {{ $van->plate_number }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Customer filter --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Customer</label>
          <select name="customer_id" class="text-sm" onchange="document.getElementById('filterForm').submit()">
            <option value="">All Customers</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                {{ $customer->company_name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Date from --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">From</label>
          <input type="date" name="from" class="text-sm" value="{{ request('from') }}"
                 onchange="document.getElementById('filterForm').submit()">
        </div>

        {{-- Date to --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">To</label>
          <input type="date" name="to" class="text-sm" value="{{ request('to') }}"
                 onchange="document.getElementById('filterForm').submit()">
        </div>

        {{-- Status filter --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
          <select name="status" class="text-sm" onchange="document.getElementById('filterForm').submit()">
            <option value="">All Statuses</option>
            <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Active</option>
            <option value="voided"  {{ request('status') === 'voided'  ? 'selected' : '' }}>Voided</option>
          </select>
        </div>

        {{-- Clear filters --}}
        @if(request()->hasAny(['van_id','customer_id','from','to','status']))
        <a href="{{ route('operations.trips') }}"
           class="btn-ghost text-xs self-end" style="padding:6px 12px;">Clear</a>
        @endif
      </div>

      {{-- Log Trip button --}}
      <button type="button" onclick="modal('tripModal',true)" class="btn-primary self-end">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Log Trip
      </button>
    </form>
  </div>

  {{-- ── Table ── --}}
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Trip ID</th>
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Customer</th>
          <th class="px-5 py-3 text-left">Route</th>
          <th class="px-5 py-3 text-right">Fare</th>
          <th class="px-5 py-3 text-right">{{ $tenantSettings->tax_label }}</th>
          <th class="px-5 py-3 text-right">Total</th>
          <th class="px-5 py-3 text-center">Status</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($trips as $trip)
        <tr class="table-row {{ $trip->status === 'voided' ? 'opacity-60' : '' }}">

          {{-- Trip ID --}}
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs text-sky-400 font-semibold"
                  style="{{ $trip->status === 'voided' ? 'text-decoration:line-through;' : '' }}">
              T-{{ str_pad($trip->id, 5, '0', STR_PAD_LEFT) }}
            </span>
          </td>

          {{-- Date --}}
          <td class="px-5 py-3.5 text-xs text-slate-400">
            {{ $trip->trip_date->format('d M Y') }}
          </td>

          {{-- Van plate --}}
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs px-2 py-0.5 rounded font-semibold text-slate-300" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);">
              {{ $trip->van->plate_number ?? '—' }}
            </span>
          </td>

          {{-- Customer --}}
          <td class="px-5 py-3.5 text-slate-200 text-xs font-medium">
            {{ $trip->customer->company_name ?? '—' }}
          </td>

          {{-- Route --}}
          <td class="px-5 py-3.5 text-xs text-slate-400">
            {{ $trip->origin }} → {{ $trip->destination }}
          </td>

          {{-- Fare --}}
          <td class="px-5 py-3.5 text-right text-slate-300 text-xs">
            {{ $currencySymbol }}{{ number_format($trip->fare_amount, 2) }}
          </td>

          {{-- Tax --}}
          <td class="px-5 py-3.5 text-right text-slate-400 text-xs">
            {{ $currencySymbol }}{{ number_format($trip->tax_amount, 2) }}
          </td>

          {{-- Total --}}
          <td class="px-5 py-3.5 text-right font-semibold text-slate-100 text-xs">
            {{ $currencySymbol }}{{ number_format($trip->total_amount, 2) }}
          </td>

          {{-- Status badge --}}
          <td class="px-5 py-3.5 text-center">
            @if($trip->status === 'active')
              <span class="badge-blue">Active</span>
            @else
              <span class="badge-blue">Voided</span>
            @endif
          </td>

          {{-- Actions --}}
          <td class="px-5 py-3.5 text-right">
            @if($trip->status === 'active')
            <form method="POST" action="{{ route('operations.trips.void', $trip) }}"
                  onsubmit="return confirm('Void trip T-{{ str_pad($trip->id, 5, '0', STR_PAD_LEFT) }}? This cannot be undone.');"
                  style="display:inline;">
              @csrf
              <button type="submit"
                      class="text-xs text-slate-500 hover:text-sky-400 font-medium transition-colors">
                Void
              </button>
            </form>
            @else
            <span class="text-xs text-slate-600">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" class="px-5 py-12 text-center text-slate-500 text-sm">
            <svg width="40" height="40" fill="none" stroke="#475569" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 10px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <circle cx="12" cy="11" r="3"/>
            </svg>
            No trips found. Adjust your filters or log a new trip.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ── Pagination footer ── --}}
  <div class="px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500" style="border-top:1px solid rgba(255,255,255,0.07);">
    <span>
      Showing
      <strong class="text-slate-300">{{ $trips->firstItem() ?? 0 }}</strong>–<strong class="text-slate-300">{{ $trips->lastItem() ?? 0 }}</strong>
      of <strong class="text-slate-300">{{ $trips->total() }}</strong> trip{{ $trips->total() !== 1 ? 's' : '' }}
    </span>
    @if($trips->hasPages())
    <div class="flex gap-1">
      {{-- Previous --}}
      @if($trips->onFirstPage())
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">‹</span>
      @else
        <a href="{{ $trips->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">‹</a>
      @endif

      {{-- Page numbers --}}
      @foreach($trips->getUrlRange(max(1, $trips->currentPage()-2), min($trips->lastPage(), $trips->currentPage()+2)) as $page => $url)
        @if($page == $trips->currentPage())
          <span class="px-3 py-1.5 rounded-lg text-white font-semibold" style="background:linear-gradient(135deg,#0284c7,#2563eb);border:none;">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">{{ $page }}</a>
        @endif
      @endforeach

      {{-- Next --}}
      @if($trips->hasMorePages())
        <a href="{{ $trips->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">›</a>
      @else
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">›</span>
      @endif
    </div>
    @endif
  </div>

</div>
@endsection

{{-- ════════════════ MODALS ════════════════ --}}
@section('modals')
<div id="tripModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-lg" style="max-height:90vh;overflow-y:auto;">

    {{-- Modal header --}}
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 class="font-semibold text-slate-100 text-sm">Log New Trip</h2>
      <button onclick="modal('tripModal',false)" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- Modal form --}}
    <form method="POST" action="{{ route('operations.trips.store') }}" id="tripForm">
      @csrf
      <div class="p-5 space-y-4">

        {{-- Van + Date --}}
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">
              Van (Active only) <span class="text-sky-400">*</span>
            </label>
            <select name="van_id" required class="w-full">
              <option value="">— Select Van —</option>
              @foreach($vans as $van)
              <option value="{{ $van->id }}" {{ old('van_id') == $van->id ? 'selected' : '' }}>
                {{ $van->plate_number }} — {{ $van->make_model }}
              </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">
              Trip Date <span class="text-sky-400">*</span>
            </label>
            <input type="date" name="trip_date" required class="w-full"
                   value="{{ old('trip_date', date('Y-m-d')) }}">
          </div>
        </div>

        {{-- Customer --}}
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">
            Customer <span class="text-sky-400">*</span>
          </label>
          <select name="customer_id" required class="w-full">
            <option value="">— Select Customer —</option>
            @foreach($customers as $customer)
            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
              {{ $customer->company_name }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Origin + Destination --}}
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">
              Origin <span class="text-sky-400">*</span>
            </label>
            <input type="text" name="origin" required maxlength="255"
                   placeholder="e.g. Nairobi CBD"
                   value="{{ old('origin') }}" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">
              Destination <span class="text-sky-400">*</span>
            </label>
            <input type="text" name="destination" required maxlength="255"
                   placeholder="e.g. Mombasa"
                   value="{{ old('destination') }}" class="w-full">
          </div>
        </div>

        {{-- Fare amount + live tax preview --}}
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">
            Fare Amount <span class="text-sky-400">*</span>
          </label>
          <input type="number" name="fare_amount" id="fareInput" required
                 min="0.01" max="9999999" step="0.01" placeholder="0.00"
                 value="{{ old('fare_amount') }}"
                 class="w-full" oninput="calcTax(this)">

          {{-- Live tax/total preview --}}
          <div style="margin-top:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:10px 14px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#94a3b8;margin-bottom:4px;">
              <span>Pre-tax fare</span>
              <span id="previewFare" class="text-slate-200">0.00</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#94a3b8;margin-bottom:4px;">
              <span>{{ $tenantSettings->tax_label }} ({{ $tenantSettings->tax_rate }}%)</span>
              <span id="previewTax" style="color:#7dd3fc;">+ 0.00</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:#f8fafc;border-top:1px solid rgba(255,255,255,0.08);padding-top:6px;margin-top:4px;">
              <span>Total</span>
              <span id="previewTotal" class="text-sky-400">0.00</span>
            </div>
          </div>
        </div>

      </div>

      {{-- Modal footer --}}
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="modal('tripModal',false)" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Trip</button>
      </div>
    </form>

  </div>
</div>
@endsection

@section('scripts')
<script>
function modal(id, s) {
  document.getElementById(id).classList.toggle('hidden', !s);
}

function calcTax(el) {
  const fare  = parseFloat(el.value) || 0;
  const tax   = Math.round(fare * {{ (float) $tenantSettings->tax_rate / 100 }} * 100) / 100;
  const total = Math.round((fare + tax) * 100) / 100;

  document.getElementById('previewFare').textContent  = fare.toFixed(2);
  document.getElementById('previewTax').textContent   = '+ ' + tax.toFixed(2);
  document.getElementById('previewTotal').textContent = total.toFixed(2);
}

// Auto-open modal on validation error (old input present)
@if($errors->any())
document.addEventListener('DOMContentLoaded', function () {
  modal('tripModal', true);
});
@endif

// Re-calculate if old value is pre-filled
document.addEventListener('DOMContentLoaded', function () {
  const fareInput = document.getElementById('fareInput');
  if (fareInput && fareInput.value) {
    calcTax(fareInput);
  }
});
</script>
@endsection

