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
        Schema::create('cash_bank_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('cash_bank_id');
            $table->uuid('debit_note_id')->nullable();
            $table->decimal('amount', 18, 2);
            $table->timestamps();

            $table->foreign('cash_bank_id')->references('id')->on('cash_banks')->cascadeOnDelete();
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_bank_details');
    }
};
