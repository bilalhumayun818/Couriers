<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Van;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with([
            'vans:id,plate_number,make_model,status',
        ])->withCount('assignments');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', "%$term%")
                  ->orWhere('licence_number', 'like', "%$term%");
            });
        }

        $drivers = $query->orderBy('full_name')->paginate(15)->withQueryString();

        return view('demo.crm.drivers', compact('drivers'));
    }

    public function show(Driver $driver)
    {
        $driver->load([
            'assignments' => fn($q) => $q->with('van')->orderByDesc('start_date'),
            'vans:id,plate_number,make_model,status',
        ]);

        // Load advances and wage payouts
        $advances = \App\Models\DriverAdvance::where('driver_id', $driver->id)
            ->with('van')
            ->orderByDesc('advance_date')
            ->take(20)
            ->get();

        $payouts = \App\Models\WagePayout::where('driver_id', $driver->id)
            ->orderByDesc('pay_period')
            ->take(12)
            ->get();

        $currentAssignment = $driver->assignments->firstWhere('end_date', null);
        $daysToExpiry = Carbon::parse($driver->licence_expiry_date)->diffInDays(now(), false);
        $licenceExpiringSoon = $daysToExpiry >= -30 && $daysToExpiry <= 0;
        $licenceExpired      = $daysToExpiry > 0;

        return view('demo.crm.driver-detail', compact(
            'driver', 'currentAssignment', 'advances', 'payouts',
            'licenceExpiringSoon', 'licenceExpired', 'daysToExpiry'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'national_id'         => 'nullable|string|max:100',
            'licence_number'      => 'required|string|max:100|unique:drivers,licence_number',
            'licence_expiry_date' => 'required|date',
            'contact_number'      => 'nullable|string|max:30',
            'emergency_contact'   => 'nullable|string|max:255',
        ]);

        Driver::create($data);

        return redirect()->route('crm.drivers')
            ->with('success', 'Driver "' . $data['full_name'] . '" added.');
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'national_id'         => 'nullable|string|max:100',
            'licence_number'      => 'required|string|max:100|unique:drivers,licence_number,' . $driver->id,
            'licence_expiry_date' => 'required|date',
            'contact_number'      => 'nullable|string|max:30',
            'emergency_contact'   => 'nullable|string|max:255',
        ]);

        $driver->update($data);

        return redirect()->route('crm.drivers')
            ->with('success', 'Driver updated.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->route('crm.drivers')
            ->with('success', '"' . $driver->full_name . '" removed.');
    }
}
