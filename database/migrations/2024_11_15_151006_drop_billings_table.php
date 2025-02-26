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
        Schema::table('billings', function (Blueprint $table) {
            // Drop table
            Schema::dropIfExists('billings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['AR', 'AP']);
            $table->string('number', 100)->unique();
            $table->uuid('contact_id');
            $table->uuid('contract_id')->nullable();
            $table->string('reference', 100)->nullable();
            $table->date('date');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->string('currency_code', 3);
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['unpaid', 'paid', 'cancelled']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('currency_code')->references('code')->on('currencies');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }
};
