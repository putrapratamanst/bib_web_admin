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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contact_id')->nullable();
            $table->string('number', 20)->unique();
            $table->timestamp('date');
            $table->enum('type', ['in', 'out']);
            $table->unsignedBigInteger('bank_id');
            $table->string('bank_account_name', 50);
            $table->string('bank_account_number', 50);
            $table->decimal('amount', 18, 2);
            $table->unsignedBigInteger('currency_id');
            $table->decimal('currency_rate', 18, 2);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('bank_id')->references('id')->on('banks');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
