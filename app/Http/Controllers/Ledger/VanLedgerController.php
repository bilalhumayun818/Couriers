<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Van;
use App\Models\Trip;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VanLedgerController extends Controller
{
    /**
     * Summary table — all vans grouped.
     */
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $vanId    = $request->filled('van_id')   ? (int) $request->van_id   : null;
        $category = $request->filled('category') ? $request->category       : null;

        $vans        = Van::with('fixedCost')->orderBy('plate_number')->get();
        $daysInRange = max(1, $from->diffInDays($to) + 1);
        $daysInMonth = Carbon::now()->daysInMonth;

        $rows = [];
        $grandRevenue = $grandVariable = $grandFixed = $grandNet = 0;

        foreach ($vans as $van) {
            if ($vanId && $van->id !== $vanId) continue;

            $revenueQuery = Trip::where('van_id', $van->id)
                ->where('status', 'active')
                ->whereBetween('trip_date', [$from->toDateString(), $to->toDateString()]);
            $revenue   = (float) $revenueQuery->sum('fare_amount');
            $tripCount = $revenueQuery->count();

            $expQuery = Expense::where('van_id', $van->id)
                ->where('status', 'active')
                ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
            if ($category) $expQuery->where('category', $category);
            $variableExpenses = (float) $expQuery->sum('amount');

            $byCategory = Expense::where('van_id', $van->id)
                ->where('status', 'active')
                ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
                ->selectRaw('category, SUM(amount) as total')
                ->groupBy('category')
                ->pluck('total', 'category');

            $fc = $van->fixedCost;
            $proratedLease     = $fc ? round(((float)$fc->monthly_lease     / $daysInMonth) * $daysInRange, 2) : 0;
            $proratedRoadTax   = $fc ? round(((float)$fc->road_tax_annual   / 365)           * $daysInRange, 2) : 0;
            $proratedInsurance = $fc ? round(((float)$fc->insurance_monthly / $daysInMonth) * $daysInRange, 2) : 0;
            $fixedTotal        = $proratedLease + $proratedRoadTax + $proratedInsurance;

            if (!$vanId && $revenue == 0 && $variableExpenses == 0 && $fixedTotal == 0) continue;

            $totalExpenses = $variableExpenses + ($category ? 0 : $fixedTotal);
            $netProfit     = $revenue - $totalExpenses;

            $rows[] = [
                'van'               => $van,
                'revenue'           => $revenue,
                'trip_count'        => $tripCount,
                'variable_expenses' => $variableExpenses,
                'fixed_lease'       => $proratedLease,
                'fixed_road_tax'    => $proratedRoadTax,
                'fixed_insurance'   => $proratedInsurance,
                'fixed_total'       => $fixedTotal,
                'total_expenses'    => $totalExpenses,
                'net_profit'        => $netProfit,
                'by_category'       => $byCategory,
                'is_profitable'     => $netProfit >= 0,
            ];

            $grandRevenue  += $revenue;
            $grandVariable += $variableExpenses;
            $grandFixed    += $category ? 0 : $fixedTotal;
            $grandNet      += $netProfit;
        }

        $categories = Expense::$categories;

        return view('demo.ledger.van-ledger', compact(
            'rows', 'vans', 'categories',
            'from', 'to', 'vanId', 'category',
            'grandRevenue', 'grandVariable', 'grandFixed', 'grandNet',
            'daysInRange'
        ));
    }

    /**
     * Detail view — single van.
     */
    public function show(Van $van, Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $daysInRange = max(1, $from->diffInDays($to) + 1);
        $daysInMonth = Carbon::now()->daysInMonth;

        $trips = Trip::where('van_id', $van->id)
            ->where('status', 'active')
            ->whereBetween('trip_date', [$from->toDateString(), $to->toDateString()])
            ->with('customer')
            ->orderByDesc('trip_date')
            ->get();
        $revenue = (float) $trips->sum('fare_amount');

        $expenses = Expense::where('van_id', $van->id)
            ->where('status', 'active')
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('expense_date')
            ->get();
        $expByCategory = $expenses->groupBy('category')->map(fn($g) => $g->sum('amount'));
        $variableTotal = (float) $expenses->sum('amount');

        $fc = $van->fixedCost;
        $proratedLease     = $fc ? round(((float)$fc->monthly_lease     / $daysInMonth) * $daysInRange, 2) : 0;
        $proratedRoadTax   = $fc ? round(((float)$fc->road_tax_annual   / 365)           * $daysInRange, 2) : 0;
        $proratedInsurance = $fc ? round(((float)$fc->insurance_monthly / $daysInMonth) * $daysInRange, 2) : 0;
        $fixedTotal        = $proratedLease + $proratedRoadTax + $proratedInsurance;

        $totalExpenses = $variableTotal + $fixedTotal;
        $netProfit     = $revenue - $totalExpenses;

        $van->load(['driver', 'fixedCost']);

        return view('demo.ledger.van-detail', compact(
            'van', 'trips', 'expenses', 'expByCategory',
            'revenue', 'variableTotal', 'fixedTotal', 'totalExpenses', 'netProfit',
            'proratedLease', 'proratedRoadTax', 'proratedInsurance',
            'from', 'to', 'daysInRange'
        ));
    }
}
