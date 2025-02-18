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
            $table->string('number', 50)->unique(true);
            $table->uuid('client_id')->comment('Client');
            $table->string('address', 250)->nullable(true);
            $table->date('period_start');
            $table->date('period_end');
            $table->string('description', 250)->nullable(true);
            $table->integer('count_of_item')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('currency_id');
            $table->decimal('currency_rate', 18, 2);
            $table->decimal('discount', 18, 2);
            $table->decimal('gross_amount', 18, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_type_id')->references('id')->on('contract_types');
            $table->foreign('client_id')->references('id')->on('contacts');
            $table->foreign('currency_id')->references('id')->on('currencies');
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
