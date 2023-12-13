<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SpendingDataRequest;
use App\Models\Spending\Account;
use App\Models\Spending\Transaction;

class DashboardController extends Controller
{
    // FIXME
    public function getSpendingData(SpendingDataRequest $request)
    {
        $accounts = [];
        $totals = [];
        $categoryExpenses = [];
        $totals = [
            'balance' => 0,
            'allIncome' => 0,
            'allExpense' => 0,
            'profit' => 0,
            'basicExpense' => 0,
            'premiumExpense' => 0,
        ];

        $transactions = Transaction::where('status', 1)
            ->whereYear('date', $request->year)
            ->when($request->filled('month'), function ($query) use ($request) {
                return $query->whereMonth('date', $request->month);
            })
            ->with('transactionCategory')
            ->get();

        $groupedTransactions = $transactions->groupBy('transactionCategory.id');
        foreach ($groupedTransactions as $categoryId => $categoryTransactions) {
            $result = [
                'category' => [
                    'id' => $categoryId,
                    'name' => $categoryTransactions->first()->transactionCategory->name,
                    'type' => $categoryTransactions->first()->transactionCategory->transaction_type,
                ],
                'amount' => $categoryTransactions->sum('amount'),
                'transactions' => $categoryTransactions->map(function ($transaction) {
                    return [
                        'date' => $transaction->date,
                        'amount' => $transaction->amount,
                        'comment' => $transaction->comment,
                        'account' => $transaction->account->name,
                    ];
                })->toArray()
            ];

            if (in_array($result['category']['id'], explode(',', env('BASIC_EXPENSE_CATEGORIES')))) {
                $totals['basicExpense'] += $result['amount'];
            }
            if (in_array($result['category']['id'], explode(',', env('PREMIUM_EXPENSE_CATEGORIES')))) {
                $totals['premiumExpense'] += $result['amount'];
            }

            $categoryExpenses[] = $result;
        }

        foreach (Account::where('status', 1)->get() as $account) {
            $incomeTotal = $account->transactions()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->whereHas('transactionCategory', function ($query) {
                    $query->where('transaction_type', 'income');
                })
                ->sum('amount');

            $expenseTotal = $account->transactions()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->whereHas('transactionCategory', function ($query) {
                    $query->where('transaction_type', 'expense');
                })
                ->sum('amount');

            $profit = $incomeTotal - $expenseTotal;
            $accounts[] = [
                'name' => $account->name,
                'balance' => $account->balance,
                'income' => $incomeTotal,
                'expense' => $expenseTotal,
                'profit' => $profit,
            ];
        }

        foreach ($accounts as $account) {
            $totals['balance'] += $account['balance'];

            $totals['allIncome'] += $account['income'];
            $totals['allExpense'] += $account['expense'];

            $profit = $account['income'] - $account['expense'];
            $totals['profit'] += $profit;
        }

        $latestTransactions = [];
        foreach (Transaction::where('status', 1)->orderBy('date', 'desc')->take(12)->get() as $transaction) {
            $latestTransactions[] = [
                'transaction' => [
                    'type' => $transaction->transactionCategory->transaction_type,
                    'category' => $transaction->transactionCategory->name,
                    'comment' => $transaction->comment,
                ],
                'amount' => $transaction->amount,
            ];
        }

        $data = [
            'totals' => $totals,
            'accounts' => $accounts,
            'expenses' => $categoryExpenses,
            'latestTransactions' => $latestTransactions
        ];

        return [
            "data" => $data
        ];
    }
}
