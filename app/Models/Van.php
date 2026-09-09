<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasDemoToken;

class Van extends Model
{
    use SoftDeletes, HasDemoToken;

    protected $fillable = [
        'plate_number', 'make_model', 'year', 'status', 'driver_id', 'demo_token',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    // ── Relationships ──

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function fixedCost()
    {
        return $this->hasOne(VanFixedCost::class);
    }

    public function assignments()
    {
        return $this->hasMany(VanDriverAssignment::class);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByModel($query, string $model)
    {
        return $query->where('make_model', $model);
    }

    /** Scope to only records belonging to this demo token. */
    public function scopeForDemo($query, string $token)
    {
        return $query->where('demo_token', $token);
    }

    // ── Helpers ──

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'active'      => 'emerald',
            'maintenance' => 'amber',
            'leased'      => 'blue',
            default       => 'gray',
        };
    }

    public function isServiceDueSoon(): bool
    {
        if (!$this->fixedCost?->next_service_date) return false;
        return $this->fixedCost->next_service_date->diffInDays(now(), false) >= -7
               && $this->fixedCost->next_service_date->isFuture();
    }
}
