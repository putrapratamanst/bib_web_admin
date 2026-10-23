<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luminous_invoices', function (Blueprint $table) {
            $table->string('city')->default('Jakarta')->after('date');
            $table->string('subject')->default('Tagihan Biaya Sosialisasi')->after('description');
            $table->string('bank_name')->nullable()->after('document_image');
            $table->string('bank_account')->nullable()->after('bank_name');
            $table->string('account_name')->nullable()->after('bank_account');
            $table->string('signatory_name')->nullable()->after('account_name');
            $table->string('signatory_title')->nullable()->after('signatory_name');
        });
    }

    public function down(): void
    {
        Schema::table('luminous_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'subject',
                'bank_name',
                'bank_account',
                'account_name',
                'signatory_name',
                'signatory_title',
            ]);
        });
    }
};
