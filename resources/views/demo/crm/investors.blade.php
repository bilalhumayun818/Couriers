@extends('demo.layout')
@section('title','Investors & Directors')
@section('page-title','Stakeholders — Investors & Directors')
@section('page-subtitle','Capital contributions, profit distributions and retained equity balances')

@section('content')

@if(session('success'))
<div style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.35);color:#34d399;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.35);color:#f87171;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Capital</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">${{ number_format($totals['capital'] + $totals['injections'], 2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">Initial + injections</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Injections</p>
    <p class="text-2xl font-bold text-indigo-400 mt-1">${{ number_format($totals['injections'], 2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">Additional capital</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Distributions</p>
    <p class="text-2xl font-bold text-red-400 mt-1">${{ number_format($totals['distributions'], 2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">Profit payouts</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Retained Equity</p>
    <p class="text-2xl font-bold text-emerald-400 mt-1">${{ number_format($totals['retained'], 2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">Net balance</p>
  </div>
</div>

{{-- Investor cards --}}
<div class="space-y-4">
  @foreach($investors as $inv)
  @php
    $retained = $inv->retained_balance;
    $totalCapital = (float)$inv->initial_capital + $inv->total_injections;
    $txns = $inv->transactions;
    // Build running balance for ledger
    $running = (float)$inv->initial_capital;
    $ledger = [];
    foreach ($txns as $tx) {
      if ($tx->type === 'injection')    $running += (float)$tx->amount;
      else                               $running -= (float)$tx->amount;
      $ledger[] = ['tx' => $tx, 'balance' => $running];
    }
  @endphp
  <div class="card overflow-hidden">
    {{-- Header --}}
    <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:{{ $inv->role==='director'?'rgba(56,189,248,0.12)':'rgba(16,185,129,0.12)' }};border:1px solid {{ $inv->role==='director'?'rgba(56,189,248,0.25)':'rgba(16,185,129,0.25)' }};">
          <svg width="20" height="20" fill="none" stroke="{{ $inv->role==='director'?'#38bdf8':'#34d399' }}" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-slate-100 text-sm">{{ $inv->full_name }}</h3>
            @if($inv->role==='director')
              <span class="badge-blue capitalize">{{ $inv->role }}</span>
            @else
              <span class="badge-green capitalize">{{ $inv->role }}</span>
            @endif
          </div>
          <p class="text-xs text-slate-400 mt-0.5">{{ $inv->bank_name ?? '—' }} &bull; {{ $inv->bank_account_number ?? '—' }}</p>
        </div>
      </div>

      {{-- Quick stats --}}
      <div class="flex gap-4 text-sm">
        <div class="text-center">
          <p class="text-xs text-slate-400">Capital</p>
          <p class="font-bold text-slate-200">${{ number_format($totalCapital,2) }}</p>
        </div>
        <div class="text-center">
          <p class="text-xs text-slate-400">Distributed</p>
          <p class="font-bold text-red-400">−${{ number_format($inv->total_distributions,2) }}</p>
        </div>
        <div class="text-center">
          <p class="text-xs text-slate-400">Retained</p>
          <p class="font-bold text-emerald-400">${{ number_format($retained,2) }}</p>
        </div>
      </div>

      {{-- Action buttons --}}
      <div class="flex gap-2 flex-wrap">
        <button onclick="openTxModal('inject', {{ $inv->id }}, '{{ addslashes($inv->full_name) }}')"
          class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
          style="background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3);color:#818cf8;">
          + Inject Capital
        </button>
        <button onclick="openTxModal('distribute', {{ $inv->id }}, '{{ addslashes($inv->full_name) }}', {{ $retained }})"
          class="text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors"
          style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#f87171;">
          − Distribute
        </button>
        <button onclick="openEditModal({{ json_encode(['id'=>$inv->id,'full_name'=>$inv->full_name,'role'=>$inv->role,'bank_account_name'=>$inv->bank_account_name,'bank_account_number'=>$inv->bank_account_number,'bank_name'=>$inv->bank_name,'initial_capital'=>$inv->initial_capital]) }})"
          class="text-xs btn-ghost" style="padding:5px 10px;">Edit</button>
        <form method="POST" action="{{ route('crm.investors.destroy',$inv) }}" class="inline"
              onsubmit="return confirm('Delete {{ addslashes($inv->full_name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="text-xs text-slate-500 hover:text-red-400 font-medium px-2 py-1.5">Delete</button>
        </form>
      </div>
    </div>

    {{-- Ledger transactions --}}
    @if($txns->isNotEmpty())
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead><tr class="table-header">
          <th class="px-5 py-2.5 text-left">Date</th>
          <th class="px-5 py-2.5 text-left">Description</th>
          <th class="px-5 py-2.5 text-right">Injection</th>
          <th class="px-5 py-2.5 text-right">Distribution</th>
          <th class="px-5 py-2.5 text-right">Running Balance</th>
        </tr></thead>
        <tbody>
          {{-- Opening balance row --}}
          <tr style="background:rgba(255,255,255,0.02);">
            <td class="px-5 py-2 text-xs text-slate-400">{{ $inv->created_at->format('d M Y') }}</td>
            <td class="px-5 py-2 text-xs text-slate-400 italic">Opening — Initial Capital</td>
            <td class="px-5 py-2 text-right text-xs font-semibold text-indigo-400">${{ number_format($inv->initial_capital,2) }}</td>
            <td class="px-5 py-2 text-right text-xs text-slate-600">—</td>
            <td class="px-5 py-2 text-right text-xs font-bold text-slate-200">${{ number_format($inv->initial_capital,2) }}</td>
          </tr>
          @foreach($ledger as $row)
          <tr class="table-row">
            <td class="px-5 py-2.5 text-xs text-slate-400">{{ $row['tx']->transaction_date->format('d M Y') }}</td>
            <td class="px-5 py-2.5 text-xs text-slate-300">{{ $row['tx']->description ?? ucfirst($row['tx']->type) }}</td>
            <td class="px-5 py-2.5 text-right text-xs {{ $row['tx']->type==='injection'?'font-semibold text-indigo-400':'text-slate-600' }}">
              {{ $row['tx']->type==='injection' ? '$'.number_format($row['tx']->amount,2) : '—' }}
            </td>
            <td class="px-5 py-2.5 text-right text-xs {{ $row['tx']->type==='distribution'?'font-semibold text-red-400':'text-slate-600' }}">
              {{ $row['tx']->type==='distribution' ? '$'.number_format($row['tx']->amount,2) : '—' }}
            </td>
            <td class="px-5 py-2.5 text-right text-xs font-bold text-slate-200">${{ number_format($row['balance'],2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
    <div class="px-5 py-4 text-xs text-slate-500">No transactions yet. Use "Inject Capital" or "Distribute" to record transactions.</div>
    @endif
  </div>
  @endforeach

  @if($investors->isEmpty())
  <div class="card p-12 text-center">
    <p class="text-slate-400 text-sm">No investors registered yet.</p>
    <button onclick="openAddModal()" class="btn-primary mt-4 mx-auto">Add First Investor</button>
  </div>
  @endif
</div>

{{-- Add Investor button --}}
<div class="mt-4 flex justify-end">
  <button onclick="openAddModal()" class="btn-primary">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Add Investor / Director
  </button>
</div>

@endsection

@section('modals')
{{-- Add/Edit Investor --}}
<div id="invModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-lg" style="max-height:90vh;overflow-y:auto;">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 id="invModalTitle" class="font-semibold text-slate-100 text-sm">Add Investor / Director</h2>
      <button onclick="closeAll()" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="invForm" method="POST" action="{{ route('crm.investors.store') }}">
      @csrf
      <span id="invMethodSpan"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Full Name <span class="text-red-400">*</span></label>
            <input type="text" name="full_name" id="if_name" required maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Role <span class="text-red-400">*</span></label>
            <select name="role" id="if_role" required class="w-full">
              <option value="investor">Investor</option>
              <option value="director">Director</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Initial Capital ($)</label>
            <input type="number" name="initial_capital" id="if_capital" min="0" step="0.01" placeholder="0.00" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Bank Name</label>
            <input type="text" name="bank_name" id="if_bank" maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Account Number</label>
            <input type="text" name="bank_account_number" id="if_accno" maxlength="50" class="w-full">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Account Name</label>
            <input type="text" name="bank_account_name" id="if_accname" maxlength="255" class="w-full">
          </div>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="closeAll()" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- Transaction modal (inject / distribute) --}}
<div id="txModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h2 id="txModalTitle" class="font-semibold text-slate-100 text-sm">Record Transaction</h2>
        <p id="txModalSub" class="text-xs text-slate-400 mt-0.5"></p>
      </div>
      <button onclick="closeAll()" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="txForm" method="POST" action="">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Date <span class="text-red-400">*</span></label>
          <input type="date" name="transaction_date" required class="w-full" value="{{ date('Y-m-d') }}">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Amount ($) <span class="text-red-400">*</span></label>
          <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0.00" class="w-full" id="tx_amount">
          <p id="tx_balance_hint" class="text-xs text-slate-400 mt-1 hidden"></p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Description</label>
          <input type="text" name="description" maxlength="500" placeholder="e.g. Q3 profit distribution" class="w-full">
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="closeAll()" class="btn-ghost">Cancel</button>
        <button type="submit" id="txSubmitBtn" class="btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openAddModal() {
  document.getElementById('invModalTitle').textContent = 'Add Investor / Director';
  document.getElementById('invForm').action = '{{ route("crm.investors.store") }}';
  document.getElementById('invMethodSpan').innerHTML = '';
  document.getElementById('invForm').reset();
  document.getElementById('invModal').classList.remove('hidden');
}

function openEditModal(data) {
  document.getElementById('invModalTitle').textContent = 'Edit — ' + data.full_name;
  document.getElementById('invForm').action = '/crm/investors/' + data.id;
  document.getElementById('invMethodSpan').innerHTML = '<input type="hidden" name="_method" value="PUT">';
  document.getElementById('if_name').value    = data.full_name || '';
  document.getElementById('if_role').value    = data.role || 'investor';
  document.getElementById('if_capital').value = data.initial_capital || '';
  document.getElementById('if_bank').value    = data.bank_name || '';
  document.getElementById('if_accno').value   = data.bank_account_number || '';
  document.getElementById('if_accname').value = data.bank_account_name || '';
  document.getElementById('invModal').classList.remove('hidden');
}

function openTxModal(type, investorId, name, balance) {
  const isInject = type === 'injection' || type === 'inject';
  document.getElementById('txModalTitle').textContent = isInject ? '+ Inject Capital' : '− Record Distribution';
  document.getElementById('txModalSub').textContent   = name;
  document.getElementById('txForm').action = '/crm/investors/' + investorId + '/' + (isInject ? 'inject' : 'distribute');
  document.getElementById('txForm').reset();
  document.getElementById('tx_amount').value = '';

  const hint = document.getElementById('tx_balance_hint');
  if (!isInject && balance !== undefined) {
    hint.textContent = 'Available balance: $' + parseFloat(balance).toFixed(2);
    hint.classList.remove('hidden');
  } else {
    hint.classList.add('hidden');
  }

  const btn = document.getElementById('txSubmitBtn');
  btn.className = isInject ? 'btn-primary' : 'inline-flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-lg cursor-pointer text-white border-none';
  if (!isInject) btn.style.background = '#ef4444';

  document.getElementById('txModal').classList.remove('hidden');
}

function closeAll() {
  ['invModal','txModal'].forEach(id => document.getElementById(id).classList.add('hidden'));
}
</script>
@endsection

