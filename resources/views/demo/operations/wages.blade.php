@extends('demo.layout')
@section('title','Advances & Wages')
@section('page-title','Operations — Advances & Wage Payouts')
@section('page-subtitle','Record driver cash advances and calculate net wage payouts')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

  {{-- Driver Advances --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-slate-800 text-sm">Driver Advances</h3>
        <p class="text-xs text-slate-400 mt-0.5">Cash advances issued to drivers</p>
      </div>
      <button onclick="modal('advModal',true)" class="btn-primary text-xs px-3 py-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> New Advance
      </button>
    </div>
    <table class="w-full text-sm">
      <thead><tr class="table-header">
        <th class="px-4 py-3 text-left">Driver</th>
        <th class="px-4 py-3 text-left">Date</th>
        <th class="px-4 py-3 text-left">Purpose</th>
        <th class="px-4 py-3 text-right">Amount</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @php $advances = [
          ['James Mwangi','10 Jun 2025','Fuel advance','$80.00'],
          ['James Mwangi','14 Jun 2025','Expense float','$40.00'],
          ['Peter Otieno','12 Jun 2025','Fuel advance','$60.00'],
          ['Samuel Kamau','09 Jun 2025','Expense float','$50.00'],
          ['David Njoroge','16 Jun 2025','Fuel advance','$75.00'],
        ]; @endphp
        @foreach($advances as $a)
        <tr class="table-row">
          <td class="px-4 py-3 text-slate-700 font-medium">{{ $a[0] }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $a[1] }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $a[2] }}</td>
          <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ $a[3] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Wage Payouts --}}
  <div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-slate-800 text-sm">Wage Payouts — June 2025</h3>
        <p class="text-xs text-slate-400 mt-0.5">Net = Gross Wage − Total Advances</p>
      </div>
      <button onclick="modal('wageModal',true)" class="btn-primary text-xs px-3 py-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Calculate Payout
      </button>
    </div>
    <table class="w-full text-sm">
      <thead><tr class="table-header">
        <th class="px-4 py-3 text-left">Driver</th>
        <th class="px-4 py-3 text-right">Gross Wage</th>
        <th class="px-4 py-3 text-right">Advances</th>
        <th class="px-4 py-3 text-right">Net Payout</th>
        <th class="px-4 py-3 text-center">Status</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-50">
        @php $wages = [
          ['James Mwangi','$1,200.00','$120.00','$1,080.00',false],
          ['Peter Otieno','$1,100.00','$60.00','$1,040.00',false],
          ['Samuel Kamau','$1,050.00','$50.00','$1,000.00',false],
          ['David Njoroge','$900.00','$75.00','$825.00',false],
          ['John Waweru','$950.00','$0.00','$950.00',false],
        ]; @endphp
        @foreach($wages as $w)
        <tr class="table-row">
          <td class="px-4 py-3 text-slate-700 font-medium">{{ $w[0] }}</td>
          <td class="px-4 py-3 text-right text-slate-600">{{ $w[1] }}</td>
          <td class="px-4 py-3 text-right text-red-500">−{{ $w[2] }}</td>
          <td class="px-4 py-3 text-right font-bold text-emerald-700">{{ $w[3] }}</td>
          <td class="px-4 py-3 text-center">
            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('modals')
<div id="advModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">New Driver Advance</h2>
      <button onclick="modal('advModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Driver</label>
        <select class="w-full"><option>James Mwangi</option><option>Peter Otieno</option><option>Samuel Kamau</option></select></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Date</label><input type="date" class="w-full"></div>
        <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Amount ($)</label><input type="number" placeholder="0.00" class="w-full"></div>
      </div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Purpose</label>
        <input type="text" placeholder="e.g. Fuel advance" class="w-full"></div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('advModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Advance</button>
    </div>
  </div>
</div>

<div id="wageModal" class="hidden fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Calculate Wage Payout</h2>
      <button onclick="modal('wageModal',false)" class="text-slate-400 hover:text-slate-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="p-5 space-y-4">
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Driver</label>
        <select class="w-full" onchange="previewWage()"><option value="1200">James Mwangi (Advances: $120)</option><option value="1100">Peter Otieno (Advances: $60)</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Pay Period</label>
        <select class="w-full"><option>June 2025</option><option>May 2025</option></select></div>
      <div><label class="block text-xs font-semibold text-slate-600 mb-1.5">Gross Wage ($)</label>
        <input type="number" id="grossWage" placeholder="0.00" class="w-full" oninput="previewWage()"></div>
      <div class="bg-slate-50 rounded-lg px-4 py-3 border border-slate-200">
        <div class="flex justify-between text-xs text-slate-500 mb-1"><span>Gross Wage</span><span id="pw-gross">$0.00</span></div>
        <div class="flex justify-between text-xs text-red-500 mb-1"><span>Total Advances</span><span id="pw-adv">−$120.00</span></div>
        <div class="flex justify-between text-sm font-bold text-emerald-700 border-t border-slate-200 pt-2 mt-2"><span>Net Payout</span><span id="pw-net">$0.00</span></div>
      </div>
    </div>
    <div class="px-5 py-4 border-t border-slate-100 flex justify-end gap-2">
      <button onclick="modal('wageModal',false)" class="btn-ghost">Cancel</button>
      <button class="btn-primary">Save Payout</button>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
function modal(id,s){document.getElementById(id).classList.toggle('hidden',!s);}
function previewWage(){
  const gross=parseFloat(document.getElementById('grossWage').value)||0;
  const adv=120;
  document.getElementById('pw-gross').textContent='$'+gross.toFixed(2);
  document.getElementById('pw-adv').textContent='−$'+adv.toFixed(2);
  const net=gross-adv;
  const netEl=document.getElementById('pw-net');
  netEl.textContent=(net<0?'−$'+Math.abs(net).toFixed(2):'$'+net.toFixed(2));
  netEl.className=net<0?'text-red-600 font-bold text-sm':'text-emerald-700 font-bold text-sm';
}
</script>
@endsection
