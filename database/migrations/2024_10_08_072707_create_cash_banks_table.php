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
            $table->enum('type', ['receive', 'pay']);
            $table->string('number', 50)->unique();
            $table->uuid('contact_id');
            $table->dateTime('date');
            $table->ulid('chart_of_account_id')->comment('Only Kas Bank');
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
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
