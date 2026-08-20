<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Van;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    public function run(): void
    {
        $vans = Van::all();
        if ($vans->isEmpty()) {
            $this->command->warn('No vans found — run FleetSeeder first.');
            return;
        }

        $today = Carbon::today();

        $records = [
            // [plate, category, days_ago, amount, description]
            ['ABC-001', 'Fuel',                0,  85.00, 'Petrol refill — full tank'],
            ['ABC-001', 'Tolls',               0,  12.00, 'Nairobi Expressway — round trip'],
            ['XYZ-202', 'Fuel',                1,  90.00, 'Petrol refill'],
            ['DEF-303', 'Maintenance/Repairs', 1, 140.00, 'Replace brake pads'],
            ['GHJ-441', 'Spare Parts',         1,  65.00, 'Oil filter + engine oil'],
            ['ABC-001', 'Fuel',                2,  88.00, 'Petrol refill — half tank'],
            ['KLM-505', 'Tolls',               2,   8.00, 'Thika Superhighway'],
            ['STU-808', 'Fuel',                3, 110.00, 'Diesel refill'],
            ['NOP-606', 'Maintenance/Repairs', 3, 220.00, 'Tyre replacement — rear pair'],
            ['XYZ-202', 'Spare Parts',         4,  45.00, 'Wiper blades replacement'],
            ['DEF-303', 'Fuel',                4,  92.00, 'Petrol refill'],
            ['KLM-505', 'Fuel',                5, 105.00, 'Petrol refill — full tank'],
            ['QRS-707', 'Maintenance/Repairs', 5, 180.00, 'Engine oil change + filter'],
            ['ABC-001', 'Tolls',               6,  15.00, 'Southern bypass'],
            ['STU-808', 'Spare Parts',         6,  55.00, 'Air filter replacement'],
            ['NOP-606', 'Fuel',                7,  88.00, 'Petrol refill'],
            ['BCD-222', 'Fuel',                7,  95.00, 'Diesel refill'],
            ['EFG-333', 'Maintenance/Repairs', 8, 350.00, 'Suspension repair'],
            ['UVW-909', 'Fuel',                8,  82.00, 'Petrol refill'],
            ['YZA-111', 'Spare Parts',         9,  70.00, 'Battery replacement'],
        ];

        $vanMap = $vans->keyBy('plate_number');

        foreach ($records as [$plate, $category, $daysAgo, $amount, $desc]) {
            $van = $vanMap->get($plate);
            if (!$van) continue;

            Expense::firstOrCreate(
                [
                    'van_id'       => $van->id,
                    'category'     => $category,
                    'expense_date' => $today->copy()->subDays($daysAgo)->toDateString(),
                    'amount'       => $amount,
                ],
                [
                    'description'  => $desc,
                    'status'       => 'active',
                ]
            );
        }

        $this->command->info('✓ Expenses seeded: ' . count($records) . ' records.');
    }
}
