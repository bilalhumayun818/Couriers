@extends('demo.layout')
@section('title','Advances & Wages')
@section('page-title','Operations — Advances & Wage Payouts')
@section('page-subtitle','Driver cash advances and monthly net wage calculations')

@section('content')

@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  {{ session('success') }}
</div>
@endif

{{-- Negative payout confirmation banner --}}
@if(session('negative_warning'))
@php $nw = session('negative_warning'); @endphp
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.3);border-radius:10px;padding:14px 18px;margin-bottom:16px;">
  <p class="text-sm font-semibold text-blue-300 mb-1">Negative Net Payout</p>
  <p class="text-xs text-blue-200 mb-3">
    Driver has advances of <strong>{{ $currencySymbol }}{{ number_format($nw['advances'],2) }}</strong> which exceed gross wage of
    <strong>{{ $currencySymbol }}{{ number_format($nw['gross_wage'],2) }}</strong>.
    Net payout would be <strong class="text-red-400">{{ $currencySymbol }}{{ number_format($nw['net'],2) }}</strong>.
    Confirm to proceed.
  </p>
  <form method="POST" action="{{ route('operations.wages.payout') }}" class="inline">
    @csrf
    <input type="hidden" name="driver_id"  value="{{ $nw['driver_id'] }}">
    <input type="hidden" name="pay_period" value="{{ $nw['pay_period'] }}">
    <input type="hidden" name="gross_wage" value="{{ $nw['gross_wage'] }}">
    <input type="hidden" name="confirmed"  value="1">
    <button type="submit" class="btn-primary text-xs px-3 py-1.5 mr-2">Confirm & Save Payout</button>
    <a href="{{ route('operations.wages') }}" class="btn-ghost text-xs px-3 py-1.5">Cancel</a>
  </form>
</div>
@endif

{{-- Period selector --}}
<div class="card px-5 py-4 mb-5 flex flex-wrap items-center gap-4">
  <span class="text-sm font-semibold text-slate-200">Pay Period:</span>
  <form method="GET" action="{{ route('operations.wages') }}" class="flex gap-2 items-center">
    <select name="pay_period" class="text-sm" onchange="this.form.submit()">
      @foreach($periods as $p)
        <option value="{{ $p['value'] }}" {{ $selectedPeriod===$p['value']?'selected':'' }}>{{ $p['label'] }}</option>
      @endforeach
    </select>
  </form>
  <span class="text-xs text-slate-400">Showing data for <strong class="text-slate-200">{{ \Carbon\Carbon::createFromFormat('Y-m',$selectedPeriod)->format('F Y') }}</strong></span>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Advances</p>
    <p class="text-2xl font-bold text-red-400 mt-1">{{ $currencySymbol }}{{ number_format($totalAdvances,2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">{{ $advances->count() }} advance{{ $advances->count()!==1?'s':'' }}</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Gross Wages</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $currencySymbol }}{{ number_format($totalGross,2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">{{ $payouts->count() }} payout{{ $payouts->count()!==1?'s':'' }} processed</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Net Payouts</p>
    <p class="text-2xl font-bold text-emerald-400 mt-1">{{ $currencySymbol }}{{ number_format($totalNet,2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">After deducting advances</p>
  </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

  {{-- ── Driver Advances ── --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <div>
        <h3 class="font-semibold text-slate-100 text-sm">Driver Advances</h3>
        <p class="text-xs text-slate-400 mt-0.5">Cash advances issued — {{ \Carbon\Carbon::createFromFormat('Y-m',$selectedPeriod)->format('F Y') }}</p>
      </div>
      <button onclick="document.getElementById('advanceModal').classList.remove('hidden')" class="btn-primary text-xs px-3 py-1.5">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        New Advance
      </button>
    </div>
    <table class="w-full text-sm">
      <thead><tr class="table-header">
        <th class="px-4 py-3 text-left">Driver</th>
        <th class="px-4 py-3 text-left">Date</th>
        <th class="px-4 py-3 text-left">Purpose</th>
        <th class="px-4 py-3 text-right">Amount</th>
        <th class="px-4 py-3 text-right">Action</th>
      </tr></thead>
      <tbody>
        @forelse($advances as $adv)
        <tr class="table-row">
          <td class="px-4 py-3 font-medium text-slate-200">{{ $adv->driver->full_name }}</td>
          <td class="px-4 py-3 text-xs text-slate-400">{{ $adv->advance_date->format('d M Y') }}</td>
          <td class="px-4 py-3 text-xs text-slate-400">{{ $adv->purpose ?? '—' }}</td>
          <td class="px-4 py-3 text-right font-semibold text-red-400">{{ $currencySymbol }}{{ number_format($adv->amount,2) }}</td>
          <td class="px-4 py-3 text-right">
            <form method="POST" action="{{ route('operations.wages.advance.destroy',$adv) }}" class="inline"
                  onsubmit="return confirm('Remove this advance?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs text-slate-500 hover:text-sky-400 font-medium">Remove</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 text-xs">No advances for this period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- ── Wage Payouts ── --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <div>
        <h3 class="font-semibold text-slate-100 text-sm">Wage Payouts</h3>
        <p class="text-xs text-slate-400 mt-0.5">Net = Gross − Advances &bull; {{ \Carbon\Carbon::createFromFormat('Y-m',$selectedPeriod)->format('F Y') }}</p>
      </div>
      <button onclick="document.getElementById('payoutModal').classList.remove('hidden')" class="btn-primary text-xs px-3 py-1.5">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Calculate Payout
      </button>
    </div>
    <table class="w-full text-sm">
      <thead><tr class="table-header">
        <th class="px-4 py-3 text-left">Driver</th>
        <th class="px-4 py-3 text-right">Gross</th>
        <th class="px-4 py-3 text-right">Advances</th>
        <th class="px-4 py-3 text-right">Net Payout</th>
        <th class="px-4 py-3 text-center">Status</th>
      </tr></thead>
      <tbody>
        @forelse($payouts as $payout)
        <tr class="table-row">
          <td class="px-4 py-3 font-medium text-slate-200">{{ $payout->driver->full_name }}</td>
          <td class="px-4 py-3 text-right text-slate-300">{{ $currencySymbol }}{{ number_format($payout->gross_wage,2) }}</td>
          <td class="px-4 py-3 text-right text-red-400">−{{ $currencySymbol }}{{ number_format($payout->total_advances,2) }}</td>
          <td class="px-4 py-3 text-right font-bold {{ $payout->is_negative ? 'text-red-400' : 'text-emerald-400' }}">
            {{ $payout->is_negative ? '−' : '' }}{{ $currencySymbol }}{{ number_format(abs($payout->net_wage),2) }}
          </td>
          <td class="px-4 py-3 text-center">
            @if($payout->is_negative)
              <span class="financial-loss">Negative</span>
            @else
              <span class="badge-blue">Paid</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 text-xs">No payouts calculated for this period.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('modals')
{{-- New Advance Modal --}}
<div id="advanceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 class="font-semibold text-slate-100 text-sm">Log Driver Advance</h2>
      <button onclick="document.getElementById('advanceModal').classList.add('hidden')" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="{{ route('operations.wages.advance') }}">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Driver <span class="text-sky-400">*</span></label>
          <select name="driver_id" required class="w-full">
            <option value="">— Select Driver —</option>
            @foreach($drivers as $d)
              <option value="{{ $d->id }}">{{ $d->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Date <span class="text-sky-400">*</span></label>
            <input type="date" name="advance_date" required class="w-full" value="{{ date('Y-m-d') }}">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Amount ({{ $currencySymbol }}) <span class="text-sky-400">*</span></label>
            <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0.00" class="w-full">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Purpose</label>
          <input type="text" name="purpose" placeholder="e.g. Fuel advance" maxlength="255" class="w-full">
        </div>
        <input type="hidden" name="pay_period" value="{{ $selectedPeriod }}">
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="document.getElementById('advanceModal').classList.add('hidden')" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Advance</button>
      </div>
    </form>
  </div>
</div>

{{-- Calculate Payout Modal --}}
<div id="payoutModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 class="font-semibold text-slate-100 text-sm">Calculate Wage Payout</h2>
      <button onclick="document.getElementById('payoutModal').classList.add('hidden')" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="{{ route('operations.wages.payout') }}">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Driver <span class="text-sky-400">*</span></label>
          <select name="driver_id" id="po_driver" required class="w-full" onchange="fetchPreview()">
            <option value="">— Select Driver —</option>
            @foreach($drivers as $d)
              <option value="{{ $d->id }}">{{ $d->full_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Gross Wage ({{ $currencySymbol }}) <span class="text-sky-400">*</span></label>
          <input type="number" name="gross_wage" id="po_gross" required min="0" step="0.01" placeholder="0.00" class="w-full" oninput="fetchPreview()">
        </div>
        <input type="hidden" name="pay_period" value="{{ $selectedPeriod }}">

        {{-- Live preview --}}
        <div id="previewBox" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:12px 14px;">
          <div class="flex justify-between text-xs text-slate-400 mb-1.5"><span>Gross Wage</span><span id="pv_gross" class="text-slate-200">{{ $currencySymbol }}0.00</span></div>
          <div class="flex justify-between text-xs text-slate-400 mb-1.5"><span>Total Advances</span><span id="pv_adv" class="text-red-400">−{{ $currencySymbol }}0.00</span></div>
          <div class="flex justify-between text-sm font-bold pt-2 mt-1" style="border-top:1px solid rgba(255,255,255,0.08);" id="pv_net_row">
            <span class="text-slate-200">Net Payout</span><span id="pv_net" class="text-emerald-400">{{ $currencySymbol }}0.00</span>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="document.getElementById('payoutModal').classList.add('hidden')" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Payout</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
const PREVIEW_URL  = '{{ route("operations.wages.preview") }}';
const CSRF_TOKEN   = '{{ csrf_token() }}';
const PAY_PERIOD   = '{{ $selectedPeriod }}';

let previewTimer = null;

function fetchPreview() {
  clearTimeout(previewTimer);
  previewTimer = setTimeout(doPreview, 300);
}

function doPreview() {
  const driverId = document.getElementById('po_driver').value;
  const gross    = document.getElementById('po_gross').value;
  if (!driverId || !gross) return;

  fetch(PREVIEW_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
    body: JSON.stringify({ driver_id: driverId, pay_period: PAY_PERIOD, gross_wage: gross })
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('pv_gross').textContent = @json($currencySymbol) + data.gross_wage.toFixed(2);
    document.getElementById('pv_adv').textContent   = '−$' + data.total_advances.toFixed(2);
    const netEl = document.getElementById('pv_net');
    netEl.textContent = (data.is_negative ? '−$' : '$') + Math.abs(data.net_wage).toFixed(2);
    netEl.style.color = data.is_negative ? '#f87171' : '#34d399';
  })
  .catch(() => {});
}
</script>
@endsection

