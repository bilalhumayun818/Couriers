<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAdvance extends Model
{
    protected $fillable = [
        'driver_id', 'van_id', 'advance_date',
        'amount', 'purpose', 'pay_period',
    ];

    protected $casts = [
        'advance_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function driver() { return $this->belongsTo(Driver::class); }
    public function van()    { return $this->belongsTo(Van::class); }
}
