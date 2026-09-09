<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDemoToken;

class VanDriverAssignment extends Model
{
    use HasDemoToken;

    protected $table = 'van_driver_assignments';

    protected $fillable = [
        'van_id', 'driver_id', 'start_date', 'end_date', 'demo_token',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function isActive(): bool
    {
        return $this->end_date === null;
    }
}
