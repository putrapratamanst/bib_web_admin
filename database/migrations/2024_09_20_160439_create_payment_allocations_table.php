<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cash_transaction_id');
            $table->string('number', 36)->unique();
            $table->date('date');
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'canceled'])->default('draft');
            $table->timestamps();

            $table->foreign('cash_transaction_id')->references('id')->on('cash_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
