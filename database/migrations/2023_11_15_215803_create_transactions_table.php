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
        Schema::create('spending.transactions', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->date('date');
            $table->integer('amount');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('transaction_category_id')->nullable();
            $table->string('comment')->nullable();
            $table->json('meta');
            $table->foreign('account_id')->references('id')->on('spending.accounts');
            $table->foreign('transaction_category_id')->references('id')->on('spending.transaction_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending.transactions');
    }
};
