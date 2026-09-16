<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Trip;
use App\Models\Van;
use Illuminate\Http\Request;

class TripController extends Controller
{

    /**
     * List trips with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Trip::with(['van', 'customer'])
            ->orderBy('trip_date', 'desc')
            ->orderBy('id', 'desc');

        // Filter: van
        if ($request->filled('van_id')) {
            $query->where('van_id', $request->van_id);
        }

        // Filter: customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter: status
        if ($request->filled('status') && in_array($request->status, ['active', 'voided'])) {
            $query->where('status', $request->status);
        }

        // Filter: date range
        if ($request->filled('from')) {
            $query->where('trip_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('trip_date', '<=', $request->to);
        }

        $trips     = $query->paginate(15)->withQueryString();
        $vans      = Van::active()->orderBy('plate_number')->get();
        $customers = Customer::orderBy('company_name')->get();

        return view('demo.operations.trips', compact('trips', 'vans', 'customers'));
    }

    /**
     * Store a new trip.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'van_id'       => ['required', 'exists:vans,id', function ($attr, $value, $fail) {
                $van = Van::find($value);
                if (!$van || $van->status !== 'active') {
                    $fail('The selected van must be active.');
                }
            }],
            'customer_id'  => 'required|exists:customers,id',
            'trip_date'    => 'required|date',
            'origin'       => 'required|string|max:255',
            'destination'  => 'required|string|max:255',
            'fare_amount'  => 'required|numeric|min:0.01|max:9999999',
        ]);

        $fare  = (float) $data['fare_amount'];
        $tax   = round($fare * (float) \App\Models\TenantSetting::current()->tax_rate / 100, 2);
        $total = round($fare + $tax, 2);

        Trip::create([
            'van_id'       => $data['van_id'],
            'customer_id'  => $data['customer_id'],
            'trip_date'    => $data['trip_date'],
            'origin'       => $data['origin'],
            'destination'  => $data['destination'],
            'fare_amount'  => $fare,
            'tax_amount'   => $tax,
            'total_amount' => $total,
            'status'       => 'active',
            'created_by'   => null,
        ]);

        return redirect()->route('operations.trips')->with('success', 'Trip logged successfully.');
    }

    /**
     * Void an active trip.
     */
    public function void(Trip $trip)
    {
        $trip->update(['status' => 'voided']);

        return redirect()->route('operations.trips')->with('success', 'Trip #' . str_pad($trip->id, 5, '0', STR_PAD_LEFT) . ' has been voided.');
    }
}
