<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasDemoToken;

class Customer extends Model
{
    use SoftDeletes, HasDemoToken;

    protected $fillable = [
        'company_name', 'contact_name', 'email', 'phone', 'billing_address', 'credit_limit', 'demo_token',
    ];

    protected $attributes = [
        'credit_limit' => 0,
    ];

    // ── Relationships ──

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /** Scope to only records belonging to this demo token. */
    public function scopeForDemo($query, string $token)
    {
        return $query->where('demo_token', $token);
    }
}
