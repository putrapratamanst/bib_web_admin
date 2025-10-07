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
        Schema::create('cashouts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('debit_note_id');
            $table->uuid('insurance_id'); // contact ID dari insurance company
            $table->string('number', 100); // nomor cashout
            $table->date('date'); // tanggal cashout
            $table->date('due_date')->nullable(); // tanggal jatuh tempo
            $table->string('currency_code', 3);
            $table->decimal('exchange_rate', 18, 2);
            $table->decimal('amount', 18, 2); // amount yang harus dibayar ke insurance
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->cascadeOnDelete();
            $table->foreign('insurance_id')->references('id')->on('contacts');
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
        Schema::dropIfExists('cashouts');
    }
};
