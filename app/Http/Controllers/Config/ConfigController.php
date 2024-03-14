<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spending\SettingsUpdateRequest;
use App\Models\Spending\TransactionCategory;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function getSpendingActualBalances()
    {
        $actualBalances = [];
        foreach (DB::table('spending.actual_balances')->get(['date', 'amount']) as $record) {
            $actualBalances[$record->date] = $record->amount;
        }

        return [
            'data' => $actualBalances
        ];
    }

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

        foreach ($request->actualBalances as $date => $value) {
            DB::table('spending.actual_balances')->where('date', $date)->update(['amount' => $value]);
        }

        return $this->getSpendingSettings();
    }
}
