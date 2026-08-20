<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Van;
use App\Models\DriverAdvance;
use App\Models\WagePayout;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WageController extends Controller
{
    public function index(Request $request)
    {
        $selectedPeriod = $request->get('pay_period', Carbon::now()->format('Y-m'));

        // Advances for selected period
        $advances = DriverAdvance::with(['driver', 'van'])
            ->where('pay_period', $selectedPeriod)
            ->orderByDesc('advance_date')
            ->get();

        // Payouts for selected period
        $payouts = WagePayout::with('driver')
            ->where('pay_period', $selectedPeriod)
            ->orderBy('created_at')
            ->get();

        // All drivers for selects
        $drivers = Driver::orderBy('full_name')->get();
        $vans    = Van::orderBy('plate_number')->get();

        // Period options (last 6 months)
        $periods = collect();
        for ($i = 0; $i < 6; $i++) {
            $p = Carbon::now()->subMonths($i)->format('Y-m');
            $periods->push([
                'value' => $p,
                'label' => Carbon::createFromFormat('Y-m', $p)->format('F Y'),
            ]);
        }

        // Summary totals for selected period
        $totalAdvances = $advances->sum('amount');
        $totalGross    = $payouts->sum('gross_wage');
        $totalNet      = $payouts->sum('net_wage');

        return view('demo.operations.wages', compact(
            'advances', 'payouts', 'drivers', 'vans',
            'periods', 'selectedPeriod', 'totalAdvances', 'totalGross', 'totalNet'
        ));
    }

    /**
     * Log a driver advance.
     */
    public function storeAdvance(Request $request)
    {
        $data = $request->validate([
            'driver_id'    => 'required|exists:drivers,id',
            'advance_date' => 'required|date',
            'amount'       => 'required|numeric|min:0.01|max:999999.99',
            'purpose'      => 'nullable|string|max:255',
            'pay_period'   => 'required|regex:/^\d{4}-\d{2}$/',
        ]);

        // Auto-assign van from current driver assignment
        $van = Van::where('driver_id', $data['driver_id'])->first();
        $data['van_id'] = $van?->id;

        DriverAdvance::create($data);

        return redirect()->route('operations.wages', ['pay_period' => $data['pay_period']])
            ->with('success', 'Advance logged successfully.');
    }

    /**
     * Delete an advance.
     */
    public function destroyAdvance(DriverAdvance $advance)
    {
        $period = $advance->pay_period;
        $advance->delete();

        return redirect()->route('operations.wages', ['pay_period' => $period])
            ->with('success', 'Advance removed.');
    }

    /**
     * Preview net wage calculation (no save).
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'driver_id'  => 'required|exists:drivers,id',
            'pay_period' => 'required|regex:/^\d{4}-\d{2}$/',
            'gross_wage' => 'required|numeric|min:0',
        ]);

        $totalAdvances = DriverAdvance::where('driver_id', $data['driver_id'])
            ->where('pay_period', $data['pay_period'])
            ->sum('amount');

        $net        = round((float)$data['gross_wage'] - $totalAdvances, 2);
        $isNegative = $net < 0;

        return response()->json([
            'gross_wage'     => (float) $data['gross_wage'],
            'total_advances' => (float) $totalAdvances,
            'net_wage'       => $net,
            'is_negative'    => $isNegative,
        ]);
    }

    /**
     * Save a wage payout.
     */
    public function storePayout(Request $request)
    {
        $data = $request->validate([
            'driver_id'  => 'required|exists:drivers,id',
            'pay_period' => 'required|regex:/^\d{4}-\d{2}$/',
            'gross_wage' => 'required|numeric|min:0',
            'confirmed'  => 'nullable|boolean',
        ]);

        $totalAdvances = DriverAdvance::where('driver_id', $data['driver_id'])
            ->where('pay_period', $data['pay_period'])
            ->sum('amount');

        $net        = round((float)$data['gross_wage'] - $totalAdvances, 2);
        $isNegative = $net < 0;

        // Require confirmation for negative payout
        if ($isNegative && !$request->boolean('confirmed')) {
            return back()
                ->withInput()
                ->with('negative_warning', [
                    'driver_id'  => $data['driver_id'],
                    'pay_period' => $data['pay_period'],
                    'gross_wage' => $data['gross_wage'],
                    'advances'   => $totalAdvances,
                    'net'        => $net,
                ]);
        }

        WagePayout::updateOrCreate(
            ['driver_id' => $data['driver_id'], 'pay_period' => $data['pay_period']],
            [
                'gross_wage'     => $data['gross_wage'],
                'total_advances' => $totalAdvances,
                'net_wage'       => $net,
                'is_negative'    => $isNegative,
                'confirmed'      => true,
            ]
        );

        return redirect()->route('operations.wages', ['pay_period' => $data['pay_period']])
            ->with('success', 'Wage payout saved for ' . Carbon::createFromFormat('Y-m', $data['pay_period'])->format('F Y') . '.');
    }
}
