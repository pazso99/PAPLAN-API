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
        Schema::create('spending.category_group_transaction_category', function (Blueprint $table) {
            $table->unsignedBigInteger('category_group_id')->nullable();
            $table->unsignedBigInteger('transaction_category_id')->nullable();
            $table->foreign('category_group_id')->references('id')->on('spending.category_groups');
            $table->foreign('transaction_category_id')->references('id')->on('spending.transaction_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending.category_group_transaction_category');
    }
};
