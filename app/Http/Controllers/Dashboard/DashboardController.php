<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SpendingDataRequest;
use App\Http\Resources\Dashboard\AccountResource;
use App\Http\Resources\Dashboard\TransactionResource;
use App\Models\Spending\Account;
use App\Models\Spending\Transaction;

class DashboardController extends Controller
{
    // TODO: by date $request->date
    public function getSpendingData(SpendingDataRequest $request)
    {
        return [
            'accounts' => AccountResource::collection(
                Account::where('status', 1)->get()
            ),
            'latestTransactions' => TransactionResource::collection(
                Transaction::where('status', 1)->orderByDesc('date')->limit(5)->get()
            ),
            'monthlyCategoryExpenses' => [
                'category' => 'TODO',
                'category2' => 'TODO',
            ]
        ];
    }
}
