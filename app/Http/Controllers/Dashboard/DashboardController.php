<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SpendingDataRequest;
use App\Models\Spending\Account;
use App\Models\Spending\Transaction;
use App\Models\Spending\TransactionCategory;

class DashboardController extends Controller
{
    public function getSpendingData(SpendingDataRequest $request)
    {
        $accounts = [];
        $totals = [];
        $categories = [];
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

        $categoriesWithTransactions = [];
        $groupedTransactions = $transactions->groupBy('transactionCategory.id');
        foreach ($groupedTransactions as $categoryId => $categoryTransactions) {
            $categoriesWithTransactions[] = $categoryId;
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

            $categories[] = $result;
        }

        foreach (TransactionCategory::where('status', 1)->whereNotIn('id', $categoriesWithTransactions)->get() as $category) {
            $categories[] = [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->transaction_type,
                ],
                'amount' => 0,
                'transactions' => []
            ];
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
                'id' => $account->id,
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
                    'account' => $transaction->account->name,
                    'date' => $transaction->date,
                ],
                'amount' => $transaction->amount,
            ];
        }

        return [
            'data' => [
                'totals' => $totals,
                'accounts' => $accounts,
                'categories' => collect($categories)->sortBy('category.id')->values()->all(),
                'latestTransactions' => $latestTransactions
            ]
        ];
    }
}
