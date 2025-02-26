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
        Schema::table('credit_notes', function (Blueprint $table) {
            // remove foreign key
            $table->dropForeign(['billing_id']);

            $table->dropColumn('billing_id');

            $table->uuid('contract_id')->after('id');
            $table->uuid('debit_note_id')->after('contract_id')->nullable();

            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('debit_note_id')->references('id')->on('debit_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            // remove foreign key
            $table->dropForeign(['contract_id']);
            $table->dropForeign(['debit_note_id']);

            $table->dropColumn('contract_id');
            $table->dropColumn('debit_note_id');

            $table->uuid('billing_id')->after('id');            
            $table->foreign('billing_id')->references('id')->on('billings');
        });
    }
};
