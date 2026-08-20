<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WagePayout extends Model
{
    protected $fillable = [
        'driver_id', 'pay_period', 'gross_wage',
        'total_advances', 'net_wage', 'is_negative', 'confirmed',
    ];

    protected $casts = [
        'gross_wage'     => 'decimal:2',
        'total_advances' => 'decimal:2',
        'net_wage'       => 'decimal:2',
        'is_negative'    => 'boolean',
        'confirmed'      => 'boolean',
    ];

    public function driver() { return $this->belongsTo(Driver::class); }
}
