<?php

namespace Database\Seeders;

use App\Models\Spending\Account;
use App\Models\Spending\Transaction;
use App\Models\Spending\TransactionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spending.accounts')->truncate();
        DB::table('spending.transaction_categories')->truncate();
        DB::table('spending.transactions')->truncate();

        $defaultAccounts = [
            ['name' => 'Account 1', 'slug' => 'account-1', 'balance' => 100000],
            ['name' => 'Account 2', 'slug' => 'account-2', 'balance' => 0],
            ['name' => 'Account 3', 'slug' => 'account-3', 'balance' => 400000],
        ];

        $defaultCategories = [
            ['transaction_type' => 'income', 'name' => 'Salary', 'slug' => 'salary'],
            ['transaction_type' => 'income', 'name' => 'Bonus', 'slug' => 'bonus'],
            ['transaction_type' => 'income', 'name' => 'Other income', 'slug' => 'other-income'],

            ['transaction_type' => 'expense', 'name' => 'Rent', 'slug' => 'rent'],
            ['transaction_type' => 'expense', 'name' => 'Utilities', 'slug' => 'utilities'],
            ['transaction_type' => 'expense', 'name' => 'Food', 'slug' => 'food'],
            ['transaction_type' => 'expense', 'name' => 'Transportation', 'slug' => 'transportation'],
            ['transaction_type' => 'expense', 'name' => 'Subscription', 'slug' => 'subscription'],
            ['transaction_type' => 'expense', 'name' => 'One-time expense', 'slug' => 'one-time-expense'],
            ['transaction_type' => 'expense', 'name' => 'Other expense', 'slug' => 'other-expense'],
        ];

        $account1 = Account::create($defaultAccounts[0]);
        $account2 = Account::create($defaultAccounts[1]);
        $account3 = Account::create($defaultAccounts[2]);

        $incomeCategories = [];
        $expenseCategories = [];
        foreach ($defaultCategories as $defaultCategory) {
            $transactionCategory = TransactionCategory::create($defaultCategory);

            $transactionCategory->transaction_type === 'income'
                ? $incomeCategories[] = $transactionCategory
                : $expenseCategories[] = $transactionCategory;
        }

        $currentDate = Carbon::yesterday();
        $months = env('CREATE_TRANSACTION_MONTHS', 6);
        $baseSalary = round(rand(150000, 500000), -3);
        $rent = round(rand(100000, 225000), -3);

        for ($i = 0; $i <= $months; $i++) {
            $endDate = $i === 0
                ? $currentDate->format('d')
                : $currentDate->copy()->subMonths($i)->endOfMonth()->format('Y-m-d');

            $startDate = $i === 0
                ? $currentDate->startOfMonth()->format('Y-m-d')
                : $currentDate->copy()->subMonths($i)->startOfMonth()->format('Y-m-d');

            $this->saveTransaction(
                false,
                [
                    'date' => $startDate,
                    'amount' => $baseSalary,
                    'account_id' => $account2->id,
                    'transaction_category_id' => $incomeCategories[0]->id,
                    'comment' => '',
                    'meta' => '{}'
                ],
                $account1
            );

            if (rand(1,10) < 5) {
                $isBonus = rand(1,2) === 1;
                $this->saveTransaction(
                    false,
                    [
                        'date' => $startDate,
                        'amount' => $isBonus ? $baseSalary / 2 : $baseSalary / 10,
                        'account_id' => $account2->id,
                        'transaction_category_id' => $isBonus ? $incomeCategories[1]->id : $incomeCategories[2]->id,
                        'comment' => '',
                        'meta' => '{}'
                    ],
                    $account2
                );
            }

            if ($account2->balance > 100000) {
                $this->saveTransaction(
                    true,
                    [
                        'date' => $startDate,
                        'amount' => 100000,
                        'account_id' => $account2->id,
                        'transaction_category_id' => $expenseCategories[5]->id,
                        'comment' => '',
                        'meta' => '{}'
                    ],
                    $account2
                );
            }

            $this->saveTransaction(
                true,
                [
                    'date' => $startDate,
                    'amount' => $rent,
                    'account_id' => $account1->id,
                    'transaction_category_id' => $expenseCategories[0]->id,
                    'comment' => '',
                    'meta' => '{}'
                ],
                $account1
            );

            $utilities = round(rand(20000, 35000), -3);
            $this->saveTransaction(
                true,
                [
                    'date' => $startDate,
                    'amount' => $utilities,
                    'account_id' => $account1->id,
                    'transaction_category_id' => $expenseCategories[1]->id,
                    'comment' => '',
                    'meta' => '{}'
                ],
                $account1
            );

            for ($j = ($i === 0 ? $endDate : rand(18, 28)); $j >= 0; $j--) {
                $randomDate = $i === 0 ? Carbon::yesterday() : new Carbon($endDate);
                $account = rand(1,3) > 1 ? $account1 : $account3;
                $this->saveTransaction(
                    true,
                    [
                        'date' => $randomDate->subDays($j)->format('Y-m-d'),
                        'amount' => round(rand(1000, 15000), -3),
                        'account_id' => $account->id,
                        'transaction_category_id' => $expenseCategories[rand(2, 6)]->id,
                        'comment' => '',
                        'meta' => '{}'
                    ],
                    $account
                );
            }
        }
    }

    private function saveTransaction($isExpense, $transactionData, $account)
    {
        if ($isExpense) {
            if ($account->balance >= $transactionData['amount']) {
                $transaction = Transaction::create($transactionData);
                $account->balance -= $transaction->amount;
                $account->save();
                return $transaction;
            }
        } else {
            $transaction = Transaction::create($transactionData);
            $account->balance += $transaction->amount;
            $account->save();
            return $transaction;
        }
    }
}
