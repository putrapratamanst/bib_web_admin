<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contract_types', function (Blueprint $table) {
            $table->string('code', 20)->nullable()->after('id')->unique();
        });

        $contractTypes = DB::table('contract_types')->select('id', 'code')->get();

        foreach ($contractTypes as $contractType) {
            if (empty($contractType->code)) {
                DB::table('contract_types')
                    ->where('id', $contractType->id)
                    ->update(['code' => sprintf('CT%03d', $contractType->id)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_types', function (Blueprint $table) {
            $table->dropUnique('contract_types_code_unique');
            $table->dropColumn('code');
        });
    }
};
