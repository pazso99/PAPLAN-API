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
        Schema::table('spending.monthly_metadata_accounts', function (Blueprint $table) {
            $table->integer('transfer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spending.monthly_metadata_accounts', function (Blueprint $table) {
            $table->dropColumn('transfer');
        });
    }
};
