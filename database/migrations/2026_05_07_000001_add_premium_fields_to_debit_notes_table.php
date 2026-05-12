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
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->decimal('gross_premium', 18, 2)->nullable()->after('amount');
            $table->decimal('discount_percent', 8, 2)->nullable()->after('gross_premium');
            $table->decimal('discount_amount', 18, 2)->nullable()->after('discount_percent');
            $table->decimal('net_premium_amount', 18, 2)->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropColumn([
                'gross_premium',
                'discount_percent',
                'discount_amount',
                'net_premium_amount',
            ]);
        });
    }
};
