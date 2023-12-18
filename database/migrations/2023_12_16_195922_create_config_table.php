<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('config', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->jsonb('value');
        });

        // Initial configs
        DB::table('config')->insert([
            ['key' => 'spending_basic_transaction_categories', 'value' => '[]'],
            ['key' => 'spending_premium_transaction_categories', 'value' => '[]'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config');
    }
};
