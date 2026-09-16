@extends('demo.layout')
@section('title','Daily Expenses')
@section('page-title','Operations — Daily Expenses')
@section('page-subtitle','Log and track operating expenses per van')

@section('content')

@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  <strong>Please fix:</strong>
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

{{-- Summary cards --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
  @php
  $catTotals = \App\Models\Expense::where('status','active')
    ->selectRaw('category, SUM(amount) as total')
    ->groupBy('category')->pluck('total','category');
  $colors = ['Fuel'=>'#38bdf8','Tolls'=>'#cbd5e1','Spare Parts'=>'#60a5fa','Maintenance/Repairs'=>'#38bdf8'];
  @endphp
  <div class="card p-4 col-span-2 sm:col-span-1">
    <p class="text-xs text-slate-400 uppercase font-semibold tracking-wide">Total Expenses</p>
    <p class="text-2xl font-bold text-slate-100 mt-1">{{ $currencySymbol }}{{ number_format($totalAmount,2) }}</p>
    <p class="text-xs text-slate-500 mt-0.5">{{ $totalCount }} record{{ $totalCount!==1?'s':'' }}</p>
  </div>
  @foreach($categories as $cat)
  <div class="card p-4">
    <p class="text-xs font-semibold tracking-wide" style="color:{{ $colors[$cat] ?? '#cbd5e1' }};text-transform:uppercase;">{{ $cat }}</p>
    <p class="text-xl font-bold text-slate-100 mt-1">{{ $currencySymbol }}{{ number_format($catTotals[$cat] ?? 0,2) }}</p>
  </div>
  @endforeach
</div>

<div class="card overflow-hidden">
  {{-- Filter bar --}}
  <div class="px-5 py-4 flex flex-wrap gap-3 items-end justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <form method="GET" action="{{ route('operations.expenses') }}" id="filterForm"
          class="flex flex-wrap gap-3 items-end justify-between w-full">
      <div class="flex flex-wrap gap-2 items-end">
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Van</label>
          <select name="van_id" class="text-sm" onchange="filterForm.submit()">
            <option value="">All Vans</option>
            @foreach($vans as $van)
              <option value="{{ $van->id }}" {{ request('van_id')==$van->id?'selected':'' }}>{{ $van->plate_number }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Category</label>
          <select name="category" class="text-sm" onchange="filterForm.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">Status</label>
          <select name="status" class="text-sm" onchange="filterForm.submit()">
            <option value="">All</option>
            <option value="active"  {{ request('status')==='active'?'selected':'' }}>Active</option>
            <option value="deleted" {{ request('status')==='deleted'?'selected':'' }}>Deleted</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">From</label>
          <input type="date" name="from" class="text-sm" value="{{ request('from') }}" onchange="filterForm.submit()">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1">To</label>
          <input type="date" name="to" class="text-sm" value="{{ request('to') }}" onchange="filterForm.submit()">
        </div>
        @if(request()->hasAny(['van_id','category','status','from','to']))
          <a href="{{ route('operations.expenses') }}" class="btn-ghost text-xs self-end" style="padding:6px 12px;">✕ Clear</a>
        @endif
      </div>
      <button type="button" onclick="openModal(null)" class="btn-primary self-end">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Log Expense
      </button>
    </form>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">ID</th>
          <th class="px-5 py-3 text-left">Date</th>
          <th class="px-5 py-3 text-left">Van</th>
          <th class="px-5 py-3 text-left">Category</th>
          <th class="px-5 py-3 text-left">Description</th>
          <th class="px-5 py-3 text-right">Amount</th>
          <th class="px-5 py-3 text-center">Status</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($expenses as $exp)
        <tr class="table-row {{ $exp->status==='deleted'?'opacity-60':'' }}">
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs text-sky-400 font-semibold"
                  style="{{ $exp->status==='deleted'?'text-decoration:line-through;':'' }}">
              E-{{ str_pad($exp->id,5,'0',STR_PAD_LEFT) }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-xs text-slate-400">{{ $exp->expense_date->format('d M Y') }}</td>
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs px-2 py-0.5 rounded font-semibold text-slate-300" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);">
              {{ $exp->van->plate_number ?? '—' }}
            </span>
          </td>
          <td class="px-5 py-3.5">
            @if($exp->category === 'Fuel')
              <span class="badge-blue">{{ $exp->category }}</span>
            @elseif($exp->category === 'Spare Parts')
              <span class="badge-blue">{{ $exp->category }}</span>
            @elseif($exp->category === 'Maintenance/Repairs')
              <span class="badge-blue">{{ $exp->category }}</span>
            @else
              <span class="badge-slate">{{ $exp->category }}</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-xs text-slate-400">{{ $exp->description ?? '—' }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-slate-200">{{ $currencySymbol }}{{ number_format($exp->amount,2) }}</td>
          <td class="px-5 py-3.5 text-center">
            @if($exp->status==='active')
              <span class="badge-blue">Active</span>
            @else
              <span class="badge-blue">Deleted</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            @if($exp->status==='active')
              <button onclick="openModal({{ json_encode(['id'=>$exp->id,'van_id'=>$exp->van_id,'category'=>$exp->category,'date'=>$exp->expense_date->format('Y-m-d'),'amount'=>$exp->amount,'description'=>$exp->description]) }})"
                class="text-xs text-sky-400 hover:text-sky-300 font-medium">Edit</button>
              <form method="POST" action="{{ route('operations.expenses.destroy',$exp) }}" class="inline"
                    onsubmit="return confirm('Delete expense E-{{ str_pad($exp->id,5,'0',STR_PAD_LEFT) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-slate-500 hover:text-sky-400 font-medium">Delete</button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-5 py-12 text-center text-slate-500 text-sm">
            No expenses found. Adjust filters or log a new expense.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500" style="border-top:1px solid rgba(255,255,255,0.07);">
    <span>Showing <strong class="text-slate-300">{{ $expenses->firstItem() ?? 0 }}</strong>–<strong class="text-slate-300">{{ $expenses->lastItem() ?? 0 }}</strong> of <strong class="text-slate-300">{{ $expenses->total() }}</strong> records</span>
    @if($expenses->hasPages())
    <div class="flex gap-1">
      @if($expenses->onFirstPage())
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">‹</span>
      @else
        <a href="{{ $expenses->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">‹</a>
      @endif
      @foreach($expenses->getUrlRange(max(1,$expenses->currentPage()-2),min($expenses->lastPage(),$expenses->currentPage()+2)) as $page=>$url)
        @if($page==$expenses->currentPage())
          <span class="px-3 py-1.5 rounded-lg text-white font-semibold" style="background:linear-gradient(135deg,#0284c7,#2563eb);border:none;">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">{{ $page }}</a>
        @endif
      @endforeach
      @if($expenses->hasMorePages())
        <a href="{{ $expenses->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">›</a>
      @else
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">›</span>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

@section('modals')
<div id="expenseModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-md">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <div>
        <h2 id="modalTitle" class="font-semibold text-slate-100 text-sm">Log Expense</h2>
        <p id="modalSubtitle" class="text-xs text-slate-400 mt-0.5"></p>
      </div>
      <button onclick="closeModal()" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="expenseForm" method="POST" action="{{ route('operations.expenses.store') }}">
      @csrf
      <span id="methodField"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Van <span class="text-sky-400">*</span></label>
            <select name="van_id" id="f_van" required class="w-full">
              <option value="">— Select —</option>
              @foreach($vans as $van)
                <option value="{{ $van->id }}">{{ $van->plate_number }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Expense Date <span class="text-sky-400">*</span></label>
            <input type="date" name="expense_date" id="f_date" required class="w-full" value="{{ date('Y-m-d') }}">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Category <span class="text-sky-400">*</span></label>
          <select name="category" id="f_category" required class="w-full">
            <option value="">— Select Category —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Amount ({{ $currencySymbol }}) <span class="text-sky-400">*</span></label>
          <input type="number" name="amount" id="f_amount" required min="0.01" max="999999.99" step="0.01" placeholder="0.00" class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-400 mb-1.5">Description <span class="text-slate-500 font-normal">(optional)</span></label>
          <textarea name="description" id="f_description" rows="2" maxlength="500" placeholder="Brief note…" class="w-full resize-none"></textarea>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="closeModal()" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Expense</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openModal(data) {
  const form = document.getElementById('expenseForm');
  const methodField = document.getElementById('methodField');

  if (data) {
    // Edit mode
    document.getElementById('modalTitle').textContent    = 'Edit Expense';
    document.getElementById('modalSubtitle').textContent = 'E-' + String(data.id).padStart(5,'0');
    form.action   = '/operations/expenses/' + data.id;
    methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_van').value         = data.van_id;
    document.getElementById('f_category').value   = data.category;
    document.getElementById('f_date').value        = data.date;
    document.getElementById('f_amount').value      = data.amount;
    document.getElementById('f_description').value = data.description || '';
  } else {
    // Create mode
    document.getElementById('modalTitle').textContent    = 'Log Expense';
    document.getElementById('modalSubtitle').textContent = '';
    form.action   = '{{ route("operations.expenses.store") }}';
    methodField.innerHTML = '';
    form.reset();
    document.getElementById('f_date').value = '{{ date("Y-m-d") }}';
  }
  document.getElementById('expenseModal').classList.remove('hidden');
}

function closeModal() {
  document.getElementById('expenseModal').classList.add('hidden');
}

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => openModal(null));
@endif
</script>
@endsection

