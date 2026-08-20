@extends('demo.layout')
@section('title','Investors & Directors')
@section('page-title','Stakeholders — Investors & Directors')
@section('page-subtitle','Capital contributions, profit distributions and retained equity balances')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Capital</p>
    <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($totals['capital'] + $totals['injections'], 2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">Initial + injections</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Injections</p>
    <p class="text-2xl font-bold mt-1" style="color:#6366f1;">${{ number_format($totals['injections'], 2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">Additional capital</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Distributions</p>
    <p class="text-2xl font-bold mt-1" style="color:#ef4444;">${{ number_format($totals['distributions'], 2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">Profit payouts</p>
  </div>
  <div class="card p-4">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Retained Equity</p>
    <p class="text-2xl font-bold mt-1" style="color:#16a34a;">${{ number_format($totals['retained'], 2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">Net balance</p>
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
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
      <div class="flex items-center gap-3">
        <div style="width:40px;height:40px;background:{{ $inv->role==='director'?'#eff6ff':'#f0fdf4' }};border:1px solid {{ $inv->role==='director'?'#bfdbfe':'#bbf7d0' }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="20" height="20" fill="none" stroke="{{ $inv->role==='director'?'#3b82f6':'#16a34a' }}" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="font-bold text-gray-900 text-sm">{{ $inv->full_name }}</h3>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full capitalize"
                  style="{{ $inv->role==='director'?'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;':'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;' }}">
              {{ ucfirst($inv->role) }}
            </span>
          </div>
          <p class="text-xs text-gray-400 mt-0.5">{{ $inv->bank_name ?? '—' }} &bull; {{ $inv->bank_account_number ?? '—' }}</p>
        </div>
      </div>

      {{-- Quick stats --}}
      <div class="flex gap-4 text-sm">
        <div class="text-center">
          <p class="text-xs text-gray-400">Capital</p>
          <p class="font-bold text-gray-800">${{ number_format($totalCapital,2) }}</p>
        </div>
        <div class="text-center">
          <p class="text-xs text-gray-400">Distributed</p>
          <p class="font-bold text-red-500">−${{ number_format($inv->total_distributions,2) }}</p>
        </div>
        <div class="text-center">
          <p class="text-xs text-gray-400">Retained</p>
          <p class="font-bold text-emerald-700">${{ number_format($retained,2) }}</p>
        </div>
      </div>

      {{-- Action buttons --}}
      <div class="flex gap-2 flex-wrap">
        <button onclick="openTxModal('inject', {{ $inv->id }}, '{{ addslashes($inv->full_name) }}')"
          class="text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 font-semibold px-3 py-1.5 rounded-lg transition-colors">
          + Inject Capital
        </button>
        <button onclick="openTxModal('distribute', {{ $inv->id }}, '{{ addslashes($inv->full_name) }}', {{ $retained }})"
          class="text-xs bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 font-semibold px-3 py-1.5 rounded-lg transition-colors">
          − Distribute
        </button>
        <button onclick="openEditModal({{ json_encode(['id'=>$inv->id,'full_name'=>$inv->full_name,'role'=>$inv->role,'bank_account_name'=>$inv->bank_account_name,'bank_account_number'=>$inv->bank_account_number,'bank_name'=>$inv->bank_name,'initial_capital'=>$inv->initial_capital]) }})"
          class="text-xs btn-ghost" style="padding:5px 10px;">Edit</button>
        <form method="POST" action="{{ route('crm.investors.destroy',$inv) }}" class="inline"
              onsubmit="return confirm('Delete {{ addslashes($inv->full_name) }}?')">
          @csrf @method('DELETE')
          <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium px-2 py-1.5">Delete</button>
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
        <tbody class="divide-y divide-gray-50">
          {{-- Opening balance row --}}
          <tr style="background:#f8fafc;">
            <td class="px-5 py-2 text-xs text-gray-400">{{ $inv->created_at->format('d M Y') }}</td>
            <td class="px-5 py-2 text-xs text-gray-500 italic">Opening — Initial Capital</td>
            <td class="px-5 py-2 text-right text-xs font-semibold text-indigo-600">${{ number_format($inv->initial_capital,2) }}</td>
            <td class="px-5 py-2 text-right text-xs text-gray-300">—</td>
            <td class="px-5 py-2 text-right text-xs font-bold text-gray-800">${{ number_format($inv->initial_capital,2) }}</td>
          </tr>
          @foreach($ledger as $row)
          <tr class="table-row">
            <td class="px-5 py-2.5 text-xs text-gray-500">{{ $row['tx']->transaction_date->format('d M Y') }}</td>
            <td class="px-5 py-2.5 text-xs text-gray-600">{{ $row['tx']->description ?? ucfirst($row['tx']->type) }}</td>
            <td class="px-5 py-2.5 text-right text-xs {{ $row['tx']->type==='injection'?'font-semibold text-indigo-600':'text-gray-300' }}">
              {{ $row['tx']->type==='injection' ? '$'.number_format($row['tx']->amount,2) : '—' }}
            </td>
            <td class="px-5 py-2.5 text-right text-xs {{ $row['tx']->type==='distribution'?'font-semibold text-red-500':'text-gray-300' }}">
              {{ $row['tx']->type==='distribution' ? '$'.number_format($row['tx']->amount,2) : '—' }}
            </td>
            <td class="px-5 py-2.5 text-right text-xs font-bold text-gray-800">${{ number_format($row['balance'],2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
    <div class="px-5 py-4 text-xs text-gray-400">No transactions yet. Use "Inject Capital" or "Distribute" to record transactions.</div>
    @endif
  </div>
  @endforeach

  @if($investors->isEmpty())
  <div class="card p-12 text-center">
    <p class="text-gray-400 text-sm">No investors registered yet.</p>
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
<div id="invModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl" style="max-height:90vh;overflow-y:auto;">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 id="invModalTitle" class="font-semibold text-gray-800 text-sm">Add Investor / Director</h2>
      <button onclick="closeAll()" class="text-gray-400 hover:text-gray-600">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="invForm" method="POST" action="{{ route('crm.investors.store') }}">
      @csrf
      <span id="invMethodSpan"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="full_name" id="if_name" required maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Role <span class="text-red-500">*</span></label>
            <select name="role" id="if_role" required class="w-full">
              <option value="investor">Investor</option>
              <option value="director">Director</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Initial Capital ($)</label>
            <input type="number" name="initial_capital" id="if_capital" min="0" step="0.01" placeholder="0.00" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank Name</label>
            <input type="text" name="bank_name" id="if_bank" maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Number</label>
            <input type="text" name="bank_account_number" id="if_accno" maxlength="50" class="w-full">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Name</label>
            <input type="text" name="bank_account_name" id="if_accname" maxlength="255" class="w-full">
          </div>
        </div>
      </div>
      <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
        <button type="button" onclick="closeAll()" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- Transaction modal (inject / distribute) --}}
<div id="txModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <div>
        <h2 id="txModalTitle" class="font-semibold text-gray-800 text-sm">Record Transaction</h2>
        <p id="txModalSub" class="text-xs text-gray-400 mt-0.5"></p>
      </div>
      <button onclick="closeAll()" class="text-gray-400 hover:text-gray-600">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="txForm" method="POST" action="">
      @csrf
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Date <span class="text-red-500">*</span></label>
          <input type="date" name="transaction_date" required class="w-full" value="{{ date('Y-m-d') }}">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount ($) <span class="text-red-500">*</span></label>
          <input type="number" name="amount" required min="0.01" step="0.01" placeholder="0.00" class="w-full" id="tx_amount">
          <p id="tx_balance_hint" class="text-xs text-gray-400 mt-1 hidden"></p>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
          <input type="text" name="description" maxlength="500" placeholder="e.g. Q3 profit distribution" class="w-full">
        </div>
      </div>
      <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
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
