<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SpendingDataRequest;
use App\Models\Spending\Account;
use App\Models\Spending\Transaction;
use App\Models\Spending\TransactionCategory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getSpendingData(SpendingDataRequest $request)
    {
        $totals = [
            'balance' => 0,
            'income' => 0,
            'expense' => 0,
            'profit' => 0,
            'basicExpense' => 0,
            'premiumExpense' => 0,
        ];

        // Getting transactions info by categories in the selected date
        $transactionDataByCategories = [];
        $categoryIds = [];
        $basicExpenseCategories = json_decode(
            DB::table('config')->where('key', 'spending_basic_transaction_categories')->value('value')
        );
        $premiumExpenseCategories = json_decode(
            DB::table('config')->where('key', 'spending_premium_transaction_categories')->value('value')
        );
        foreach (
            Transaction
                ::active()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->with('transactionCategory')
                ->get()
                ->groupBy('transactionCategory.id')
            as $categoryId => $categoryTransactions
        ) {
            $categoryData = [
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

            if (in_array($categoryData['category']['id'], $basicExpenseCategories)) {
                $totals['basicExpense'] += $categoryData['amount'];
            }
            if (in_array($categoryData['category']['id'], $premiumExpenseCategories)) {
                $totals['premiumExpense'] += $categoryData['amount'];
            }

            $transactionDataByCategories[] = $categoryData;
            $categoryIds[] = $categoryId;
        };

        // Fill array with categories without transactions
        foreach (
            TransactionCategory
                ::active()
                ->whereNotIn('id', $categoryIds)
                ->get()
            as $category
        ) {
            $transactionDataByCategories[] = [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->transaction_type,
                ],
                'amount' => 0,
                'transactions' => []
            ];
        };

        // Getting account balance infos
        $transactionDataByAccounts = [];
        foreach (
            Account
                ::active()
                ->orderBy('id')
                ->get()
            as $account
        ) {
            $totalIncome = $account
                ->transactions()
                ->active()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->whereHas('transactionCategory', function ($query) {
                    $query->where('transaction_type', 'income');
                })
                ->sum('amount');

            $incomeFromTransfers = Transaction
                ::active()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->whereHas('transactionCategory', function ($query) {
                    $query->where('transaction_type', 'transfer');
                })
                ->whereJsonContains('meta->toAccountId', $account->id)
                ->sum('amount');

            $totalExpense = $account
                ->transactions()
                ->active()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->whereHas('transactionCategory', function ($query) {
                    $query
                        ->where('transaction_type', 'expense');
                })
                ->sum('amount');

            $expenseFromTransfers = $account
                ->transactions()
                ->active()
                ->whereYear('date', $request->year)
                ->when($request->filled('month'), function ($query) use ($request) {
                    return $query->whereMonth('date', $request->month);
                })
                ->whereHas('transactionCategory', function ($query) {
                    $query
                        ->where('transaction_type', 'transfer');
                })
                ->sum('amount');

            $profit = $totalIncome - $totalExpense;

            $totals['balance'] += $account->balance;
            $totals['income'] += $totalIncome;
            $totals['expense'] += $totalExpense;
            $totals['profit'] += $profit;

            $totalIncome += $incomeFromTransfers;
            $totalExpense += $expenseFromTransfers;
            $profit = $totalIncome - $totalExpense;
            $transactionDataByAccounts[] = [
                'id' => $account->id,
                'name' => $account->name,
                'balance' => $account->balance,
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'profit' => $profit,
            ];
        };

        // Getting latest transactions
        $latestTransactions = [];
        foreach (
            Transaction
                ::active()
                ->whereHas('transactionCategory', function ($query) {
                    $query->where('transaction_type', '!=', 'transfer');
                })
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->take(12)
                ->get()
            as $transaction
        ) {
            $latestTransactions[] = [
                'transaction' => [
                    'id' => $transaction->id,
                    'type' => $transaction->transactionCategory->transaction_type,
                    'category' => $transaction->transactionCategory->name,
                    'comment' => $transaction->comment,
                    'account' => $transaction->account->name,
                    'date' => $transaction->date,
                ],
                'amount' => $transaction->amount,
            ];
        };

        // Getting diagram data
        $diagramData = [
            'yearlyBalance' => DB::table('spending.actual_balances')
                ->select(['date', 'amount'])
                ->where('date', 'like', "$request->year%")
                ->orderBy('date')
                ->get()
        ];

        return [
            'data' => [
                'totals' => $totals,
                'accounts' => $transactionDataByAccounts,
                'categories' => collect($transactionDataByCategories)->sortBy('category.id')->values()->all(),
                'latestTransactions' => $latestTransactions,
                'diagrams' => $diagramData,
            ]
        ];
    }
}
