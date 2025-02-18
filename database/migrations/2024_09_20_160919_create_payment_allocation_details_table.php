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
        Schema::create('payment_allocation_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payment_allocation_id');
            $table->uuid('billing_id');
            $table->decimal('amount', 18, 2);
            $table->string('description')->nullable();
            $table->timestamps();

            $table->foreign('payment_allocation_id')->references('id')->on('payment_allocations')->onDelete('cascade');
            $table->foreign('billing_id')->references('id')->on('billings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_allocation_details');
    }
};
