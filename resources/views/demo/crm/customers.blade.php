@extends('demo.layout')
@section('title','Customers')
@section('page-title','Stakeholders — Customers')
@section('page-subtitle','Customer directory, billing details and outstanding balances')

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

<div class="card overflow-hidden">

  {{-- Filter bar --}}
  <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-end gap-3 justify-between">
    <form method="GET" action="{{ route('crm.customers') }}" id="filterForm" class="flex gap-2 flex-wrap items-end">
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
        <input type="text" name="search" placeholder="Company name…" value="{{ request('search') }}"
               class="text-sm w-52" onchange="filterForm.submit()">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Balance</label>
        <select name="balance" class="text-sm" onchange="filterForm.submit()">
          <option value="">All</option>
          <option value="with"  {{ request('balance')==='with'?'selected':'' }}>Has Balance</option>
          <option value="none"  {{ request('balance')==='none'?'selected':'' }}>No Balance</option>
        </select>
      </div>
      @if(request()->hasAny(['search','balance']))
        <a href="{{ route('crm.customers') }}" class="btn-ghost text-xs self-end" style="padding:6px 12px;">✕ Clear</a>
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
      <tbody class="divide-y divide-gray-50">
        @forelse($customers as $c)
        <tr class="table-row">
          <td class="px-5 py-3.5">
            <a href="{{ route('crm.customers.show', $c) }}"
               class="font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
              {{ $c->company_name }}
            </a>
          </td>
          <td class="px-5 py-3.5 text-gray-600">{{ $c->contact_name ?? '—' }}</td>
          <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $c->email ?? '—' }}</td>
          <td class="px-5 py-3.5 text-gray-500 text-xs">{{ $c->phone ?? '—' }}</td>
          <td class="px-5 py-3.5 text-right text-gray-600">${{ number_format($c->credit_limit ?? 0, 2) }}</td>
          <td class="px-5 py-3.5 text-right font-semibold text-gray-800">
            ${{ number_format($c->total_invoiced ?? 0, 2) }}
          </td>
          <td class="px-5 py-3.5 text-right text-gray-600">{{ $c->trips_count }}</td>
          <td class="px-5 py-3.5 text-right space-x-2">
            <a href="{{ route('crm.customers.show', $c) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View</a>
            <button onclick="openModal({{ json_encode(['id'=>$c->id,'company_name'=>$c->company_name,'contact_name'=>$c->contact_name,'email'=>$c->email,'phone'=>$c->phone,'billing_address'=>$c->billing_address,'credit_limit'=>$c->credit_limit]) }})"
              class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Edit</button>
            <form method="POST" action="{{ route('crm.customers.destroy',$c) }}" class="inline"
                  onsubmit="return confirm('Delete {{ addslashes($c->company_name) }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">
            No customers found. Add your first customer.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
    <span>Showing <strong>{{ $customers->firstItem() ?? 0 }}</strong>–<strong>{{ $customers->lastItem() ?? 0 }}</strong> of <strong>{{ $customers->total() }}</strong> customers</span>
    @if($customers->hasPages())
    <div class="flex gap-1">
      @if($customers->onFirstPage())
        <span class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-300">‹</span>
      @else
        <a href="{{ $customers->previousPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">‹</a>
      @endif
      @foreach($customers->getUrlRange(max(1,$customers->currentPage()-2),min($customers->lastPage(),$customers->currentPage()+2)) as $page=>$url)
        @if($page==$customers->currentPage())
          <span class="px-3 py-1.5 border border-indigo-600 bg-indigo-600 text-white rounded-lg">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">{{ $page }}</a>
        @endif
      @endforeach
      @if($customers->hasMorePages())
        <a href="{{ $customers->nextPageUrl() }}" class="px-3 py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50">›</a>
      @else
        <span class="px-3 py-1.5 border border-gray-200 rounded-lg text-gray-300">›</span>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection

@section('modals')
<div id="custModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);">
  <div class="bg-white rounded-xl w-full max-w-lg shadow-xl" style="max-height:90vh;overflow-y:auto;">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
      <h2 id="modalTitle" class="font-semibold text-gray-800 text-sm">Add Customer</h2>
      <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form id="custForm" method="POST" action="{{ route('crm.customers.store') }}">
      @csrf
      <span id="methodSpan"></span>
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Company Name <span class="text-red-500">*</span></label>
            <input type="text" name="company_name" id="f_company" required maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Contact Name</label>
            <input type="text" name="contact_name" id="f_contact" maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
            <input type="email" name="email" id="f_email" maxlength="255" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Phone</label>
            <input type="text" name="phone" id="f_phone" maxlength="30" class="w-full">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Credit Limit ($)</label>
            <input type="number" name="credit_limit" id="f_limit" min="0" step="0.01" placeholder="0.00" class="w-full">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Billing Address</label>
            <textarea name="billing_address" id="f_address" rows="2" maxlength="1000" class="w-full resize-none"></textarea>
          </div>
        </div>
      </div>
      <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
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
