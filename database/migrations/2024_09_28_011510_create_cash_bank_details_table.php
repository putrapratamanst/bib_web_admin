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
            $table->ulid('chart_of_account_id')->comment('pembayaran ke atau dari');
            $table->string('description', 160)->nullable();
            $table->decimal('amount', 18, 2);
            $table->timestamps();
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
