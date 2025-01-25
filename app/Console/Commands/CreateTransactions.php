<?php

namespace App\Console\Commands;

use App\Models\Spending\Account;
use App\Models\Spending\Transaction;
use App\Models\Spending\TransactionCategory;
use Database\Seeders\TransactionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spending:create-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates random, or import transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('Do you want to init transaction with random data?', true)) {
            $seeder = new TransactionSeeder();

            $seeder->run();
        } else {
            $fileName = $this->ask('Enter the file name', 'spending.csv');

            if (($open = fopen(storage_path() . "/$fileName", 'r')) !== false) {
                DB::table('spending.accounts')->truncate();
                DB::table('spending.transaction_categories')->truncate();
                DB::table('spending.transactions')->truncate();

                $accounts = [];
                $transactionCategories = [];
                $row = 0;

                /*
                    1.                         accounts : account1:0,account2:10000
                    2.    transaction category (income) : income1,income2
                    3.   transaction category (expense) : expense1,expense2,expense3
                    4...                   transactions : 2023-01-01,income1,10000,comment,account1
                                                        : 2023-01-01,expense2,5000,comment,account2
                */

                while (($data = fgetcsv($open, 1000, ',')) !== false) {
                    if ($row === 0) {
                        // 1. row: accounts
                        foreach ($data as $account) {
                            $accountInfo = explode(':', $account);

                            $generatedAccount = Account::create([
                                'name' => $accountInfo[0],
                                'slug' => Str::slug($accountInfo[0], '-'),
                                'balance' => $accountInfo[1],
                                'start_balance' => $accountInfo[1]
                            ]);

                            $accounts[$accountInfo[0]] = $generatedAccount->id;
                        }
                    } else if ($row === 1) {
                        // 2. row: income transaction category
                        foreach ($data as $transactionCategory) {
                            $incomeTransactionCategory = TransactionCategory::create([
                                'transaction_type' => 'income',
                                'name' => $transactionCategory,
                                'slug' => Str::slug($transactionCategory, '-')
                            ]);

                            $transactionCategories[$transactionCategory] = $incomeTransactionCategory->id;
                        }
                    } else if ($row === 2) {
                        // 3. row: expense transaction category
                        foreach ($data as $transactionCategory) {
                            $expenseTransactionCategory = TransactionCategory::create([
                                'transaction_type' => 'expense',
                                'name' => $transactionCategory,
                                'slug' => Str::slug($transactionCategory, '-')
                            ]);

                            $transactionCategories[$transactionCategory] = $expenseTransactionCategory->id;
                        }
                    } else {
                        // transactions
                        Transaction::create([
                            'date' => $data[0],
                            'transaction_category_id' => $transactionCategories[$data[1]],
                            'amount' => $data[2],
                            'comment' => $data[3],
                            'account_id' => $accounts[$data[4]],
                            'meta' => '{}'
                        ]);
                    }

                    $row++;
                }

                fclose($open);
            }
        }
    }
}
