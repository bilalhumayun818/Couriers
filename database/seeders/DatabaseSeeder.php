<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(fn () => $this->call([
            AdminUserSeeder::class,
            FleetSeeder::class,
            TripSeeder::class,
            ExpenseSeeder::class,
            WageSeeder::class,
            InvestorSeeder::class,
        ]));
    }
}
