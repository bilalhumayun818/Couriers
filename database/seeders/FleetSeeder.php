<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Van;
use App\Models\VanFixedCost;
use App\Models\VanDriverAssignment;
use Carbon\Carbon;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // ── Drivers ──────────────────────────────
        $drivers = [
            ['James Mwangi',   'KE19230001', 'DL-KE-20190234', '2025-07-04', '+254 700 111001', 'Mary Mwangi'],
            ['Peter Otieno',   'KE18450002', 'DL-KE-20180892', '2025-11-15', '+254 722 111002', 'Jane Otieno'],
            ['Samuel Kamau',   'KE21780003', 'DL-KE-20211045', '2026-03-22', '+254 733 111003', 'Grace Kamau'],
            ['David Njoroge',  'KE17340004', 'DL-KE-20170563', '2025-08-08', '+254 711 111004', 'Agnes Njoroge'],
            ['John Waweru',    'KE20560005', 'DL-KE-20201287', '2025-09-30', '+254 799 111005', 'Esther Waweru'],
            ['Alice Wanjiku',  'KE22110006', 'DL-KE-20220341', '2025-12-12', '+254 755 111006', 'Paul Wanjiku'],
            ['Grace Achieng',  'KE19670007', 'DL-KE-20190789', '2025-06-20', '+254 722 111007', 'Joseph Achieng'],
            ['Brian Omondi',   'KE23040008', 'DL-KE-20230412', '2026-05-18', '+254 711 111008', 'Lilian Omondi'],
            ['Felix Mutua',    'KE21230009', 'DL-KE-20210567', '2026-01-25', '+254 733 111009', 'Mercy Mutua'],
            ['Anne Wanjiru',   'KE22670010', 'DL-KE-20220891', '2026-04-18', '+254 755 111010', 'Charles Wanjiru'],
        ];

        $driverModels = [];
        foreach ($drivers as $d) {
            $driver = Driver::withTrashed()->firstOrCreate(
                ['licence_number' => $d[2]],
                [
                    'full_name'            => $d[0],
                    'national_id'          => $d[1],
                    'licence_expiry_date'  => $d[3],
                    'contact_number'       => $d[4],
                    'emergency_contact'    => $d[5],
                ]
            );
            if ($driver->trashed()) {
                $driver->restore();
            }
            $driverModels[] = $driver;
        }

        // ── Vans ─────────────────────────────────
        $vansData = [
            // plate,        model,               year, status,        driver_idx, lease,  road_tax, insurance, next_service
            ['ABC-001', 'Toyota HiAce',      2022, 'active',      0, 1800, 450, 120, '2025-07-20'],
            ['GHJ-441', 'Toyota HiAce',      2020, 'maintenance', null, 1800, 450, 120, '2025-06-20'],
            ['UVW-909', 'Toyota HiAce',      2023, 'active',      7, 1800, 450, 120, '2025-08-12'],
            ['XYZ-202', 'Nissan NV350',      2021, 'active',      1, 1600, 400, 110, '2025-08-15'],
            ['YZA-111', 'Nissan NV350',      2022, 'maintenance', null, 1600, 400, 110, '2025-07-08'],
            ['DEF-303', 'Ford Transit',      2023, 'active',      2, 2000, 500, 140, '2025-09-10'],
            ['EFG-333', 'Ford Transit',      2022, 'leased',      9, 2000, 500, 140, '2025-10-18'],
            ['KLM-505', 'Isuzu NPR',         2022, 'active',      3, 2200, 550, 150, '2025-07-22'],
            ['BCD-222', 'Isuzu NPR',         2021, 'active',      8, 2200, 550, 150, '2025-09-25'],
            ['NOP-606', 'Mitsubishi Canter', 2021, 'active',      4, 1500, 380, 100, '2025-07-30'],
            ['QRS-707', 'Hino 300',          2023, 'leased',      5, 2400, 600, 160, '2025-10-05'],
            ['STU-808', 'Toyota Dyna',       2022, 'active',      6, 1700, 420, 115, '2025-08-18'],
        ];

        foreach ($vansData as $vd) {
            $driverId = $vd[4] !== null ? $driverModels[$vd[4]]->id : null;

            $van = Van::withTrashed()->firstOrCreate(
                ['plate_number' => $vd[0]],
                [
                    'make_model' => $vd[1],
                    'year'       => $vd[2],
                    'status'     => $vd[3],
                    'driver_id'  => $driverId,
                ]
            );
            if ($van->trashed()) {
                $van->restore();
            }

            // Fixed costs
            VanFixedCost::firstOrCreate(
                ['van_id' => $van->id],
                [
                    'monthly_lease'     => $vd[5],
                    'road_tax_annual'   => $vd[6],
                    'insurance_monthly' => $vd[7],
                    'next_service_date' => $vd[8],
                ]
            );

            // Assignment history
            if ($driverId !== null) {
                VanDriverAssignment::firstOrCreate(
                    ['van_id' => $van->id, 'end_date' => null],
                    [
                        'driver_id'  => $driverId,
                        'start_date' => Carbon::now()->subMonths(rand(2, 10))->toDateString(),
                        'end_date'   => null,
                    ]
                );
            }
        }

        $this->command->info('✓ Fleet seeded: ' . count($vansData) . ' vans, ' . count($drivers) . ' drivers.');
    }
}
