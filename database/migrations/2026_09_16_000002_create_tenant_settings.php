<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Acme Logistics Ltd');
            $table->string('currency_code', 3)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->decimal('tax_rate', 5, 2)->default(8);
            $table->string('tax_label', 40)->default('VAT');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tenant_settings'); }
};
