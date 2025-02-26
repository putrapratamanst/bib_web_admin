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
        Schema::create('debit_note_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('debit_note_id');
            $table->uuid('insurance_id');
            $table->decimal('amount', 18, 2);
            $table->timestamps();

            $table->foreign('debit_note_id')->references('id')->on('debit_notes');
            $table->foreign('insurance_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_details');
    }
};
