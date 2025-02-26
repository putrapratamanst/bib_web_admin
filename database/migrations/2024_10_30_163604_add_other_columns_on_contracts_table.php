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
            $table->enum('contract_status', ['renewal', 'new'])->after('id');
            $table->string('policy_number', 150)->after('number');
            $table->decimal('coverage_amount', 18, 2)->after('exchange_rate');
            $table->decimal('gross_premium', 18, 2)->after('coverage_amount');
            $table->decimal('discount', 18, 2)->after('gross_premium')->comment('Discount in percentage');
            $table->decimal('stamp_fee', 18, 2)->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('contract_status');
            $table->dropColumn('policy_number');
            $table->dropColumn('coverage_amount');
            $table->dropColumn('gross_premium');
            $table->dropColumn('discount');
            $table->dropColumn('stamp_fee');
        });
    }
};
