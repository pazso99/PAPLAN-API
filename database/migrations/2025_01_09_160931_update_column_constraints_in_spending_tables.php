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
        Schema::table('spending.accounts', function (Blueprint $table) {
            $table->string('name')->unique()->change();
        });

        Schema::table('spending.transaction_categories', function (Blueprint $table) {
            $table->string('name')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spending.accounts', function (Blueprint $table) {
            $table->dropUnique('spending_accounts_name_unique');
        });

        Schema::table('spending.transaction_categories', function (Blueprint $table) {
            $table->dropUnique('spending_transaction_categories_name_unique');
        });
    }
};
