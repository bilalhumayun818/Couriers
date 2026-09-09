<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasDemoToken;

class Driver extends Model
{
    use SoftDeletes, HasDemoToken;

    protected $fillable = [
        'full_name', 'national_id', 'licence_number',
        'licence_expiry_date', 'contact_number', 'emergency_contact', 'demo_token',
    ];

    protected $casts = [
        'licence_expiry_date' => 'date',
    ];

    public function vans()
    {
        return $this->hasMany(Van::class);
    }

    public function assignments()
    {
        return $this->hasMany(VanDriverAssignment::class);
    }

    /** Scope to only records belonging to this demo token. */
    public function scopeForDemo($query, string $token)
    {
        return $query->where('demo_token', $token);
    }
}
