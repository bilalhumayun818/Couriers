<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDemoToken;

class Expense extends Model
{
    use HasDemoToken;
    protected $fillable = [
        'van_id', 'category', 'expense_date',
        'amount', 'description', 'status', 'created_by', 'demo_token',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public static array $categories = [
        'Fuel',
        'Tolls',
        'Spare Parts',
        'Maintenance/Repairs',
    ];

    public function van()
    {
        return $this->belongsTo(Van::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'Fuel'                  => 'amber',
            'Tolls'                 => 'slate',
            'Spare Parts'           => 'indigo',
            'Maintenance/Repairs'   => 'red',
            default                 => 'gray',
        };
    }

    /** Scope to only records belonging to this demo token. */
    public function scopeForDemo($query, string $token)
    {
        return $query->where('demo_token', $token);
    }
}
