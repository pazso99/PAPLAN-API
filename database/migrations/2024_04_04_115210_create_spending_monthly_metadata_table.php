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
        Schema::create('spending.monthly_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('month');
            $table->integer('total_balance')->nullable();
            $table->integer('total_income')->nullable();
            $table->integer('total_basic_expense')->nullable();
            $table->integer('total_premium_expense')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending.monthly_metadata');
    }
};
