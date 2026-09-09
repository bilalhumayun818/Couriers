<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasDemoToken;

class InvestorTransaction extends Model
{
    use HasDemoToken;

    protected $table = 'investor_transactions';

    protected $fillable = [
        'investor_id', 'type', 'transaction_date', 'amount', 'description', 'demo_token',
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
