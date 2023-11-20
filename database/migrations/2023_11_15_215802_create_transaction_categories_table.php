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
        Schema::create('spending.transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('name');
            $table->string('slug');
            $table->enum('transaction_type', ['income', 'expense', 'transfer']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending.transaction_categories');
    }
};
