<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add demo_token to all entity tables.
     * Records without a token = real production data.
     * Records with a token   = demo session data (scoped per browser cookie).
     */
    public function up(): void
    {
        $tables = [
            'vans',
            'drivers',
            'customers',
            'trips',
            'expenses',
            'driver_advances',
            'wage_payouts',
            'investors',
            'investor_transactions',
            'van_fixed_costs',
            'van_driver_assignments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'demo_token')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('demo_token', 64)->nullable()->index();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'vans', 'drivers', 'customers', 'trips',
            'expenses', 'driver_advances', 'wage_payouts',
            'investors', 'investor_transactions',
            'van_fixed_costs', 'van_driver_assignments',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'demo_token')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('demo_token');
                });
            }
        }
    }
};
