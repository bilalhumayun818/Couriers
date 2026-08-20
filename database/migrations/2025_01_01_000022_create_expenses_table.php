<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('van_id')->constrained('vans')->cascadeOnDelete();
            $table->string('category'); // Fuel, Tolls, Spare Parts, Maintenance/Repairs
            $table->date('expense_date');
            $table->decimal('amount', 12, 2);
            $table->string('description', 500)->nullable();
            $table->enum('status', ['active', 'deleted'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['van_id', 'expense_date']);
            $table->index('category');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
