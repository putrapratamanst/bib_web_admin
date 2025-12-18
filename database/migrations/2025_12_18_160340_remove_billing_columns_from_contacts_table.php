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
            $table->dropColumn(['billing_name', 'billing_address', 'billing_email', 'billing_phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('billing_name', 100)->nullable()->after('phone');
            $table->text('billing_address')->nullable()->after('billing_name');
            $table->string('billing_email', 100)->nullable()->after('billing_address');
            $table->string('billing_phone', 20)->nullable()->after('billing_email');
        });
    }
};
