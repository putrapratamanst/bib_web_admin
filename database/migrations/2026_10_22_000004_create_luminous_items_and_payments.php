<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luminous_invoices', function (Blueprint $table) {
            $table->uuid('insurance_id')->nullable()->after('number');
            $table->foreign('insurance_id')->references('id')->on('contacts');
        });

        // cashout_id unik: satu Hutang Asuransi hanya bisa ditagihkan satu kali
        Schema::create('luminous_invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('luminous_invoice_id');
            $table->uuid('cashout_id')->unique();
            $table->decimal('basos_amount', 18, 2);
            $table->timestamps();

            $table->foreign('luminous_invoice_id')->references('id')->on('luminous_invoices')->cascadeOnDelete();
            $table->foreign('cashout_id')->references('id')->on('cashouts');
        });

        Schema::create('luminous_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('luminous_invoice_id');
            $table->date('date');
            $table->decimal('amount', 18, 2);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('luminous_invoice_id')->references('id')->on('luminous_invoices')->cascadeOnDelete();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('luminous_payments');
        Schema::dropIfExists('luminous_invoice_items');
        Schema::table('luminous_invoices', function (Blueprint $table) {
            $table->dropForeign(['insurance_id']);
            $table->dropColumn('insurance_id');
        });
    }
};
