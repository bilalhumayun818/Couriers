<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Van;
use App\Models\DriverAdvance;
use App\Models\WagePayout;
use Carbon\Carbon;

class WageSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = Driver::all();
        if ($drivers->isEmpty()) {
            $this->command->warn('No drivers — run FleetSeeder first.');
            return;
        }

        $currentPeriod  = Carbon::now()->format('Y-m');
        $previousPeriod = Carbon::now()->subMonth()->format('Y-m');
        $vanMap = Van::all()->keyBy('id');

        // Advances for current period
        $advancesData = [
            [0, 80.00,  'Fuel advance',   3, $currentPeriod],
            [0, 40.00,  'Expense float',  7, $currentPeriod],
            [1, 60.00,  'Fuel advance',   5, $currentPeriod],
            [2, 50.00,  'Expense float',  9, $currentPeriod],
            [3, 75.00,  'Fuel advance',   2, $currentPeriod],
            [4, 30.00,  'Road toll float',6, $currentPeriod],
            // Previous period
            [0, 90.00,  'Fuel advance',   35, $previousPeriod],
            [1, 45.00,  'Expense float',  33, $previousPeriod],
            [2, 60.00,  'Fuel advance',   30, $previousPeriod],
            [3, 70.00,  'Expense float',  28, $previousPeriod],
        ];

        foreach ($advancesData as [$dIdx, $amount, $purpose, $daysAgo, $period]) {
            $driver = $drivers[$dIdx] ?? null;
            if (!$driver) continue;

            // Get the van assigned to this driver
            $assignment = $driver->vans()->first();

            DriverAdvance::firstOrCreate(
                [
                    'driver_id'    => $driver->id,
                    'advance_date' => Carbon::now()->subDays($daysAgo)->toDateString(),
                    'amount'       => $amount,
                ],
                [
                    'van_id'     => $assignment?->id,
                    'purpose'    => $purpose,
                    'pay_period' => $period,
                ]
            );
        }

        // Wage payouts for previous period only (current period pending)
        $grossWages = [1200.00, 1100.00, 1050.00, 900.00, 950.00, 850.00, 1000.00];

        foreach ($drivers->take(7) as $idx => $driver) {
            $gross    = $grossWages[$idx] ?? 900.00;
            $advances = DriverAdvance::where('driver_id', $driver->id)
                ->where('pay_period', $previousPeriod)
                ->sum('amount');
            $net      = round($gross - $advances, 2);

            WagePayout::firstOrCreate(
                ['driver_id' => $driver->id, 'pay_period' => $previousPeriod],
                [
                    'gross_wage'     => $gross,
                    'total_advances' => $advances,
                    'net_wage'       => $net,
                    'is_negative'    => $net < 0,
                    'confirmed'      => true,
                ]
            );
        }

        $this->command->info('✓ Wages seeded: advances + payouts for ' . $drivers->count() . ' drivers.');
    }
}
