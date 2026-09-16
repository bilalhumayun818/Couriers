<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    protected $fillable = ['company_name', 'currency_code', 'currency_symbol', 'tax_rate', 'tax_label'];
    protected $attributes = ['company_name' => 'Acme Logistics Ltd', 'currency_code' => 'USD', 'currency_symbol' => '$', 'tax_rate' => 8, 'tax_label' => 'VAT'];

    public static function current(): self
    {
        // Public demo requests always use independent defaults.
        if (request()->attributes->get('is_demo')) {
            return new self;
        }

        return self::find(1) ?? new self;
    }
}
