<?php

namespace App\Http\Controllers\Fleet;

use App\Http\Controllers\Controller;
use App\Models\Van;
use App\Models\Driver;
use Illuminate\Http\Request;

class VanController extends Controller
{
    /**
     * Show all models grouped (category cards view).
     */
    public function index()
    {
        // Get all vans with driver and fixed cost, grouped by model
        $vans = Van::with(['driver', 'fixedCost'])
            ->orderBy('make_model')
            ->orderBy('plate_number')
            ->get();

        // Group by make_model
        $models = $vans->groupBy('make_model')->map(function ($group, $modelName) {
            return [
                'model'       => $modelName,
                'total'       => $group->count(),
                'active'      => $group->where('status', 'active')->count(),
                'maintenance' => $group->where('status', 'maintenance')->count(),
                'leased'      => $group->where('status', 'leased')->count(),
                'plates'      => $group->pluck('plate_number')->toArray(),
            ];
        })->values();

        $totalVans    = $vans->count();
        $totalModels  = $models->count();

        return view('demo.fleet.vans', compact('models', 'totalVans', 'totalModels'));
    }

    /**
     * Show all vans of a specific model.
     */
    public function byModel(string $model)
    {
        $model = urldecode($model);

        $vans = Van::with(['driver', 'fixedCost'])
            ->where('make_model', $model)
            ->orderBy('plate_number')
            ->get();

        $stats = [
            'total'       => $vans->count(),
            'active'      => $vans->where('status', 'active')->count(),
            'maintenance' => $vans->where('status', 'maintenance')->count(),
            'leased'      => $vans->where('status', 'leased')->count(),
        ];

        return view('demo.fleet.van-model', compact('model', 'vans', 'stats'));
    }

    /**
     * Store a new van.
     */
    public function store(Request $request)
    {
        $currentYear = (int) date('Y');

        $data = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vans,plate_number',
            'make_model'   => 'required|string|max:100',
            'year'         => 'required|integer|min:1990|max:' . $currentYear,
            'status'       => 'required|in:active,maintenance,leased',
        ]);

        Van::create($data);

        return redirect()->route('fleet.vans')->with('success', 'Vehicle added successfully.');
    }

    /**
     * Update van.
     */
    public function update(Request $request, Van $van)
    {
        $currentYear = (int) date('Y');

        $data = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vans,plate_number,' . $van->id,
            'make_model'   => 'required|string|max:100',
            'year'         => 'required|integer|min:1990|max:' . $currentYear,
            'status'       => 'required|in:active,maintenance,leased',
        ]);

        $van->update($data);

        return back()->with('success', 'Vehicle updated.');
    }

    /**
     * Soft delete van.
     */
    public function destroy(Van $van)
    {
        $van->delete();

        return redirect()->route('fleet.vans')->with('success', 'Vehicle removed.');
    }
}
