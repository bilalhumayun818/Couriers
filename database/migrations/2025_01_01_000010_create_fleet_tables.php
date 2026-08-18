<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drivers table (referenced by vans)
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('national_id')->nullable();
            $table->string('licence_number')->unique();
            $table->date('licence_expiry_date');
            $table->string('contact_number')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Vans table
        Schema::create('vans', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('make_model');
            $table->unsignedSmallInteger('year');
            $table->enum('status', ['active', 'maintenance', 'leased'])->default('active');
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('make_model');
            $table->index('status');
        });

        // Fixed costs table (one row per van)
        Schema::create('van_fixed_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('van_id')->unique()->constrained('vans')->cascadeOnDelete();
            $table->decimal('monthly_lease', 12, 2)->default(0);
            $table->decimal('road_tax_annual', 12, 2)->default(0);
            $table->decimal('insurance_monthly', 12, 2)->default(0);
            $table->date('next_service_date')->nullable();
            $table->timestamps();
        });

        // Van-Driver assignment history
        Schema::create('van_driver_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('van_id')->constrained('vans')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->index(['van_id', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('van_driver_assignments');
        Schema::dropIfExists('van_fixed_costs');
        Schema::dropIfExists('vans');
        Schema::dropIfExists('drivers');
    }
};
