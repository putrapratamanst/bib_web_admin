<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cashouts', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
        });

        Schema::table('debit_notes', function (Blueprint $table) {
            $table->uuid('source_cashout_id')->nullable()->unique()->after('contract_id');
            $table->foreign('source_cashout_id')
                ->references('id')
                ->on('cashouts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropForeign(['source_cashout_id']);
            $table->dropUnique(['source_cashout_id']);
            $table->dropColumn('source_cashout_id');
        });

        Schema::table('cashouts', function (Blueprint $table) {
            $table->dropColumn('paid_at');
        });
    }
};
