@extends('demo.layout')
@section('title','Customers')
@section('page-title','Stakeholders — Customers')
@section('page-subtitle','Customer directory, billing details and outstanding balances')

@section('content')

@if(session('success'))
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;font-weight:500;">
  {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:rgba(56,189,248,0.12);border:1px solid rgba(56,189,248,0.35);color:#38bdf8;border-radius:8px;padding:10px 16px;margin-bottom:16px;font-size:13px;">
  @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
</div>
@endif

<div class="card overflow-hidden">

  {{-- Filter bar --}}
  <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-end gap-3 justify-between" style="border-bottom:1px solid rgba(255,255,255,0.07);">
    <form method="GET" action="{{ route('crm.customers') }}" id="filterForm" class="flex gap-2 flex-wrap items-end">
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Search</label>
        <input type="text" name="search" placeholder="Company name…" value="{{ request('search') }}"
               class="text-sm w-52" onchange="filterForm.submit()">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-500 mb-1">Balance</label>
        <select name="balance" class="text-sm" onchange="filterForm.submit()">
          <option value="">All</option>
          <option value="with"  {{ request('balance')==='with'?'selected':'' }}>Has Balance</option>
          <option value="none"  {{ request('balance')==='none'?'selected':'' }}>No Balance</option>
        </select>
      </div>
      @if(request()->hasAny(['search','balance']))
        <a href="{{ route('crm.customers') }}" class="btn-ghost text-xs self-end" style="padding:6px 12px;">Clear</a>
      @endif
    </form>
    <button onclick="openModal(null)" class="btn-primary self-end">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      Add Customer
    </button>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="table-header">
          <th class="px-5 py-3 text-left">Company</th>
          <th class="px-5 py-3 text-left">Contact</th>
          <th class="px-5 py-3 text-left">Email</th>
          <th class="px-5 py-3 text-left">Phone</th>
          <th class="px-5 py-3 text-right">Credit Limit</th>
          <th class="px-5 py-3 text-right">Total Invoiced</th>
          <th class="px-5 py-3 text-right">Trips</th>
          <th class="px-5 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $c)
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <a href="{{ route('crm.customers.show', $c) }}"
               class="font-semibold text-sky-400 hover:text-sky-300 hover:underline">
              {{ $c->company_name }}
            </a>
          </td>
          <td class="px-5 py-3.5 text-slate-300">{{ $c->contact_name ?? '—' }}</td>
          <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $c->email ?? '—' }}</td>
          <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $c->phone ?? '—' }}</td>
          <td class="px-5 py-3.5 text-right text-slate-400">{{ $currencySymbol }}{{ number_format($c->credit_limit ?? 0, 2) }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-slate-200">
            {{ $currencySymbol }}{{ number_format($c->total_invoiced ?? 0, 2) }}
          </td>
          <td class="px-5 py-3.5 text-right text-slate-400">{{ $c->trips_count }}</td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <a href="{{ route('crm.customers.show', $c) }}" class="text-xs text-sky-400 hover:text-sky-300 font-medium">View</a>
            <button onclick="openModal({{ json_encode(['id'=>$c->id,'company_name'=>$c->company_name,'contact_name'=>$c->contact_name,'email'=>$c->email,'phone'=>$c->phone,'billing_address'=>$c->billing_address,'credit_limit'=>$c->credit_limit]) }})"
              class="text-xs text-sky-400 hover:text-sky-300 font-medium">Edit</button>
            <form method="POST" action="{{ route('crm.customers.destroy',$c) }}" class="inline"
                  onsubmit="return confirm('Delete {{ addslashes($c->company_name) }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs text-slate-500 hover:text-sky-400 font-medium">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-5 py-12 text-center text-slate-500 text-sm">
            No customers found. Add your first customer.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="px-5 py-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500" style="border-top:1px solid rgba(255,255,255,0.07);">
    <span>Showing <strong class="text-slate-300">{{ $customers->firstItem() ?? 0 }}</strong>–<strong class="text-slate-300">{{ $customers->lastItem() ?? 0 }}</strong> of <strong class="text-slate-300">{{ $customers->total() }}</strong> customers</span>
    @if($customers->hasPages())
    <div class="flex gap-1">
      @if($customers->onFirstPage())
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">‹</span>
      @else
        <a href="{{ $customers->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">‹</a>
      @endif
      @foreach($customers->getUrlRange(max(1,$customers->currentPage()-2),min($customers->lastPage(),$customers->currentPage()+2)) as $page=>$url)
        @if($page==$customers->currentPage())
          <span class="px-3 py-1.5 rounded-lg text-white font-semibold" style="background:linear-gradient(135deg,#0284c7,#2563eb);border:none;">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">{{ $page }}</a>
        @endif
      @endforeach
      @if($customers->hasMorePages())
        <a href="{{ $customers->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 transition-colors" style="border:1px solid rgba(255,255,255,0.1);">›</a>
      @else
        <span class="px-3 py-1.5 rounded-lg text-slate-600" style="border:1px solid rgba(255,255,255,0.07);">›</span>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

@section('modals')
<div id="custModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay">
  <div class="modal-box w-full max-w-lg" style="max-height:90vh;overflow-y:auto;">
    <div class="flex items-center justify-between px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,0.08);">
      <h2 id="modalTitle" class="font-semibold text-slate-100 text-sm">Add Customer</h2>
      <button onclick="closeModal()" class="text-slate-500 hover:text-slate-300">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="custForm" method="POST" action="{{ route('crm.customers.store') }}">
      @csrf
      <span id="methodSpan"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Company Name <span class="text-sky-400">*</span></label>
            <input type="text" name="company_name" id="f_company" required maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Contact Name</label>
            <input type="text" name="contact_name" id="f_contact" maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Email</label>
            <input type="email" name="email" id="f_email" maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Phone</label>
            <input type="text" name="phone" id="f_phone" maxlength="30" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Credit Limit ({{ $currencySymbol }})</label>
            <input type="number" name="credit_limit" id="f_limit" min="0" step="0.01" placeholder="0.00" class="w-full">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-slate-400 mb-1.5">Billing Address</label>
            <textarea name="billing_address" id="f_address" rows="2" maxlength="1000" class="w-full resize-none"></textarea>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 flex justify-end gap-2" style="border-top:1px solid rgba(255,255,255,0.08);">
        <button type="button" onclick="closeModal()" class="btn-ghost">Cancel</button>
        <button type="submit" class="btn-primary">Save Customer</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
function openModal(data) {
  const form = document.getElementById('custForm');
  const ms   = document.getElementById('methodSpan');
  if (data) {
    document.getElementById('modalTitle').textContent = 'Edit Customer';
    form.action = '/crm/customers/' + data.id;
    ms.innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('f_company').value = data.company_name || '';
    document.getElementById('f_contact').value = data.contact_name || '';
    document.getElementById('f_email').value   = data.email        || '';
    document.getElementById('f_phone').value   = data.phone        || '';
    document.getElementById('f_limit').value   = data.credit_limit || '';
    document.getElementById('f_address').value = data.billing_address || '';
  } else {
    document.getElementById('modalTitle').textContent = 'Add Customer';
    form.action = '{{ route("crm.customers.store") }}';
    ms.innerHTML = '';
    form.reset();
  }
  document.getElementById('custModal').classList.remove('hidden');
}
function closeModal() { document.getElementById('custModal').classList.add('hidden'); }
@if($errors->any()) document.addEventListener('DOMContentLoaded', () => openModal(null)); @endif
</script>
@endsection

