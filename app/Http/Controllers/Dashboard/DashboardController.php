<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Dashboard\SpendingDataRequest;
use App\Models\Inventory\ItemType;
use App\Models\Spending\Account;
use App\Models\Spending\Transaction;
use App\Models\Spending\TransactionCategory;
use App\Models\Recipes\Recipe;
use App\Models\Notes\Note;

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
        $categoryIdsWithTransaction = [];
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
                'id' => $categoryId,
                'name' => $categoryTransactions->first()->transactionCategory->name,
                'type' => $categoryTransactions->first()->transactionCategory->transaction_type,
                'sumTransactionAmount' => $categoryTransactions->sum('amount'),
                // 'transactions' => $categoryTransactions->map(function ($transaction) { // kell ez ide később talán
                //     return [
                //         'id' => $transaction->id,
                //         'date' => $transaction->date,
                //         'amount' => $transaction->amount,
                //         'comment' => $transaction->comment,
                //         'account' => $transaction->account->name,
                //         'meta' => $transaction->meta,
                //     ];
                // })->toArray()
            ];

            if (in_array($categoryData['id'], $basicExpenseCategories)) {
                $totals['basicExpense'] += $categoryData['sumTransactionAmount'];
            }
            if (in_array($categoryData['id'], $premiumExpenseCategories)) {
                $totals['premiumExpense'] += $categoryData['sumTransactionAmount'];
            }

            $transactionDataByCategories[] = $categoryData;
            $categoryIdsWithTransaction[] = $categoryId;
        };

        // Fill array with categories without transactions
        foreach (
            TransactionCategory
                ::active()
                ->whereNotIn('id', $categoryIdsWithTransaction)
                ->get()
            as $category
        ) {
            $transactionDataByCategories[] = [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->transaction_type,
                'sumTransactionAmount' => 0,
                // 'transactions' => []
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
                'id' => $transaction->id,
                'date' => $transaction->date,
                'amount' => $transaction->amount,
                'transactionCategory' => [
                    'id' => $transaction->transactionCategory->id,
                    'name' => $transaction->transactionCategory->name,
                    'transactionType' => $transaction->transactionCategory->transaction_type,
                ],
                'account' => [
                    'id' => $transaction->account->id,
                    'name' => $transaction->account->name,
                    'balance' => $transaction->account->balance,
                ],
                'comment' => $transaction->comment,
                'meta' => $transaction->meta,
            ];
        };

        // Getting diagram data
        $diagramData = [
            'yearlyBalance' => DB::table('spending.monthly_metadata')
                ->select([DB::Raw("CONCAT(year, '-', month) AS date"), 'total_balance AS amount'])
                ->where('year', '=', $request->year)
                ->orderBy('date')
                ->get()
        ];

        return [
            'data' => [
                'totals' => $totals,
                'accounts' => $transactionDataByAccounts,
                'categories' => collect($transactionDataByCategories)->sortBy('id')->values()->all(),
                'latestTransactions' => $latestTransactions,
                'diagrams' => $diagramData,
            ]
        ];
    }

    public function getRecipesData()
    {
        return [
            'data' => [
                'recipes' => Recipe::active()->get([
                    'id',
                    'name',
                    'time',
                    'description'
                ]),
            ]
        ];
    }

    public function getNotesData()
    {
        return [
            'data' => [
                'notes' => Note::active()->select(
                    'id',
                    'name',
                    'due_date AS dueDate',
                    'priority',
                    'description'
                )
                ->orderByRaw("
                    CASE
                        WHEN priority = 'critical' THEN 1
                        WHEN priority = 'high' THEN 2
                        WHEN priority = 'medium' THEN 3
                        WHEN priority = 'low' THEN 4
                        WHEN priority = 'none' THEN 5
                        ELSE 6
                    END
                ")
                ->get(),
            ]
        ];
    }

    public function getInventoryData()
    {
        $inventoryItemTypes = [];

        foreach (ItemType::active()->get() as $itemType) {
            $items = [];
            $outOfStockNumber = 0;
            $inStockNumber = 0;

            foreach ($itemType->items()->active()->get() as $item) {
                $inStockItems = $this->getStructuredPurchasedItems(
                    $item->purchasedItems()
                        ->active()
                        ->inStock()
                        ->orderBy('expiration_date')
                        ->get()
                );
                $usedItems = $this->getStructuredPurchasedItems(
                    $item->purchasedItems()
                        ->active()
                        ->outStock()
                        ->get()
                );

                $inStockCount = count($inStockItems);
                $usedItemsCount = count($usedItems);
                $stockStatus = $inStockCount > 0 ? 'in_stock' : 'out';
                $expectedRunOutDate = $inStockItems[0]['expirationDate'] ?? null;
                $ranOutDate = null;
                $isEssential = $item->is_essential;

                if (
                    $stockStatus === 'in_stock' &&
                    $item->recommended_stock &&
                    $inStockCount < $item->recommended_stock
                ) {
                    $stockStatus = 'running_out';
                }

                if (
                    $stockStatus === 'out' &&
                    $usedItemsCount > 0
                ) {
                    $ranOutDate = $usedItems[0]['expirationDate'];
                }

                if ($isEssential && $stockStatus === 'out') {
                    $outOfStockNumber++;
                }
                if ($stockStatus !== 'out') {
                    $inStockNumber++;
                }

                $items[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'recommendedStock' => $item->recommended_stock,
                    'isEssential' => $isEssential,
                    'stockStatus' => $stockStatus,
                    'ranOutDate' => $ranOutDate,
                    'expectedRunOutDate' => $expectedRunOutDate,
                    'inStockItems' => $inStockItems,
                    'usedItems' => $usedItems,
                ];
            }

            $inventoryItemTypes[] = [
                'id' => $itemType->id,
                'name' => $itemType->name,
                'items' => $this->sortItems($items),
                'outOfStockNumber' => $outOfStockNumber,
                'inStockNumber' => $inStockNumber,
            ];
        }

        return [
            'data' => [
                'inventoryItemTypes' => $inventoryItemTypes,
            ],
        ];
    }

    private function getStructuredPurchasedItems($purchasedItems): array
    {
        $structuredItems = [];
        foreach ($purchasedItems as $purchasedItem) {
            $structuredItems[] = [
                'id' => $purchasedItem->id,
                'amount' => $purchasedItem->amount,
                'packageUnit' => $purchasedItem->packageUnit->name,
                'leftoverAmountPercentage' => $purchasedItem->leftover_amount_percentage,
                'price' => $purchasedItem->price,
                'purchaseDate' => $purchasedItem->purchase_date,
                'expirationDate' => $purchasedItem->expiration_date,
                'comment' => $purchasedItem->comment,
            ];
        }

        return $structuredItems;
    }

    private function sortItems($items): array
    {
        $items = collect($items);

        $sortedItems = $items->sortBy(function ($item) {
            if ($item['isEssential']) {
                switch ($item['stockStatus']) {
                    case 'out':
                        return 1;
                    case 'running_out':
                        return 2;
                    case 'in_stock':
                        return 3;
                }
            } else {
                switch ($item['stockStatus']) {
                    case 'out':
                        return 4;
                    case 'running_out':
                        return 5;
                    case 'in_stock':
                        return 6;
                }
            }
        });

        return $sortedItems->values()->all();
    }
}
