<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VanDriverAssignment extends Model
{
    protected $table = 'van_driver_assignments';

    protected $fillable = [
        'van_id', 'driver_id', 'start_date', 'end_date',
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
