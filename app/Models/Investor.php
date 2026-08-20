<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'full_name', 'role', 'bank_account_name',
        'bank_account_number', 'bank_name', 'initial_capital',
    ];

    protected $casts = [
        'initial_capital' => 'decimal:2',
    ];

    protected $attributes = [
        'initial_capital' => 0,
    ];

    public function transactions()
    {
        return $this->hasMany(InvestorTransaction::class)->orderBy('transaction_date');
    }

    // Retained balance = initial_capital + injections - distributions
    public function getRetainedBalanceAttribute(): float
    {
        $injections    = $this->transactions->where('type', 'injection')->sum('amount');
        $distributions = $this->transactions->where('type', 'distribution')->sum('amount');
        return (float) $this->initial_capital + $injections - $distributions;
    }

    public function getTotalInjectionsAttribute(): float
    {
        return (float) $this->transactions->where('type', 'injection')->sum('amount');
    }

    public function getTotalDistributionsAttribute(): float
    {
        return (float) $this->transactions->where('type', 'distribution')->sum('amount');
    }
}
