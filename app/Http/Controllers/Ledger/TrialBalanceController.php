<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\Expense;
use App\Models\DriverAdvance;
use App\Models\WagePayout;
use App\Models\InvestorTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $accounts = $this->buildAccounts($from, $to);

        $totalDebit  = collect($accounts)->sum('debit');
        $totalCredit = collect($accounts)->sum('credit');
        $balanced    = abs($totalDebit - $totalCredit) < 0.01; // float tolerance

        return view('demo.ledger.trial-balance', compact(
            'accounts', 'totalDebit', 'totalCredit', 'balanced', 'from', 'to'
        ));
    }

    private function buildAccounts(Carbon $from, Carbon $to): array
    {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        // ── Revenue (Credit) ──────────────────────────────────────────────
        $tripRevenue = (float) Trip::where('status', 'active')
            ->whereBetween('trip_date', [$fromDate, $toDate])
            ->sum('fare_amount');

        $tripTax = (float) Trip::where('status', 'active')
            ->whereBetween('trip_date', [$fromDate, $toDate])
            ->sum('tax_amount');

        // ── Expenses (Debit) ──────────────────────────────────────────────
        $expByCategory = Expense::where('status', 'active')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $totalExpenses = (float) $expByCategory->sum();

        // ── Driver Advances & Wages (Debit) ──────────────────────────────
        $totalAdvances = (float) DriverAdvance::whereBetween('advance_date', [$fromDate, $toDate])
            ->sum('amount');

        $totalWages = (float) WagePayout::whereBetween('created_at', [$from, $to])
            ->sum('net_wage');

        // ── Investor Capital & Distributions ──────────────────────────────
        $capitalInjections = (float) InvestorTransaction::where('type', 'injection')
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');

        $distributions = (float) InvestorTransaction::where('type', 'distribution')
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->sum('amount');

        // ── Accounts Receivable (Debit = total invoiced incl. tax) ────────
        $totalInvoiced = $tripRevenue + $tripTax;

        // ── Cash / Bank = what came in minus what went out ────────────────
        $cashIn  = $totalInvoiced + $capitalInjections;
        $cashOut = $totalExpenses + $totalAdvances + abs($totalWages) + $distributions;
        $cashNet = $cashIn - $cashOut;

        // Build account rows
        $accounts = [];

        // Revenue accounts (Credit side)
        $accounts[] = [
            'name'    => 'Trip Revenue',
            'type'    => 'Revenue',
            'debit'   => 0,
            'credit'  => $tripRevenue,
            'net'     => $tripRevenue,
        ];
        if ($tripTax > 0) {
            $accounts[] = [
                'name'    => 'Tax Collected (8%)',
                'type'    => 'Revenue',
                'debit'   => 0,
                'credit'  => $tripTax,
                'net'     => $tripTax,
            ];
        }

        // Capital (Credit side)
        if ($capitalInjections > 0) {
            $accounts[] = [
                'name'   => 'Capital Injections',
                'type'   => 'Equity',
                'debit'  => 0,
                'credit' => $capitalInjections,
                'net'    => $capitalInjections,
            ];
        }

        // Accounts Receivable (Debit side)
        $accounts[] = [
            'name'   => 'Accounts Receivable',
            'type'   => 'Asset',
            'debit'  => $totalInvoiced,
            'credit' => 0,
            'net'    => $totalInvoiced,
        ];

        // Cash / Bank
        if ($cashNet != 0) {
            $accounts[] = [
                'name'   => 'Cash / Bank',
                'type'   => 'Asset',
                'debit'  => max(0, $cashNet),
                'credit' => max(0, -$cashNet),
                'net'    => abs($cashNet),
            ];
        }

        // Expense accounts (Debit side)
        $expenseCategories = \App\Models\Expense::$categories;
        foreach ($expenseCategories as $cat) {
            $amt = (float)($expByCategory[$cat] ?? 0);
            if ($amt > 0) {
                $accounts[] = [
                    'name'   => $cat,
                    'type'   => 'Expense',
                    'debit'  => $amt,
                    'credit' => 0,
                    'net'    => $amt,
                ];
            }
        }

        if ($totalAdvances > 0) {
            $accounts[] = [
                'name'   => 'Driver Advances',
                'type'   => 'Expense',
                'debit'  => $totalAdvances,
                'credit' => 0,
                'net'    => $totalAdvances,
            ];
        }

        if (abs($totalWages) > 0) {
            $accounts[] = [
                'name'   => 'Driver Wages (Net)',
                'type'   => 'Expense',
                'debit'  => abs($totalWages),
                'credit' => 0,
                'net'    => abs($totalWages),
            ];
        }

        if ($distributions > 0) {
            $accounts[] = [
                'name'   => 'Equity Distributions',
                'type'   => 'Equity',
                'debit'  => $distributions,
                'credit' => 0,
                'net'    => $distributions,
            ];
        }

        return $accounts;
    }
}
