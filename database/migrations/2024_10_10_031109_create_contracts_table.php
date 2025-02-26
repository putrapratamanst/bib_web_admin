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
        Schema::create('contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('contract_type_id');
            $table->string('number')->unique();
            $table->uuid('contact_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->string('currency_code', 3);
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('amount', 18, 2);
            $table->enum('status', ['draft', 'active', 'inactive']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('contract_type_id')->references('id')->on('contract_types');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->foreign('currency_code')->references('code')->on('currencies');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
