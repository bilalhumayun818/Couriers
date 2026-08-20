<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'van_id',
        'customer_id',
        'trip_date',
        'origin',
        'destination',
        'fare_amount',
        'tax_amount',
        'total_amount',
        'status',
        'created_by',
    ];

    protected $casts = [
        'trip_date'    => 'date',
        'fare_amount'  => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // ── Relationships ──

    public function van()
    {
        return $this->belongsTo(Van::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVoided($query)
    {
        return $query->where('status', 'voided');
    }
}
