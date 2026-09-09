<?php

namespace App\Services;

use Illuminate\Http\Request;

class DemoSession
{
    public const COOKIE_NAME  = 'cx_demo';
    public const COOKIE_TTL   = 60 * 24 * 365; // 1 year in minutes
    public const RECORD_LIMIT = 10;

    /**
     * Map of model class → table name used for counting.
     */
    public const MODEL_TABLE_MAP = [
        \App\Models\Van::class            => 'vans',
        \App\Models\Driver::class         => 'drivers',
        \App\Models\Customer::class       => 'customers',
        \App\Models\Trip::class           => 'trips',
        \App\Models\Expense::class        => 'expenses',
        \App\Models\Investor::class       => 'investors',
        \App\Models\DriverAdvance::class  => 'driver_advances',
    ];

    /**
     * Human-readable labels for limit error messages.
     */
    public const MODEL_LABELS = [
        \App\Models\Van::class            => 'vehicles',
        \App\Models\Driver::class         => 'drivers',
        \App\Models\Customer::class       => 'customers',
        \App\Models\Trip::class           => 'trips',
        \App\Models\Expense::class        => 'expenses',
        \App\Models\Investor::class       => 'investors',
        \App\Models\DriverAdvance::class  => 'advances',
    ];

    /**
     * Route-name prefix → Model class for the DemoLimit middleware.
     */
    public const ROUTE_MODEL_MAP = [
        'demo.fleet.vans.store'              => \App\Models\Van::class,
        'demo.fleet.fixed-costs.upsert'     => null, // no limit — it's an upsert on existing van
        'demo.fleet.assignments.assign'     => null, // no limit — assignment is a pivot
        'demo.operations.trips.store'        => \App\Models\Trip::class,
        'demo.operations.expenses.store'     => \App\Models\Expense::class,
        'demo.operations.wages.advance'      => \App\Models\DriverAdvance::class,
        'demo.crm.customers.store'           => \App\Models\Customer::class,
        'demo.crm.drivers.store'             => \App\Models\Driver::class,
        'demo.crm.investors.store'           => \App\Models\Investor::class,
    ];

    private string $token;

    public function __construct(Request $request)
    {
        $this->token = $request->cookie(self::COOKIE_NAME) ?? $this->generate();
    }

    public function token(): string
    {
        return $this->token;
    }

    /** Count how many demo records exist for a given model class under this token. */
    public function count(string $modelClass): int
    {
        $table = self::MODEL_TABLE_MAP[$modelClass] ?? null;
        if (!$table) return 0;

        return \DB::table($table)
            ->where('demo_token', $this->token)
            ->count();
    }

    /** Returns true when the user has hit the limit for the given model. */
    public function limitReached(string $modelClass): bool
    {
        return $this->count($modelClass) >= self::RECORD_LIMIT;
    }

    /** Returns remaining slots for a model. */
    public function remaining(string $modelClass): int
    {
        return max(0, self::RECORD_LIMIT - $this->count($modelClass));
    }

    /** Returns true if this token has never had any data seeded yet. */
    public function isFirstVisit(): bool
    {
        foreach (self::MODEL_TABLE_MAP as $table) {
            $count = \DB::table($table)->where('demo_token', $this->token)->count();
            if ($count > 0) return false;
        }
        return true;
    }

    private function generate(): string
    {
        return bin2hex(random_bytes(32)); // 64 hex chars
    }
}
