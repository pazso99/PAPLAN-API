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
        Schema::create('spending.monthly_metadata_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_metadata_id')->references('id')->on('spending.monthly_metadata');
            $table->foreignId('account_id');
            $table->integer('balance')->nullable();
            $table->integer('income')->nullable();
            $table->integer('basic_expense')->nullable();
            $table->integer('premium_expense')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending.monthly_metadata_accounts');
    }
};
