<?php

namespace App\Http\Controllers\Ledger;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerLedgerController extends Controller
{
    /**
     * Summary listing of all customers with totals.
     */
    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $search = $request->filled('search') ? $request->search : null;

        $query = Customer::orderBy('company_name');
        if ($search) {
            $query->where('company_name', 'like', "%$search%");
        }

        $customers = $query->get()->map(function ($customer) use ($from, $to) {
            $trips = Trip::where('customer_id', $customer->id)
                ->where('status', 'active')
                ->whereBetween('trip_date', [$from->toDateString(), $to->toDateString()])
                ->orderBy('trip_date')
                ->with('van')
                ->get();

            $totalTrips    = $trips->count();
            $totalInvoiced = (float) $trips->sum('total_amount');
            $lastTrip      = $trips->last();

            return [
                'customer'      => $customer,
                'total_trips'   => $totalTrips,
                'total_invoiced'=> $totalInvoiced,
                'last_trip_date'=> $lastTrip?->trip_date,
            ];
        });

        $grandTotal = $customers->sum('total_invoiced');
        $grandTrips = $customers->sum('total_trips');

        return view('demo.ledger.customer-ledger', compact(
            'customers', 'from', 'to', 'search',
            'grandTotal', 'grandTrips'
        ));
    }

    /**
     * Customer statement — all trips for a specific customer.
     */
    public function show(Customer $customer, Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->subMonths(3)->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();

        $trips = Trip::where('customer_id', $customer->id)
            ->whereBetween('trip_date', [$from->toDateString(), $to->toDateString()])
            ->with('van')
            ->orderBy('trip_date')
            ->orderBy('id')
            ->get();

        // Running balance ledger
        $runningBalance = 0;
        $ledger = $trips->map(function ($trip) use (&$runningBalance) {
            if ($trip->status === 'active') {
                $runningBalance += (float) $trip->total_amount;
            }
            return [
                'trip'    => $trip,
                'balance' => $runningBalance,
            ];
        });

        $totalActive  = $trips->where('status', 'active')->count();
        $totalVoided  = $trips->where('status', 'voided')->count();
        $totalInvoiced = (float) $trips->where('status', 'active')->sum('total_amount');
        $totalFare     = (float) $trips->where('status', 'active')->sum('fare_amount');
        $totalTax      = (float) $trips->where('status', 'active')->sum('tax_amount');

        // Trip frequency by month
        $byMonth = $trips->where('status', 'active')
            ->groupBy(fn($t) => $t->trip_date->format('Y-m'))
            ->map(fn($group, $month) => [
                'month' => $month,
                'label' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
                'count' => $group->count(),
                'total' => $group->sum('total_amount'),
            ])->values();

        return view('demo.ledger.customer-statement', compact(
            'customer', 'ledger', 'trips',
            'totalActive', 'totalVoided', 'totalInvoiced', 'totalFare', 'totalTax',
            'from', 'to', 'byMonth'
        ));
    }
}
