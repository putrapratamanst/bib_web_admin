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
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->decimal('write_off_amount', 18, 2)->default(0)->after('allocation')
                ->comment('Amount for Loss/Gain on Collection');
            $table->enum('write_off_type', ['none', 'loss', 'gain'])->default('none')->after('write_off_amount')
                ->comment('Type: loss = Loss on Collection, gain = Gain on Collection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_allocations', function (Blueprint $table) {
            $table->dropColumn(['write_off_amount', 'write_off_type']);
        });
    }
};
