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
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['contract_reference_id']);
            $table->dropColumn('contract_reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->uuid('contract_reference_id')->nullable()->after('contact_id');
            $table->foreign('contract_reference_id')->references('id')->on('contracts')->onDelete('set null');
        });
    }
};
