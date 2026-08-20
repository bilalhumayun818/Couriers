@extends('demo.layout')
@section('title','Daily Expenses')
@section('page-title','Operations — Daily Expenses')
@section('page-subtitle','Log and track operating expenses per van')

@section('content')

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  ✓ {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
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
  $colors = ['Fuel'=>'#f59e0b','Tolls'=>'#6b7280','Spare Parts'=>'#6366f1','Maintenance/Repairs'=>'#ef4444'];
  @endphp
  <div class="card p-4 col-span-2 sm:col-span-1">
    <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Total Expenses</p>
    <p class="text-2xl font-bold text-gray-900 mt-1">${{ number_format($totalAmount,2) }}</p>
    <p class="text-xs text-gray-400 mt-0.5">{{ $totalCount }} record{{ $totalCount!==1?'s':'' }}</p>
  </div>
  @foreach($categories as $cat)
  <div class="card p-4">
    <p class="text-xs font-semibold tracking-wide" style="color:{{ $colors[$cat] ?? '#6b7280' }};text-transform:uppercase;">{{ $cat }}</p>
    <p class="text-xl font-bold text-gray-900 mt-1">${{ number_format($catTotals[$cat] ?? 0,2) }}</p>
  </div>
  @endforeach
</div>

<div class="card overflow-hidden">
  {{-- Filter bar --}}
  <div class="px-5 py-4 border-b border-gray-100">
    <form method="GET" action="{{ route('operations.expenses') }}" id="filterForm"
          class="flex flex-wrap gap-3 items-end justify-between">
      <div class="flex flex-wrap gap-2 items-end">
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Van</label>
          <select name="van_id" class="text-sm" onchange="filterForm.submit()">
            <option value="">All Vans</option>
            @foreach($vans as $van)
              <option value="{{ $van->id }}" {{ request('van_id')==$van->id?'selected':'' }}>{{ $van->plate_number }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Category</label>
          <select name="category" class="text-sm" onchange="filterForm.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}" {{ request('category')===$cat?'selected':'' }}>{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
          <select name="status" class="text-sm" onchange="filterForm.submit()">
            <option value="">All</option>
            <option value="active"  {{ request('status')==='active'?'selected':'' }}>Active</option>
            <option value="deleted" {{ request('status')==='deleted'?'selected':'' }}>Deleted</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
          <input type="date" name="from" class="text-sm" value="{{ request('from') }}" onchange="filterForm.submit()">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
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
      <tbody class="divide-y divide-gray-50">
        @forelse($expenses as $exp)
        @php
          $catStyle = match($exp->category) {
            'Fuel'                => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a;',
            'Tolls'               => 'background:#f9fafb;color:#374151;border:1px solid #e5e7eb;',
            'Spare Parts'         => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;',
            'Maintenance/Repairs' => 'background:#fef2f2;color:#991b1b;border:1px solid #fecaca;',
            default               => 'background:#f3f4f6;color:#374151;',
          };
        @endphp
        <tr class="table-row {{ $exp->status==='deleted'?'opacity-60':'' }}">
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs text-indigo-600 font-semibold"
                  style="{{ $exp->status==='deleted'?'text-decoration:line-through;':'' }}">
              E-{{ str_pad($exp->id,5,'0',STR_PAD_LEFT) }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-xs text-gray-500">{{ $exp->expense_date->format('d M Y') }}</td>
          <td class="px-5 py-3.5">
            <span class="font-mono text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded font-semibold">
              {{ $exp->van->plate_number ?? '—' }}
            </span>
          </td>
          <td class="px-5 py-3.5">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="{{ $catStyle }}">
              {{ $exp->category }}
            </span>
          </td>
          <td class="px-5 py-3.5 text-xs text-gray-500">{{ $exp->description ?? '—' }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-gray-800">${{ number_format($exp->amount,2) }}</td>
          <td class="px-5 py-3.5 text-center">
            @if($exp->status==='active')
              <span style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
            @else
              <span style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;" class="text-xs font-semibold px-2 py-0.5 rounded-full">Deleted</span>
            @endif
          </td>
          <td class="px-5 py-3.5 text-right space-x-2">
            @if($exp->status==='active')
              <button onclick="openModal({{ json_encode(['id'=>$exp->id,'van_id'=>$exp->van_id,'category'=>$exp->category,'date'=>$exp->expense_date->format('Y-m-d'),'amount'=>$exp->amount,'description'=>$exp->description]) }})"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
              <form method="POST" action="{{ route('operations.expenses.destroy',$exp) }}" class="inline"
                    onsubmit="return confirm('Delete expense E-{{ str_pad($exp->id,5,'0',STR_PAD_LEFT) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium">Delete</button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">
            No expenses found. Adjust filters or log a new expense.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
    <span>Showing <strong>{{ $expenses->firstItem() ?? 0 }}</strong>–<strong>{{ $expenses->lastItem() ?? 0 }}</strong> of <strong>{{ $expenses->total() }}</strong> records</span>
    @if($expenses->hasPages())
    <div class="flex gap-1">
      @if($expenses->onFirstPage())
        <span class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-300">‹</span>
      @else
        <a href="{{ $expenses->previousPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">‹</a>
      @endif
      @foreach($expenses->getUrlRange(max(1,$expenses->currentPage()-2),min($expenses->lastPage(),$expenses->currentPage()+2)) as $page=>$url)
        @if($page==$expenses->currentPage())
          <span class="px-3 py-1.5 border border-indigo-600 bg-indigo-600 text-white rounded-lg">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">{{ $page }}</a>
        @endif
      @endforeach
      @if($expenses->hasMorePages())
        <a href="{{ $expenses->nextPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">›</a>
      @else
        <span class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-300">›</span>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

@section('modals')
<div id="expenseModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <div>
        <h2 id="modalTitle" class="font-semibold text-gray-800 text-sm">Log Expense</h2>
        <p id="modalSubtitle" class="text-xs text-gray-400 mt-0.5"></p>
      </div>
      <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="expenseForm" method="POST" action="{{ route('operations.expenses.store') }}">
      @csrf
      <span id="methodField"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Van <span class="text-red-500">*</span></label>
            <select name="van_id" id="f_van" required class="w-full">
              <option value="">— Select —</option>
              @foreach($vans as $van)
                <option value="{{ $van->id }}">{{ $van->plate_number }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Expense Date <span class="text-red-500">*</span></label>
            <input type="date" name="expense_date" id="f_date" required class="w-full" value="{{ date('Y-m-d') }}">
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Category <span class="text-red-500">*</span></label>
          <select name="category" id="f_category" required class="w-full">
            <option value="">— Select Category —</option>
            @foreach($categories as $cat)
              <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Amount ($) <span class="text-red-500">*</span></label>
          <input type="number" name="amount" id="f_amount" required min="0.01" max="999999.99" step="0.01" placeholder="0.00" class="w-full">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description <span class="text-gray-400 font-normal">(optional)</span></label>
          <textarea name="description" id="f_description" rows="2" maxlength="500" placeholder="Brief note…" class="w-full resize-none"></textarea>
        </div>
      </div>
      <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
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
