<?php

namespace App\Http\Controllers\Spending;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spending\SettingsUpdateRequest;
use App\Http\Requests\Spending\MonthMetadataCalculateRequest;
use App\Http\Requests\Spending\MonthMetadataUpdateRequest;
use App\Jobs\CalculateMonthMetadata;
use App\Models\Spending\MonthlyMetadata;
use App\Models\Spending\MonthlyMetadataAccount;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function getSpendingSettings()
    {
        $spendingConfigs = DB::table('config')->get(['key', 'value'])
            ->map(function ($record) {
                return [$record->key => json_decode($record->value, true)];
            })
            ->collapse()
            ->toArray();

        return [
            'data' => [
                'configs' => $spendingConfigs,
            ]
        ];
    }

    public function updateSpendingSettings(SettingsUpdateRequest $request)
    {
        foreach ($request->configs as $key => $value) {
            DB::table('config')->where('key', $key)->update(['value' => json_encode($value)]);
        }

        return $this->getSpendingSettings();
    }

    public function calculateMonthMetadata(MonthMetadataCalculateRequest $request)
    {
        CalculateMonthMetadata::dispatch($request->year, $request->month);

        return [
            'data' => [
                'calculated' => true,
            ]
        ];
    }

    public function getMonthsMetadata()
    {
        $monthlyMetadata = [];
        foreach (
            MonthlyMetadata::with('monthlyMetadataAccounts')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
            as $monthMetadata
        ) {

            $accounts = [];
            foreach (
                $monthMetadata->monthlyMetadataAccounts()
                    ->with('account')
                    ->orderBy('id')
                    ->get()
                as $monthMetadataAccount
            ) {
                $accounts[] = [
                    'id' => $monthMetadataAccount->account->id,
                    'accountName' => $monthMetadataAccount->account->name,
                    'balance' => $monthMetadataAccount->balance,
                    'income' => $monthMetadataAccount->income,
                    'basicExpense' => $monthMetadataAccount->basic_expense,
                    'premiumExpense' => $monthMetadataAccount->premium_expense,
                    'transfer' => $monthMetadataAccount->transfer,
                ];
            }

            $monthlyMetadata[] = [
                'id' => $monthMetadata->id,
                'year' => $monthMetadata->year,
                'month' => $monthMetadata->month,
                'totalBalance' => $monthMetadata->total_balance,
                'totalIncome' => $monthMetadata->total_income,
                'totalBasicExpense' => $monthMetadata->total_basic_expense,
                'totalPremiumExpense' => $monthMetadata->total_premium_expense,
                'accounts' => $accounts,
            ];
        }

        return [
            'data' => [
                'monthlyMetadata' => $monthlyMetadata,
            ]
        ];
    }

    public function updateMonthMetadata(MonthlyMetadata $monthlyMetadata, MonthMetadataUpdateRequest $request)
    {
        $monthlyMetadata->update([
            'total_balance' => $request->totalBalance,
            'total_income' => $request->totalIncome,
            'total_basic_expense' => $request->totalBasicExpense,
            'total_premium_expense' => $request->totalPremiumExpense,
        ]);

        foreach ($request->accounts as $account) {
            MonthlyMetadataAccount::find($account['id'])->update([
                'balance' => $account['balance'],
                'income' => $account['income'],
                'basic_expense' => $account['basicExpense'],
                'premium_expense' => $account['premiumExpense'],
                'transfer' => $account['transfer'],
            ]);
        }

        return $monthlyMetadata;
    }
}
