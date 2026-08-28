<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Expense;
use App\Models\DriverAdvance;
use App\Models\WagePayout;
use App\Models\Van;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();
        $daysInRange = max(1, $from->diffInDays($to) + 1);
        $daysInMonth = Carbon::now()->daysInMonth;

        // ── REVENUE ───────────────────────────────────────────────────────
        $tripRevenue = (float) Trip::where('status','active')
            ->whereBetween('trip_date', [$fromDate, $toDate])
            ->sum('fare_amount');

        $tripCount = Trip::where('status','active')
            ->whereBetween('trip_date', [$fromDate, $toDate])
            ->count();

        $voidedCount = Trip::where('status','voided')
            ->whereBetween('trip_date', [$fromDate, $toDate])
            ->count();

        // ── VARIABLE EXPENSES ──────────────────────────────────────────────
        $expByCategory = Expense::where('status','active')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('category')
            ->get()
            ->keyBy('category');

        $fuelAmt        = (float)($expByCategory['Fuel']?->total ?? 0);
        $tollsAmt       = (float)($expByCategory['Tolls']?->total ?? 0);
        $sparePartsAmt  = (float)($expByCategory['Spare Parts']?->total ?? 0);
        $maintenanceAmt = (float)($expByCategory['Maintenance/Repairs']?->total ?? 0);
        $totalVariable  = $fuelAmt + $tollsAmt + $sparePartsAmt + $maintenanceAmt;

        // ── FIXED COSTS (prorated) ──────────────────────────────────────────
        $vans = Van::with('fixedCost')->get();
        $totalLease     = 0;
        $totalRoadTax   = 0;
        $totalInsurance = 0;

        foreach ($vans as $van) {
            if (!$van->fixedCost) continue;
            $totalLease     += ((float)$van->fixedCost->monthly_lease     / $daysInMonth) * $daysInRange;
            $totalRoadTax   += ((float)$van->fixedCost->road_tax_annual   / 365)           * $daysInRange;
            $totalInsurance += ((float)$van->fixedCost->insurance_monthly / $daysInMonth) * $daysInRange;
        }
        $totalLease     = round($totalLease, 2);
        $totalRoadTax   = round($totalRoadTax, 2);
        $totalInsurance = round($totalInsurance, 2);
        $totalFixed     = $totalLease + $totalRoadTax + $totalInsurance;

        // ── WAGES & ADVANCES ───────────────────────────────────────────────
        $totalAdvances = (float) DriverAdvance::whereBetween('advance_date', [$fromDate, $toDate])->sum('amount');
        $totalWages    = (float) WagePayout::whereBetween('created_at', [$from, $to])->sum('gross_wage');

        // ── TOTALS ──────────────────────────────────────────────────────────
        $totalExpenses = $totalVariable + $totalFixed + $totalAdvances + $totalWages;
        $netProfit     = $tripRevenue - $totalExpenses;
        $margin        = $tripRevenue > 0 ? round(($netProfit / $tripRevenue) * 100, 1) : 0;

        // ── MONTHLY TREND (last 6 months) ──────────────────────────────────
        $trend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month     = Carbon::now()->subMonths($i);
            $mFrom     = $month->copy()->startOfMonth()->toDateString();
            $mTo       = $month->copy()->endOfMonth()->toDateString();
            $mRevenue  = (float) Trip::where('status','active')->whereBetween('trip_date',[$mFrom,$mTo])->sum('fare_amount');
            $mExpenses = (float) Expense::where('status','active')->whereBetween('expense_date',[$mFrom,$mTo])->sum('amount');
            $trend[] = [
                'label'    => $month->format('M Y'),
                'revenue'  => $mRevenue,
                'expenses' => $mExpenses,
                'net'      => $mRevenue - $mExpenses,
            ];
        }

        return view('demo.ledger.profit-loss', compact(
            'from', 'to', 'daysInRange',
            'tripRevenue', 'tripCount', 'voidedCount',
            'fuelAmt', 'tollsAmt', 'sparePartsAmt', 'maintenanceAmt', 'totalVariable',
            'totalLease', 'totalRoadTax', 'totalInsurance', 'totalFixed',
            'totalAdvances', 'totalWages',
            'totalExpenses', 'netProfit', 'margin',
            'expByCategory', 'trend'
        ));
    }
}
