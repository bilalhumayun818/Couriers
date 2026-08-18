<?php

namespace App\Http\Controllers\Fleet;

use App\Http\Controllers\Controller;
use App\Models\Van;
use App\Models\VanFixedCost;
use Illuminate\Http\Request;

class FixedCostController extends Controller
{
    /**
     * List all vans with their fixed costs.
     */
    public function index()
    {
        $vans = Van::with('fixedCost')
            ->orderBy('make_model')
            ->orderBy('plate_number')
            ->get();

        $fleetTotals = [
            'lease'     => $vans->sum(fn($v) => $v->fixedCost?->monthly_lease ?? 0),
            'road_tax'  => $vans->sum(fn($v) => ($v->fixedCost?->road_tax_annual ?? 0) / 12),
            'insurance' => $vans->sum(fn($v) => $v->fixedCost?->insurance_monthly ?? 0),
            'total'     => $vans->sum(fn($v) => $v->fixedCost?->total_monthly ?? 0),
        ];

        return view('demo.fleet.fixed-costs', compact('vans', 'fleetTotals'));
    }

    /**
     * Upsert fixed costs for a van.
     */
    public function upsert(Request $request, Van $van)
    {
        $data = $request->validate([
            'monthly_lease'     => 'required|numeric|min:0|max:999999.99',
            'road_tax_annual'   => 'required|numeric|min:0|max:999999.99',
            'insurance_monthly' => 'required|numeric|min:0|max:999999.99',
            'next_service_date' => 'nullable|date',
        ]);

        VanFixedCost::updateOrCreate(
            ['van_id' => $van->id],
            [
                'monthly_lease'     => $data['monthly_lease'],
                'road_tax_annual'   => $data['road_tax_annual'],
                'insurance_monthly' => $data['insurance_monthly'],
                'next_service_date' => $data['next_service_date'] ?? null,
            ]
        );

        return redirect()->route('fleet.fixed-costs')
            ->with('success', "Fixed costs updated for {$van->plate_number}.");
    }
}
