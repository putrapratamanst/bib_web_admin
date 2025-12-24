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
        Schema::create('contract_endorsements', function (Blueprint $table) {
            $table->uuid('contract_id');
            $table->uuid('contract_reference_id')->nullable();
            $table->string('endorsement_number')->nullable();
            $table->timestamps();
            
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('contract_reference_id')->references('id')->on('contracts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_endorsements');
    }
};
