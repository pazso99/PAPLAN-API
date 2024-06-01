<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Spending\Account;
use App\Models\Spending\MonthlyMetadata;
use App\Models\Spending\MonthlyMetadataAccount;
use App\Models\Spending\Transaction;
use Carbon\Carbon;

class CalculateMonthMetadata implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $year,
        private string $month,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $basicExpenseCategories = json_decode(
            DB::table('config')->where('key', 'spending_basic_transaction_categories')->value('value')
        );
        $premiumExpenseCategories = json_decode(
            DB::table('config')->where('key', 'spending_premium_transaction_categories')->value('value')
        );

        $monthlyData = [
            'total_balance' => 0,
            'total_income' => 0,
            'total_basic_expense' => 0,
            'total_premium_expense' => 0,
        ];

        $accounts = [];

        foreach (Account::active()->orderBy('id')->get() as $account) {
            $balance = $this->getAccountBalance($account);
            $accounts[$account->id] = [
                'balance' => $balance,
                'income' => 0,
                'basic_expense' => 0,
                'premium_expense' => 0,
                'transfer' => 0,
            ];
            $monthlyData['total_balance'] += $balance;
        }

        foreach (
            Transaction
                ::active()
                ->whereYear('date', $this->year)
                ->whereMonth('date', $this->month)
                ->with('transactionCategory')
                ->get()
            as $transaction
        ) {
            if ($transaction->transactionCategory->transaction_type === 'expense') {
                if (in_array($transaction->transaction_category_id, $basicExpenseCategories)) {
                    $accounts[$transaction->account_id]['basic_expense'] += $transaction->amount;
                    $monthlyData['total_basic_expense'] += $transaction->amount;
                }
                if (in_array($transaction->transaction_category_id, $premiumExpenseCategories)) {
                    $accounts[$transaction->account_id]['premium_expense'] += $transaction->amount;
                    $monthlyData['total_premium_expense'] += $transaction->amount;
                }
                $accounts[$transaction->account_id]['balance'] -= $transaction->amount;
                $monthlyData['total_balance'] -= $transaction->amount;
            } else if ($transaction->transactionCategory->transaction_type === 'income') {
                $accounts[$transaction->account_id]['income'] += $transaction->amount;
                $monthlyData['total_income'] += $transaction->amount;
                $accounts[$transaction->account_id]['balance'] += $transaction->amount;
                $monthlyData['total_balance'] += $transaction->amount;
            } else if ($transaction->transactionCategory->transaction_type === 'transfer') {
                $meta = json_decode($transaction->meta);
                $accounts[$transaction->account_id]['balance'] -= $transaction->amount;
                $accounts[$transaction->account_id]['transfer'] += $transaction->amount;
                $accounts[$meta->toAccountId]['balance'] += $transaction->amount;
                $accounts[$meta->toAccountId]['income'] += $transaction->amount;
            }
        }

        $monthMetadataId = null;
        $monthMetadata = MonthlyMetadata::where('year', '=', $this->year)->where('month', '=', $this->month)->first();

        if (!$monthMetadata) {
            $monthMetadataId = MonthlyMetadata::insertGetId(
                [
                    'year' => $this->year,
                    'month' => $this->month,
                    'created_at' => now(),
                ]
            );
        } else {
            $monthMetadataId = $monthMetadata->id;
        }

        foreach ($accounts as $accountId => $account) {
            MonthlyMetadataAccount::updateOrInsert([
                'monthly_metadata_id' => $monthMetadataId,
                'account_id' => $accountId,
            ], [
                'balance' => $account['balance'],
                'income' => $account['income'],
                'basic_expense' => $account['basic_expense'],
                'premium_expense' => $account['premium_expense'],
                'transfer' => $account['transfer'],
            ]);
        }

        MonthlyMetadata::find($monthMetadataId)->update(
            [
                'total_basic_expense' => $monthlyData['total_basic_expense'],
                'total_premium_expense' => $monthlyData['total_premium_expense'],
                'total_income' => $monthlyData['total_income'],
                'total_balance' => $monthlyData['total_balance'],
                'updated_at' => now(),
            ]
        );
    }

    private function getAccountBalance(Account $account)
    {
        $date = Carbon::createFromDate($this->year, $this->month);
        $previousDate = $date->subMonthsNoOverflow();

        $monthMetadata = MonthlyMetadata::with('monthlyMetadataAccounts')
            ->where('year', '=', $previousDate->year)
            ->where('month', '=', $previousDate->format('m'))
            ->first();

        $account = $monthMetadata->monthlyMetadataAccounts()->where('account_id', '=', $account->id)->first();
        return $account->balance;
    }
}
