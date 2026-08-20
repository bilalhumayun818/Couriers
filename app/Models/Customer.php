<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'billing_address',
        'credit_limit',
    ];

    // ── Relationships ──

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
