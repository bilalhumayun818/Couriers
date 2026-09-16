<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MainDatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_seeding_preserves_demo_data_and_can_be_repeated(): void
    {
        // Reproduce Artisan's unscoped model queries, even inside PHPUnit.
        $this->app->detectEnvironment(fn () => 'local');
        $driver = DB::table('drivers')->insertGetId([
            'full_name' => 'Demo driver', 'licence_number' => 'DEMO-TEST',
            'licence_expiry_date' => '2030-01-01', 'demo_token' => 'test-demo',
        ]);
        $van = DB::table('vans')->insertGetId([
            'plate_number' => 'DEMO-TEST', 'make_model' => 'Demo van',
            'year' => 2025, 'status' => 'active', 'driver_id' => $driver,
            'demo_token' => 'test-demo',
        ]);
        $customer = DB::table('customers')->insertGetId([
            'company_name' => 'Swift Retail Co', 'demo_token' => 'test-demo',
        ]);

        try {
            $this->seed(DatabaseSeeder::class);
            $counts = [];
            foreach (['vans', 'drivers', 'customers', 'trips', 'expenses',
                'driver_advances', 'wage_payouts', 'investors', 'investor_transactions',
                'van_fixed_costs', 'van_driver_assignments'] as $table) {
                $counts[$table] = DB::table($table)->whereNull('demo_token')->count();
                $this->assertGreaterThan(0, $counts[$table], $table);
            }

            $this->assertSame(0, DB::table('trips')->where('van_id', $van)->count());
            $this->assertSame(0, DB::table('trips')->where('customer_id', $customer)->count());
            $this->assertSame(0, DB::table('driver_advances')->where('driver_id', $driver)->count());
            $this->assertSame(0, DB::table('wage_payouts')->where('driver_id', $driver)->count());
            $this->assertDatabaseHas('drivers', ['id' => $driver, 'demo_token' => 'test-demo']);
            $this->assertDatabaseHas('customers', ['id' => $customer, 'demo_token' => 'test-demo']);

            $this->seed(DatabaseSeeder::class);
            foreach ($counts as $table => $count) {
                $this->assertSame($count, DB::table($table)->whereNull('demo_token')->count(), $table);
            }
        } finally {
            $this->app->detectEnvironment(fn () => 'testing');
        }
    }
}
