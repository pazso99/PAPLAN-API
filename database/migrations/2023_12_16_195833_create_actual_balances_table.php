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
        Schema::create('spending.actual_balances', function (Blueprint $table) {
            $table->string('date')->primary();
            $table->integer('amount');
        });

        DB::table('spending.actual_balances')->insert([
            ['date' => '2023-01', 'amount' => 0],
            ['date' => '2023-02', 'amount' => 0],
            ['date' => '2023-03', 'amount' => 0],
            ['date' => '2023-04', 'amount' => 0],
            ['date' => '2023-05', 'amount' => 0],
            ['date' => '2023-06', 'amount' => 0],
            ['date' => '2023-07', 'amount' => 0],
            ['date' => '2023-08', 'amount' => 0],
            ['date' => '2023-09', 'amount' => 0],
            ['date' => '2023-10', 'amount' => 0],
            ['date' => '2023-11', 'amount' => 0],
            ['date' => '2023-12', 'amount' => 0],

            ['date' => '2024-01', 'amount' => 0],
            ['date' => '2024-02', 'amount' => 0],
            ['date' => '2024-03', 'amount' => 0],
            ['date' => '2024-04', 'amount' => 0],
            ['date' => '2024-05', 'amount' => 0],
            ['date' => '2024-06', 'amount' => 0],
            ['date' => '2024-07', 'amount' => 0],
            ['date' => '2024-08', 'amount' => 0],
            ['date' => '2024-09', 'amount' => 0],
            ['date' => '2024-10', 'amount' => 0],
            ['date' => '2024-11', 'amount' => 0],
            ['date' => '2024-12', 'amount' => 0],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending.actual_balances');
    }
};
