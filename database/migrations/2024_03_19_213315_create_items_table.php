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
        Schema::create('inventory.items', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->foreignId('item_type_id')->references('id')->on('inventory.item_types');
            $table->integer('expected_lifetime_in_days')->nullable();
            $table->integer('recommended_stock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory.items');
    }
};
