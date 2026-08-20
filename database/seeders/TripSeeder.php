<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Trip;
use App\Models\Van;
use Carbon\Carbon;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        // ── Customers ────────────────────────────────────────────────────────────
        $customersData = [
            [
                'company_name' => 'Swift Retail Co',
                'contact_name' => 'Michael Johnson',
                'email'        => 'michael@swiftretail.com',
                'phone'        => '+254 700 210001',
                'credit_limit' => 50000.00,
            ],
            [
                'company_name' => 'Global Traders Ltd',
                'contact_name' => 'Sarah Kamau',
                'email'        => 'sarah@globaltraders.co.ke',
                'phone'        => '+254 722 210002',
                'credit_limit' => 75000.00,
            ],
            [
                'company_name' => 'Metro Supplies',
                'contact_name' => 'David Otieno',
                'email'        => 'david@metrosupplies.co.ke',
                'phone'        => '+254 733 210003',
                'credit_limit' => 30000.00,
            ],
            [
                'company_name' => 'Apex Importers',
                'contact_name' => 'Linda Wanjiku',
                'email'        => 'linda@apeximporters.com',
                'phone'        => '+254 711 210004',
                'credit_limit' => 100000.00,
            ],
            [
                'company_name' => 'Zenith Cargo',
                'contact_name' => 'Robert Mwangi',
                'email'        => 'robert@zenithcargo.co.ke',
                'phone'        => '+254 799 210005',
                'credit_limit' => 60000.00,
            ],
            [
                'company_name' => 'ProFreight Ltd',
                'contact_name' => 'Esther Njoroge',
                'email'        => 'esther@profreight.co.ke',
                'phone'        => '+254 755 210006',
                'credit_limit' => 80000.00,
            ],
        ];

        $customers = [];
        foreach ($customersData as $cd) {
            $customers[] = Customer::firstOrCreate(
                ['company_name' => $cd['company_name']],
                $cd
            );
        }

        // ── Vans (active only) ───────────────────────────────────────────────────
        $vans = Van::where('status', 'active')->pluck('id')->toArray();

        if (empty($vans)) {
            $this->command->warn('No active vans found — using all vans.');
            $vans = Van::pluck('id')->toArray();
        }

        if (empty($vans)) {
            $this->command->error('No vans in database. Run FleetSeeder first.');
            return;
        }

        // ── Trips ────────────────────────────────────────────────────────────────
        $taxRate = 0.08; // 8%

        $tripsData = [
            // [van_idx, customer_idx, days_ago, origin,                  destination,          fare,    status]
            [0, 0,  1, 'Nairobi CBD',         'Westlands',              1200.00, 'active'],
            [1, 1,  2, 'Industrial Area',     'Karen',                  1500.00, 'active'],
            [2, 2,  3, 'Embakasi',            'Gigiri',                 1800.00, 'active'],
            [3, 3,  4, 'Mombasa Road',        'Upperhill',              1350.00, 'active'],
            [4, 4,  5, 'Thika Road',          'South B',                2000.00, 'active'],
            [5, 5,  6, 'Ngong Road',          'Eastleigh',              1650.00, 'active'],
            [0, 1,  7, 'City Centre',         'Lavington',              2200.00, 'active'],
            [1, 2,  8, 'Ruiru',               'Kilimani',               1900.00, 'active'],
            [2, 3,  9, 'Athi River',          'Parklands',              2400.00, 'active'],
            [3, 4, 10, 'Jomo Kenyatta Airport','CBD',                    2800.00, 'active'],
            [4, 5, 11, 'Kasarani',            'Westlands',              1700.00, 'active'],
            [5, 0, 12, 'Kikuyu',              'Industrial Area',        2100.00, 'active'],
            [0, 2, 14, 'Nairobi CBD',         'Mombasa Road',           1450.00, 'active'],
            [1, 3, 16, 'Embakasi',            'Karen',                  1600.00, 'voided'],
            [2, 4, 18, 'Industrial Area',     'Westlands',              1300.00, 'voided'],
        ];

        foreach ($tripsData as $td) {
            $vanId      = $vans[$td[0] % count($vans)];
            $customerId = $customers[$td[1]]->id;
            $tripDate   = Carbon::today()->subDays($td[2])->toDateString();
            $fare       = $td[5];
            $tax        = round($fare * $taxRate, 2);
            $total      = $fare + $tax;

            Trip::firstOrCreate(
                [
                    'van_id'      => $vanId,
                    'customer_id' => $customerId,
                    'trip_date'   => $tripDate,
                    'origin'      => $td[3],
                    'destination' => $td[4],
                ],
                [
                    'fare_amount'  => $fare,
                    'tax_amount'   => $tax,
                    'total_amount' => $total,
                    'status'       => $td[6],
                ]
            );
        }

        $this->command->info('✓ Trips seeded: ' . count($tripsData) . ' trips, ' . count($customersData) . ' customers.');
    }
}
