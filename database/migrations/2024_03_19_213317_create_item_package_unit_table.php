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
        Schema::create('inventory.item_package_unit', function (Blueprint $table) {
            $table->foreignId('item_id')->references('id')->on('inventory.items');
            $table->foreignId('package_unit_id')->references('id')->on('inventory.package_units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory.item_package_unit');
    }
};
