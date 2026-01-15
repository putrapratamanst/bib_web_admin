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
            $table->enum('transaction_type', ['bank_transaction', 'bank_to_account'])->default('bank_transaction')->after('type');
            $table->ulid('contra_account_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_banks', function (Blueprint $table) {
            $table->dropColumn('transaction_type');
        });
    }
};
