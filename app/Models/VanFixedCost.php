<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VanFixedCost extends Model
{
    protected $table = 'van_fixed_costs';

    protected $fillable = [
        'van_id', 'monthly_lease', 'road_tax_annual',
        'insurance_monthly', 'next_service_date',
    ];

    protected $casts = [
        'monthly_lease'      => 'decimal:2',
        'road_tax_annual'    => 'decimal:2',
        'insurance_monthly'  => 'decimal:2',
        'next_service_date'  => 'date',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class);
    }

    public function getTotalMonthlyAttribute(): float
    {
        return (float) $this->monthly_lease
             + (float) $this->road_tax_annual / 12
             + (float) $this->insurance_monthly;
    }
}
