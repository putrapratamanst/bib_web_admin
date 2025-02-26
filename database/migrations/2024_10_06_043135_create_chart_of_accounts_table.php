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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedBigInteger('account_category_id');
            $table->string('code', 10);
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->enum('balance_type', ['DEBIT', 'CREDIT']);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_editable')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('account_category_id')->references('id')->on('account_categories');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
