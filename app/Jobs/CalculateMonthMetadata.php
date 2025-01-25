<?php

namespace App\Jobs;

use App\Models\Config;
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

    private $date;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $year,
        private string $month,
    ) {
        $this->date = Carbon::createFromDate($this->year, $this->month);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $basicExpenseCategories = Config::getValue('spending_basic_transaction_categories');
        $premiumExpenseCategories = Config::getValue('spending_premium_transaction_categories');

        $monthlyData = [
            'total_balance' => 0,
            'total_income' => 0,
            'total_basic_expense' => 0,
            'total_premium_expense' => 0,
        ];

        $activeAccounts = Account::active()->where('created_at', '<=', $this->date)->orderBy('id')->get();

        if ($activeAccounts->isEmpty()) {
            return;
        }

        $accountInfos = [];
        foreach ($activeAccounts as $account) {
            $balance = $this->getAccountBalance($account);

            $accountInfos[$account->id] = [
                'balance' => $balance,
                'income' => 0,
                'basic_expense' => 0,
                'premium_expense' => 0,
                'transfer' => 0,
            ];
            $monthlyData['total_balance'] += $balance;
        }

        $transactions = Transaction::active()
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->with('transactionCategory')
            ->get();

        foreach ($transactions as $transaction) {
            if ($transaction->transactionCategory->transaction_type === 'expense') {
                if (in_array($transaction->transaction_category_id, $basicExpenseCategories)) {
                    $accountInfos[$transaction->account_id]['basic_expense'] += $transaction->amount;
                    $monthlyData['total_basic_expense'] += $transaction->amount;
                }
                if (in_array($transaction->transaction_category_id, $premiumExpenseCategories)) {
                    $accountInfos[$transaction->account_id]['premium_expense'] += $transaction->amount;
                    $monthlyData['total_premium_expense'] += $transaction->amount;
                }
                $accountInfos[$transaction->account_id]['balance'] -= $transaction->amount;
                $monthlyData['total_balance'] -= $transaction->amount;
            } else if ($transaction->transactionCategory->transaction_type === 'income') {
                $accountInfos[$transaction->account_id]['income'] += $transaction->amount;
                $monthlyData['total_income'] += $transaction->amount;
                $accountInfos[$transaction->account_id]['balance'] += $transaction->amount;
                $monthlyData['total_balance'] += $transaction->amount;
            } else if ($transaction->transactionCategory->transaction_type === 'transfer') {
                $meta = json_decode($transaction->meta);
                $accountInfos[$transaction->account_id]['transfer'] += $transaction->amount;
                $accountInfos[$meta->toAccountId]['income'] += $transaction->amount;
                $accountInfos[$transaction->account_id]['balance'] -= $transaction->amount;
                $accountInfos[$meta->toAccountId]['balance'] += $transaction->amount;
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

        foreach ($accountInfos as $accountId => $account) {
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
        $previousDate = $this->date->subMonthsNoOverflow();

        $monthMetadata = MonthlyMetadata::with('monthlyMetadataAccounts')
            ->where('year', '=', $previousDate->year)
            ->where('month', '=', $previousDate->format('m'))
            ->whereHas('monthlyMetadataAccounts', function ($query) use ($account) {
                $query->where('account_id', '=', $account->id);
            })
            ->first();


        if ($monthMetadata) {
            $account = $monthMetadata->monthlyMetadataAccounts()->where('account_id', '=', $account->id)->first();
            return $account->balance;
        }

        return $account->start_balance;
    }
}
