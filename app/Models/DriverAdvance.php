<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDemoToken;

class DriverAdvance extends Model
{
    use HasDemoToken;
    protected $fillable = [
        'driver_id', 'van_id', 'advance_date',
        'amount', 'purpose', 'pay_period', 'demo_token',
    ];

    protected $casts = [
        'advance_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function driver() { return $this->belongsTo(Driver::class); }
    public function van()    { return $this->belongsTo(Van::class); }

    /** Scope to only records belonging to this demo token. */
    public function scopeForDemo($query, string $token)
    {
        return $query->where('demo_token', $token);
    }
}
