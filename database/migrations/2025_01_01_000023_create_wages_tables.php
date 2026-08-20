<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Driver advances
        Schema::create('driver_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('van_id')->nullable()->constrained('vans')->nullOnDelete();
            $table->date('advance_date');
            $table->decimal('amount', 12, 2);
            $table->string('purpose', 255)->nullable();
            $table->string('pay_period', 7); // YYYY-MM
            $table->timestamps();

            $table->index(['driver_id', 'pay_period']);
        });

        // Wage payouts
        Schema::create('wage_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->string('pay_period', 7); // YYYY-MM
            $table->decimal('gross_wage', 12, 2);
            $table->decimal('total_advances', 12, 2)->default(0);
            $table->decimal('net_wage', 12, 2);
            $table->boolean('is_negative')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->timestamps();

            $table->unique(['driver_id', 'pay_period']);
            $table->index('pay_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wage_payouts');
        Schema::dropIfExists('driver_advances');
    }
};
