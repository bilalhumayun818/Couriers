<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'full_name', 'national_id', 'licence_number',
        'licence_expiry_date', 'contact_number', 'emergency_contact',
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
}
