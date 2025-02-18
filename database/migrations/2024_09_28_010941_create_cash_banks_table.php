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
        Schema::create('cash_banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->enum('type', ['receive', 'pay', 'transfer']);
            $table->ulid('chart_of_account_id')->comment('only cash or bank account');
            $table->uuid('contact_id');
            $table->date('date');
            $table->string('reference', 100)->nullable();
            $table->text('memo')->nullable();
            $table->unsignedBigInteger('currency_id');
            $table->decimal('exchange_rate', 18, 2)->default(1);
            $table->decimal('amount', 18, 2);
            $table->timestamps();

            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_banks');
    }
};
