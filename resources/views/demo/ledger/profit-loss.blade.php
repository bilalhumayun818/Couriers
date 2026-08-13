@extends('demo.layout')
@section('title','Profit & Loss')
@section('page-title','Financial Statements — Profit & Loss')
@section('page-subtitle','Revenue vs expenses for the selected period')

@section('content')
<div class="space-y-4">
  <div class="card px-5 py-4 flex flex-wrap gap-3 items-center">
    <input type="date" class="text-sm" value="2025-06-01">
    <span class="text-slate-400 text-sm">to</span>
    <input type="date" class="text-sm" value="2025-06-30">
    <button class="btn-primary text-sm px-3 py-1.5">Generate</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export CSV</button>
    <button class="btn-ghost text-sm px-3 py-1.5">Export PDF</button>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    {{-- P&L Statement --}}
    <div class="xl:col-span-2 card overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800 text-sm">Profit &amp; Loss Statement — June 2025</h3>
        <p class="text-xs text-slate-400 mt-0.5">Acme Logistics Ltd</p>
      </div>
      <div class="divide-y divide-slate-50">
        {{-- Revenue --}}
        <div class="px-5 py-3 bg-emerald-50/40">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Revenue</p>
          <div class="flex justify-between text-sm py-1"><span class="text-slate-700">Trip Revenue</span><span class="font-semibold text-emerald-700">$48,320.00</span></div>
          <div class="flex justify-between text-sm font-bold border-t border-emerald-200 mt-2 pt-2"><span class="text-slate-800">Gross Revenue</span><span class="text-emerald-700 text-base">$48,320.00</span></div>
        </div>
        {{-- Expenses --}}
        <div class="px-5 py-3">
          <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">Operating Expenses</p>
          @php
          $exps = [
            ['Fuel','$11,240.00'],['Tolls','$1,840.00'],['Spare Parts','$3,200.00'],
            ['Maintenance / Repairs','$4,960.00'],['Monthly Lease','$14,400.00'],
            ['Road Tax (Prorated)','$3,600.00'],['Insurance (Prorated)','$920.00'],
            ['Driver Wages','$5,625.00'],['Driver Advances','$305.00'],
          ];
          @endphp
          @foreach($exps as $ex)
          <div class="flex justify-between text-sm py-1">
            <span class="text-slate-600">{{ $ex[0] }}</span>
            <span class="text-red-600">{{ $ex[1] }}</span>
          </div>
          @endforeach
          <div class="flex justify-between text-sm font-bold border-t border-slate-200 mt-2 pt-2"><span class="text-slate-800">Total Operating Expenses</span><span class="text-red-600 text-base">$46,090.00</span></div>
        </div>
        {{-- Net --}}
        <div class="px-5 py-4 bg-indigo-50/40">
          <div class="flex justify-between font-bold text-base"><span class="text-slate-800">Net Profit</span><span class="text-indigo-700 text-xl">$2,230.00</span></div>
          <p class="text-xs text-slate-400 mt-1">Net Margin: 4.6%</p>
        </div>
      </div>
    </div>

    {{-- Summary + Chart --}}
    <div class="space-y-4">
      <div class="card p-5">
        <h3 class="font-semibold text-slate-800 text-sm mb-4">Expense Breakdown</h3>
        <canvas id="expPie" height="200"></canvas>
      </div>
      <div class="card p-5 space-y-3">
        <div class="flex justify-between text-sm"><span class="text-slate-500">Gross Revenue</span><span class="font-semibold text-emerald-700">$48,320</span></div>
        <div class="flex justify-between text-sm"><span class="text-slate-500">Total Expenses</span><span class="font-semibold text-red-600">$46,090</span></div>
        <div class="flex justify-between text-sm font-bold border-t border-slate-200 pt-3"><span class="text-slate-800">Net Profit</span><span class="text-indigo-700">$2,230</span></div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
Chart.defaults.font.family='Inter,sans-serif';
new Chart(document.getElementById('expPie'),{
  type:'doughnut',
  data:{
    labels:['Fuel','Lease','Maintenance','Wages','Road Tax','Insurance','Tolls','Parts','Advances'],
    datasets:[{data:[11240,14400,4960,5625,3600,920,1840,3200,305],
      backgroundColor:['#6366f1','#818cf8','#a5b4fc','#c7d2fe','#e0e7ff','#10b981','#34d399','#6ee7b7','#a7f3d0'],
      borderWidth:0}]
  },
  options:{responsive:true,cutout:'60%',plugins:{legend:{position:'bottom',labels:{boxWidth:10,font:{size:10}}}}}
});
</script>
@endsection
