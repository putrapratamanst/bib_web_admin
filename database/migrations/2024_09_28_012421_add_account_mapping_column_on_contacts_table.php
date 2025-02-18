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
        Schema::table('contacts', function (Blueprint $table) {
            $table->ulid('account_mapping_receivable')->nullable()->after('status');
            $table->ulid('account_mapping_payable')->nullable()->after('account_mapping_receivable');

            $table->foreign('account_mapping_receivable')->references('id')->on('chart_of_accounts');
            $table->foreign('account_mapping_payable')->references('id')->on('chart_of_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['account_mapping_receivable']);
            $table->dropForeign(['account_mapping_payable']);

            $table->dropColumn('account_mapping_receivable');
            $table->dropColumn('account_mapping_payable');
        });
    }
};
