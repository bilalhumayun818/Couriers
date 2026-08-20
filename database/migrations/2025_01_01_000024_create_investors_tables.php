<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('role', ['investor', 'director']);
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('initial_capital', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('investor_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            $table->enum('type', ['injection', 'distribution']);
            $table->date('transaction_date');
            $table->decimal('amount', 12, 2);
            $table->string('description', 500)->nullable();
            $table->timestamps();

            $table->index(['investor_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investor_transactions');
        Schema::dropIfExists('investors');
    }
};
