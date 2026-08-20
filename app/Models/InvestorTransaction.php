<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestorTransaction extends Model
{
    protected $table = 'investor_transactions';

    protected $fillable = [
        'investor_id', 'type', 'transaction_date', 'amount', 'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
