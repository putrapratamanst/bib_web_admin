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
        Schema::table('cash_banks', function (Blueprint $table) {
            $table->string('currency_code', 3)->after('chart_of_account_id');

            $table->foreign('currency_code')->references('code')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_banks', function (Blueprint $table) {
            $table->dropForeign(['cash_banks_currency_code_foreign']);
            $table->dropColumn('currency_code');
        });
    }
};
