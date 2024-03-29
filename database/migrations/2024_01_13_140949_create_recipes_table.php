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
        Schema::create('recipes.recipes', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('time')->nullable();
            $table->string('description', '65535')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes.recipes');
    }
};
