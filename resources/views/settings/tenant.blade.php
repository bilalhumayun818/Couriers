@extends('demo.layout')
@section('title','Currency & Tax')
@section('page-title','System Settings — Currency & Tax')
@section('page-subtitle','Configure default currency and tax rates for this tenant')

@section('content')
@if(session('success'))<div role="status" class="card p-4 mb-4 text-sky-300">{{ session('success') }}</div>@endif
@if($errors->any())<div role="alert" class="card p-4 mb-4 text-sky-300"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('settings.tenant.update') }}" class="max-w-2xl space-y-5">
@csrf
@method('PUT')

  {{-- Currency Settings --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <h3 class="font-semibold text-slate-100 text-sm">Currency Settings</h3>
      <p class="text-xs text-slate-400 mt-0.5">Applied to all monetary display fields across the platform</p>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Currency Code (ISO 4217)</label>
          <select name="currency_code" class="w-full text-sm" required>
            @foreach(['USD' => 'US Dollar', 'KES' => 'Kenyan Shilling', 'GBP' => 'British Pound', 'EUR' => 'Euro', 'ZAR' => 'South African Rand'] as $code => $label)
            <option value="{{ $code }}" @selected(old('currency_code', $settings->currency_code) === $code)>{{ $code }} ? {{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Currency Symbol</label>
          <input type="text" name="currency_symbol" id="currencySymbol" value="{{ old('currency_symbol', $settings->currency_symbol) }}" required maxlength="10" oninput="previewTax()" class="w-full text-sm">
        </div>
      </div>
      <div class="rounded-lg px-4 py-3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
        <p class="text-xs text-slate-400">Preview: <span class="font-semibold text-slate-100"><span id="currencyPreview"></span></span></p>
      </div>
    </div>
  </div>

  {{-- Tax Settings --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <h3 class="font-semibold text-slate-100 text-sm">Tax / VAT Settings</h3>
      <p class="text-xs text-slate-400 mt-0.5">Tax is calculated and displayed on trip fares — not applied retroactively to historical records</p>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Default Tax Rate (%)</label>
          <input type="number" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate) }}" required step="0.01" min="0" max="100" class="w-full text-sm" id="taxRate" oninput="previewTax()">
          <p class="text-xs text-slate-400 mt-1">Supports up to 2 decimal places (0–100)</p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Tax Label</label>
          <input type="text" name="tax_label" value="{{ old('tax_label', $settings->tax_label) }}" required maxlength="40" class="w-full text-sm">
        </div>
      </div>
      <div class="rounded-lg px-4 py-3 space-y-1.5" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);">
        <p class="text-xs text-slate-300 font-semibold mb-2">Tax Calculation Preview — Fare: <span class="fare-preview"></span></p>
        <div class="flex justify-between text-xs text-slate-400"><span>Pre-tax Fare</span><span class="fare-preview"></span></div>
        <div class="flex justify-between text-xs text-slate-400"><span>Tax (<span id="rateLabel">8%</span>)</span><span id="taxPreview">$33.60</span></div>
        <div class="flex justify-between text-xs font-bold text-slate-100 pt-1.5" style="border-top:1px solid rgba(255,255,255,0.08);"><span>Total</span><span id="totalPreview">$453.60</span></div>
      </div>
    </div>
  </div>

  {{-- Tenant Info --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <h3 class="font-semibold text-slate-100 text-sm">Tenant Information</h3>
    </div>
    <div class="p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Company Name</label>
          <input type="text" name="company_name" value="{{ old('company_name', $settings->company_name) }}" required maxlength="255" class="w-full text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Tenant Slug</label>
          <input type="text" value="{{ \Illuminate\Support\Str::slug($settings->company_name) }}" class="w-full text-sm" style="background:rgba(255,255,255,0.04);opacity:0.7;" readonly>
        </div>
      </div>
    </div>
  </div>

  <div class="flex justify-end gap-3">
    <a href="{{ route('settings.tenant') }}" class="btn-ghost">Discard Changes</a>
    <button type="submit" class="btn-primary">Save Settings</button>
  </div>

  <p class="text-xs text-slate-400">
    ⚠ Tax changes apply to new trips only. Currency changes update display labels across the workspace; amounts are not converted or recalculated.
  </p>
</form>
@endsection

@section('scripts')
<script>
function previewTax() {
  const rate = parseFloat(document.getElementById('taxRate').value) || 0;
  const symbol = document.getElementById('currencySymbol').value;
  document.getElementById('currencyPreview').textContent = symbol + '1,500.00';
  document.querySelectorAll('.fare-preview').forEach(el => el.textContent = symbol + '420.00');
  const fare = 420;
  const tax = fare * (rate/100);
  document.getElementById('rateLabel').textContent = rate + '%';
  document.getElementById('taxPreview').textContent = symbol + tax.toFixed(2);
  document.getElementById('totalPreview').textContent = symbol + (fare + tax).toFixed(2);
}
previewTax();
</script>
@endsection
