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
        Schema::table('contract_details', function (Blueprint $table) {
            $table->decimal('brokerage_fee', 18, 2)->nullable()->after('percentage');
            $table->decimal('eng_fee', 18, 2)->nullable()->after('brokerage_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_details', function (Blueprint $table) {
            $table->dropColumn('brokerage_fee');
            $table->dropColumn('eng_fee');
        });
    }
};
