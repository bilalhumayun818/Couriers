<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Expense;
use App\Models\Trip;
use App\Models\Van;
use App\Models\VanFixedCost;
use App\Models\VanDriverAssignment;
use App\Models\Investor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Called by the standard artisan seeder (dev convenience only).
     */
    public function run(): void
    {
        $token = 'dev_demo_' . substr(md5('local_dev'), 0, 48);
        static::seedForToken($token);
    }

    /**
     * Seed one of each entity for a brand-new demo token.
     * All records are tagged with the token so they are isolated per browser.
     *
     * @param  string  $token  The cx_demo cookie value
     */
    public static function seedForToken(string $token): void
    {
        // ── 1. Driver ────────────────────────────────────────────────
        $driver = Driver::firstOrCreate(
            ['licence_number' => "DEMO-LIC-{$token}"],
            [
                'demo_token'          => $token,
                'full_name'           => 'Demo Driver',
                'national_id'         => 'DEMO-ID-001',
                'licence_expiry_date' => Carbon::now()->addYears(2)->toDateString(),
                'contact_number'      => '+1 000 000 0001',
                'emergency_contact'   => 'Demo Emergency Contact',
            ]
        );

        // ── 2. Van ───────────────────────────────────────────────────
        $van = Van::firstOrCreate(
            ['plate_number' => "DEMO-{$token}"],
            [
                'demo_token' => $token,
                'make_model' => 'Toyota HiAce',
                'year'       => 2023,
                'status'     => 'active',
                'driver_id'  => $driver->id,
            ]
        );

        // Fixed cost for the van
        VanFixedCost::firstOrCreate(
            ['van_id' => $van->id],
            [
                'demo_token'        => $token,
                'monthly_lease'     => 1500,
                'road_tax_annual'   => 400,
                'insurance_monthly' => 120,
                'next_service_date' => Carbon::now()->addMonths(3)->toDateString(),
            ]
        );

        // Assignment
        VanDriverAssignment::firstOrCreate(
            ['van_id' => $van->id, 'end_date' => null],
            [
                'demo_token' => $token,
                'driver_id'  => $driver->id,
                'start_date' => Carbon::now()->subMonth()->toDateString(),
                'end_date'   => null,
            ]
        );

        // ── 3. Customer ──────────────────────────────────────────────
        $customer = Customer::firstOrCreate(
            ['demo_token' => $token, 'company_name' => 'Demo Client Ltd'],
            [
                'demo_token'      => $token,
                'company_name'    => 'Demo Client Ltd',
                'contact_name'    => 'John Demo',
                'email'           => 'demo@example.com',
                'phone'           => '+1 000 000 0002',
                'credit_limit'    => 5000,
                'billing_address' => '1 Demo Street, Demo City',
            ]
        );

        // ── 4. Trip ──────────────────────────────────────────────────
        $fare  = 250.00;
        $tax   = round($fare * 0.08, 2);
        $total = round($fare + $tax, 2);

        Trip::firstOrCreate(
            ['demo_token' => $token, 'origin' => 'Demo Origin', 'destination' => 'Demo Destination'],
            [
                'demo_token'   => $token,
                'van_id'       => $van->id,
                'customer_id'  => $customer->id,
                'trip_date'    => Carbon::now()->toDateString(),
                'origin'       => 'Demo Origin',
                'destination'  => 'Demo Destination',
                'fare_amount'  => $fare,
                'tax_amount'   => $tax,
                'total_amount' => $total,
                'status'       => 'active',
                'created_by'   => null,
            ]
        );

        // ── 5. Expense ───────────────────────────────────────────────
        Expense::firstOrCreate(
            ['demo_token' => $token, 'expense_date' => Carbon::now()->toDateString(), 'category' => 'Fuel'],
            [
                'demo_token'   => $token,
                'van_id'       => $van->id,
                'category'     => 'Fuel',
                'expense_date' => Carbon::now()->toDateString(),
                'amount'       => 50.00,
                'description'  => 'Demo fuel expense',
                'status'       => 'active',
            ]
        );

        // ── 6. Investor ──────────────────────────────────────────────
        Investor::firstOrCreate(
            ['demo_token' => $token, 'full_name' => 'Demo Investor'],
            [
                'demo_token'          => $token,
                'full_name'           => 'Demo Investor',
                'role'                => 'Director',
                'bank_account_name'   => 'Demo Investor',
                'bank_account_number' => 'DEMO-0001',
                'bank_name'           => 'Demo Bank',
                'initial_capital'     => 10000.00,
            ]
        );
    }
}
