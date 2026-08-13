@extends('demo.layout')
@section('title','Currency & Tax')
@section('page-title','System Settings — Currency & Tax')
@section('page-subtitle','Configure default currency and tax rates for this tenant')

@section('content')
<div class="max-w-2xl space-y-5">

  {{-- Currency Settings --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-800 text-sm">Currency Settings</h3>
      <p class="text-xs text-slate-400 mt-0.5">Applied to all monetary display fields across the platform</p>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Currency Code (ISO 4217)</label>
          <select class="w-full text-sm">
            <option selected>USD — US Dollar</option>
            <option>KES — Kenyan Shilling</option>
            <option>GBP — British Pound</option>
            <option>EUR — Euro</option>
            <option>ZAR — South African Rand</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Currency Symbol</label>
          <input type="text" value="$" class="w-full text-sm">
        </div>
      </div>
      <div class="bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
        <p class="text-xs text-slate-500">Preview: <span class="font-semibold text-slate-800">$1,500.00</span></p>
      </div>
    </div>
  </div>

  {{-- Tax Settings --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-800 text-sm">Tax / VAT Settings</h3>
      <p class="text-xs text-slate-400 mt-0.5">Tax is calculated and displayed on trip fares — not applied retroactively to historical records</p>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Default Tax Rate (%)</label>
          <input type="number" value="8.00" step="0.01" min="0" max="100" class="w-full text-sm" id="taxRate" oninput="previewTax()">
          <p class="text-xs text-slate-400 mt-1">Supports up to 2 decimal places (0–100)</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tax Label</label>
          <input type="text" value="VAT" class="w-full text-sm">
        </div>
      </div>
      <div class="bg-slate-50 rounded-lg px-4 py-3 border border-slate-200 space-y-1.5">
        <p class="text-xs text-slate-500 font-semibold mb-2">Tax Calculation Preview — Fare: $420.00</p>
        <div class="flex justify-between text-xs text-slate-600"><span>Pre-tax Fare</span><span>$420.00</span></div>
        <div class="flex justify-between text-xs text-slate-600"><span>Tax (<span id="rateLabel">8%</span>)</span><span id="taxPreview">$33.60</span></div>
        <div class="flex justify-between text-xs font-bold text-slate-800 border-t border-slate-200 pt-1.5"><span>Total</span><span id="totalPreview">$453.60</span></div>
      </div>
    </div>
  </div>

  {{-- Tenant Info --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="font-semibold text-slate-800 text-sm">Tenant Information</h3>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Company Name</label>
          <input type="text" value="Acme Logistics Ltd" class="w-full text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5">Tenant Slug</label>
          <input type="text" value="acme-logistics" class="w-full text-sm bg-slate-50" readonly>
        </div>
      </div>
    </div>
  </div>

  <div class="flex justify-end gap-3">
    <button class="btn-ghost">Discard Changes</button>
    <button class="btn-primary">Save Settings</button>
  </div>

  <p class="text-xs text-slate-400">
    ⚠ Currency and tax changes apply to <strong>new records only</strong> and will not retroactively alter historical ledger entries.
  </p>
</div>
@endsection

@section('scripts')
<script>
function previewTax() {
  const rate = parseFloat(document.getElementById('taxRate').value) || 0;
  const fare = 420;
  const tax = fare * (rate/100);
  document.getElementById('rateLabel').textContent = rate + '%';
  document.getElementById('taxPreview').textContent = '$' + tax.toFixed(2);
  document.getElementById('totalPreview').textContent = '$' + (fare + tax).toFixed(2);
}
</script>
@endsection
