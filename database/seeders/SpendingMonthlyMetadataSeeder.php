<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpendingMonthlyMetadataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=SpendingMonthlyMetadataSeeder
     */
    public function run(): void
    {
        $monthlyMetadata = include storage_path('/monthlyMetadata.php');

        DB::table('spending.monthly_metadata')->truncate();
        DB::table('spending.monthly_metadata_accounts')->truncate();

        foreach ($monthlyMetadata as $monthMetadata) {
            $monthMetadataId = DB::table('spending.monthly_metadata')->insertGetId(
                [
                    'year' => $monthMetadata['year'],
                    'month' => $monthMetadata['month'],
                    'total_basic_expense' => $monthMetadata['total_basic_expense'],
                    'total_premium_expense' => $monthMetadata['total_premium_expense'],
                    'total_income' => $monthMetadata['total_income'],
                    'total_balance' => $monthMetadata['total_balance'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            foreach ($monthMetadata['accounts'] as $account) {
                DB::table('spending.monthly_metadata_accounts')->insert(
                    [
                        'monthly_metadata_id' => $monthMetadataId,
                        'account_id' => $account['id'],
                        'balance' => $account['balance'],
                        'income' => $account['income'],
                        'basic_expense' => $account['basic_expense'],
                        'premium_expense' => $account['premium_expense'],
                        'transfer' => $account['transfer'],
                    ]
                );
            }
        }
    }
}
