@extends('demo.layout')
@section('title','Fixed Costs')
@section('page-title','Fleet — Fixed Costs Configuration')
@section('page-subtitle','Monthly lease, insurance, road tax and service schedules per vehicle')

@section('content')

@if(session('success'))
<div style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.35);color:#34d399;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#f87171;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  <strong>Validation errors:</strong>
  @foreach($errors->all() as $error)
    <div>• {{ $error }}</div>
  @endforeach
</div>
@endif

<div class="card overflow-hidden">
  <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <p class="text-sm text-slate-400">Fixed costs are prorated daily when computing Van-Wise Ledger reports.</p>
    <button onclick="openPicker()" class="btn-primary">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Update Costs
    </button>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Model</th>
          <th class="px-5 py-3 text-right">Monthly Lease</th>
          <th class="px-5 py-3 text-right">Road Tax (Annual)</th>
          <th class="px-5 py-3 text-right">Insurance / Mo</th>
          <th class="px-5 py-3 text-right">Total Fixed / Mo</th>
          <th class="px-5 py-3 text-left">Next Service Date</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($vans as $van)
        @php
          $fc          = $van->fixedCost;
          $serviceDate = $fc?->next_service_date;
          $serviceSoon = $serviceDate
            && $serviceDate->diffInDays(now(), false) >= -7
            && $serviceDate->isFuture();
          $totalFixed  = $fc?->total_monthly ?? 0;
        @endphp
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <span class="font-mono font-semibold text-xs px-2.5 py-1 rounded-md" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#cbd5e1;">
              {{ $van->plate_number }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-slate-300">{{ $van->make_model }}</td>
          <td class="px-5 py-3.5 text-right text-slate-400">
            {{ $fc ? '$'.number_format($fc->monthly_lease, 2) : '—' }}
          </td>
          <td class="px-5 py-3.5 text-right text-slate-400">
            {{ $fc ? '$'.number_format($fc->road_tax_annual, 2) : '—' }}
          </td>
          <td class="px-5 py-3.5 text-right text-slate-400">
            {{ $fc ? '$'.number_format($fc->insurance_monthly, 2) : '—' }}
          </td>
          <td class="px-5 py-3.5 text-right font-bold {{ $totalFixed > 0 ? 'text-slate-100' : 'text-slate-500' }}">
            {{ $totalFixed > 0 ? '$'.number_format($totalFixed, 2) : '—' }}
          </td>
          <td class="px-5 py-3.5 text-xs {{ $serviceSoon ? 'font-semibold text-amber-400' : 'text-slate-400' }}">
            {{ $serviceDate ? $serviceDate->format('d M Y') : '—' }}
            @if($serviceSoon)
              <span class="ml-1 badge-amber">⚠ Soon</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right">
            <button onclick="openCostModal({ id: {{ $van->id }}, plate: '{{ $van->plate_number }}', model: '{{ addslashes($van->make_model) }}', lease: '{{ $fc?->monthly_lease ?? '' }}', road_tax: '{{ $fc?->road_tax_annual ?? '' }}', insurance: '{{ $fc?->insurance_monthly ?? '' }}', service: '{{ $serviceDate ? $serviceDate->format('Y-m-d') : '' }}' })"
              class="text-xs text-sky-400 hover:text-sky-300 font-medium">
              {{ $fc ? 'Edit' : 'Set Costs' }}
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-5 py-10 text-center text-slate-500 text-sm">
            No vehicles found. <a href="{{ route('fleet.vans') }}" class="text-sky-400 hover:underline">Add vehicles first →</a>
          </td>
        </tr>
        @endforelse
      </tbody>

      {{-- Fleet totals footer --}}
      @if($vans->isNotEmpty())
      <tfoot style="background:rgba(15,23,42,0.7);border-top:1px solid rgba(255,255,255,0.12);">
        <tr>
          <td class="px-5 py-3.5 font-bold text-slate-300 text-xs" colspan="2">FLEET TOTALS / MONTH</td>
          <td class="px-5 py-3.5 text-right font-bold text-slate-200">${{ number_format($fleetTotals['lease'], 2) }}</td>
          <td class="px-5 py-3.5 text-right font-bold text-slate-200">${{ number_format($fleetTotals['road_tax'] * 12, 2) }}<span class="text-xs font-normal text-slate-500 ml-1">(annual)</span></td>
          <td class="px-5 py-3.5 text-right font-bold text-slate-200">${{ number_format($fleetTotals['insurance'], 2) }}</td>
          <td class="px-5 py-3.5 text-right font-extrabold text-sky-400 text-base">${{ number_format($fleetTotals['total'], 2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection

@section('modals')

{{-- ── Van Picker Modal (top "Update Costs" button) ── --}}
<div id="pickerModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 class="font-semibold text-slate-100 text-sm">Select a Van to Update</h2>
      <button onclick="document.getElementById('pickerModal').classList.add('hidden')" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="p-4 max-h-80 overflow-y-auto space-y-1.5">
      @foreach($vans as $van)
      @php $fc = $van->fixedCost; @endphp
      <button onclick="pickVan({{ $van->id }})"
        class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-left transition-colors"
        style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);">
        <div>
          <span class="font-mono font-semibold text-xs px-2 py-0.5 rounded" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;">{{ $van->plate_number }}</span>
          <span class="ml-2 text-sm text-slate-200">{{ $van->make_model }}</span>
        </div>
        <span class="text-xs {{ $fc ? 'text-emerald-400 font-medium' : 'text-slate-500' }}">
          {{ $fc ? '$'.number_format($fc->total_monthly, 2).'/mo' : 'No costs set' }}
        </span>
      </button>
      @endforeach
    </div>
  </div>
</div>

{{-- ── Edit Fixed Costs Modal ── --}}
<div id="costModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-lg">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h2 class="font-semibold text-slate-100 text-sm">Update Fixed Costs</h2>
        <p id="modalVanLabel" class="text-xs text-slate-400 mt-0.5"></p>
      </div>
      <button onclick="closeCostModal()" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <form id="costForm" method="POST" action="">
      @csrf
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Monthly Lease ($) <span class="text-red-400">*</span></label>
            <input type="number" name="monthly_lease" id="f_lease" step="0.01" min="0" placeholder="0.00" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Road Tax Annual ($) <span class="text-red-400">*</span></label>
            <input type="number" name="road_tax_annual" id="f_road_tax" step="0.01" min="0" placeholder="0.00" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Insurance Monthly ($) <span class="text-red-400">*</span></label>
            <input type="number" name="insurance_monthly" id="f_insurance" step="0.01" min="0" placeholder="0.00" required class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Next Service Date</label>
            <input type="date" name="next_service_date" id="f_service" class="w-full">
          </div>
        </div>
        {{-- Live total preview --}}
        <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:10px 14px;">
          <div class="flex justify-between text-xs text-slate-400 mb-1"><span>Monthly Lease</span><span id="p_lease" class="text-slate-200">$0.00</span></div>
          <div class="flex justify-between text-xs text-slate-400 mb-1"><span>Road Tax (÷12)</span><span id="p_road_tax" class="text-slate-200">$0.00</span></div>
          <div class="flex justify-between text-xs text-slate-400"><span>Insurance</span><span id="p_insurance" class="text-slate-200">$0.00</span></div>
          <div class="flex justify-between text-sm font-bold text-slate-100 border-t mt-2 pt-2" style="border-color:rgba(255,255,255,0.08);">
            <span>Total Fixed / Month</span><span id="p_total" class="text-sky-400">$0.00</span>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="closeCostModal()" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
// All van data embedded for the picker
const vanData = {
  @foreach($vans as $van)
  @php $fc = $van->fixedCost; @endphp
  {{ $van->id }}: {
    id:        {{ $van->id }},
    plate:     "{{ $van->plate_number }}",
    model:     "{{ $van->make_model }}",
    lease:     "{{ $fc?->monthly_lease ?? '' }}",
    road_tax:  "{{ $fc?->road_tax_annual ?? '' }}",
    insurance: "{{ $fc?->insurance_monthly ?? '' }}",
    service:   "{{ $fc?->next_service_date ? $fc->next_service_date->format('Y-m-d') : '' }}"
  },
  @endforeach
};

// Open van picker
function openPicker() {
  document.getElementById('pickerModal').classList.remove('hidden');
}

// User picked a van from the picker
function pickVan(vanId) {
  document.getElementById('pickerModal').classList.add('hidden');
  openCostModal(vanData[vanId]);
}

// Open cost modal with van data (from row Edit button OR picker)
function modal(id, vanId, data) {
  if (!vanId) {
    document.getElementById(id).classList.add('hidden');
    return;
  }
  openCostModal(data);
}

function openCostModal(data) {
  document.getElementById('costForm').action = '/fleet/vans/' + data.id + '/fixed-costs';
  document.getElementById('modalVanLabel').textContent = data.plate + ' — ' + data.model;
  document.getElementById('f_lease').value     = data.lease     || '';
  document.getElementById('f_road_tax').value  = data.road_tax  || '';
  document.getElementById('f_insurance').value = data.insurance || '';
  document.getElementById('f_service').value   = data.service   || '';
  calcTotal();
  document.getElementById('costModal').classList.remove('hidden');
}

function closeCostModal() {
  document.getElementById('costModal').classList.add('hidden');
}

function fmt(n) { return '$' + parseFloat(n || 0).toFixed(2); }

function calcTotal() {
  const lease     = parseFloat(document.getElementById('f_lease').value)     || 0;
  const roadTax   = parseFloat(document.getElementById('f_road_tax').value)  || 0;
  const insurance = parseFloat(document.getElementById('f_insurance').value) || 0;
  const monthly   = lease + (roadTax / 12) + insurance;
  document.getElementById('p_lease').textContent     = fmt(lease);
  document.getElementById('p_road_tax').textContent  = fmt(roadTax / 12);
  document.getElementById('p_insurance').textContent = fmt(insurance);
  document.getElementById('p_total').textContent     = fmt(monthly);
}

['f_lease','f_road_tax','f_insurance'].forEach(id => {
  document.getElementById(id).addEventListener('input', calcTotal);
});
</script>
@endsection

