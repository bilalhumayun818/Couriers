<?php

namespace App\Http\Controllers\Fleet;

use App\Http\Controllers\Controller;
use App\Models\Van;
use App\Models\Driver;
use App\Models\VanDriverAssignment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AssignmentController extends Controller
{
    /**
     * Show all current assignments + history.
     */
    public function index()
    {
        // All vans with their current active assignment and driver
        $vans = Van::with([
            'driver',
            'assignments' => fn($q) => $q->with('driver')->orderByDesc('start_date'),
        ])
        ->orderBy('plate_number')
        ->get();

        // All unassigned drivers (no active van assignment)
        $assignedDriverIds = VanDriverAssignment::whereNull('end_date')
            ->pluck('driver_id');

        $availableDrivers = Driver::whereNotIn('id', $assignedDriverIds)
            ->orderBy('full_name')
            ->get();

        // All drivers for the reassign dropdown
        $allDrivers = Driver::orderBy('full_name')->get();

        return view('demo.fleet.assignments', compact('vans', 'availableDrivers', 'allDrivers'));
    }

    /**
     * Assign a driver to a van.
     * If the driver is already assigned elsewhere, end that assignment first.
     */
    public function assign(Request $request)
    {
        $data = $request->validate([
            'van_id'     => 'required|exists:vans,id',
            'driver_id'  => 'required|exists:drivers,id',
            'start_date' => 'required|date',
            'force'      => 'nullable|boolean',
        ]);

        $van    = Van::findOrFail($data['van_id']);
        $driver = Driver::findOrFail($data['driver_id']);

        // Check if driver is currently assigned to another van
        $existingAssignment = VanDriverAssignment::where('driver_id', $driver->id)
            ->whereNull('end_date')
            ->with('van')
            ->first();

        if ($existingAssignment && !$request->boolean('force')) {
            return back()
                ->withInput()
                ->with('confirm_reassign', [
                    'driver_name'    => $driver->full_name,
                    'current_van'    => $existingAssignment->van->plate_number,
                    'target_van'     => $van->plate_number,
                    'van_id'         => $data['van_id'],
                    'driver_id'      => $data['driver_id'],
                    'start_date'     => $data['start_date'],
                ]);
        }

        // End any existing active assignment for this van
        VanDriverAssignment::where('van_id', $van->id)
            ->whereNull('end_date')
            ->update(['end_date' => Carbon::parse($data['start_date'])->subDay()->toDateString()]);

        // End any existing active assignment for this driver (reassign case)
        if ($existingAssignment) {
            $existingAssignment->update([
                'end_date' => Carbon::parse($data['start_date'])->subDay()->toDateString(),
            ]);
        }

        // Create new assignment
        VanDriverAssignment::create([
            'van_id'     => $van->id,
            'driver_id'  => $driver->id,
            'start_date' => $data['start_date'],
            'end_date'   => null,
        ]);

        // Update van's driver_id
        $van->update(['driver_id' => $driver->id]);

        return redirect()->route('fleet.assignments')
            ->with('success', "{$driver->full_name} assigned to {$van->plate_number} successfully.");
    }

    /**
     * End an active assignment (unassign driver from van).
     */
    public function end(VanDriverAssignment $assignment)
    {
        $assignment->update(['end_date' => today()->toDateString()]);

        // Clear the driver_id on the van
        $assignment->van->update(['driver_id' => null]);

        return redirect()->route('fleet.assignments')
            ->with('success', "Assignment ended for {$assignment->driver->full_name}.");
    }
}
