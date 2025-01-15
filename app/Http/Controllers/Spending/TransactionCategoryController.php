<?php

namespace App\Http\Controllers\Spending;

use App\Http\Controllers\Controller;
use App\Models\Spending\Account;
use App\Models\Spending\TransactionCategory;
use App\Http\Resources\Spending\TransactionCategoryResource;
use App\Http\Requests\Spending\TransactionCategoryCreateRequest;
use App\Http\Requests\Spending\TransactionCategoryUpdateRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionCategoryController extends Controller
{
    /**
     * Get all transaction categories
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TransactionCategoryResource::collection(TransactionCategory::all());
    }

    /**
     * Save transaction category
     *
     * @param \App\Http\Requests\Spending\TransactionCategoryCreateRequest $request
     * @return \App\Http\Resources\Spending\TransactionCategoryResource
     */
    public function store(TransactionCategoryCreateRequest $request)
    {
        $transactionCategory = TransactionCategory::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'transaction_type' => $request->transactionType,
        ]);

        return TransactionCategoryResource::make($transactionCategory);
    }

    /**
     * Get one transaction category
     *
     * @param \App\Models\Spending\TransactionCategory $transactionCategory
     * @return \App\Http\Resources\Spending\TransactionCategoryResource
     */
    public function show(TransactionCategory $transactionCategory)
    {
        return TransactionCategoryResource::make($transactionCategory);
    }

    /**
     * Update one transaction category
     *
     * @param \App\Http\Requests\Spending\TransactionCategoryUpdateRequest $request
     * @param \App\Models\Spending\TransactionCategory $transactionCategory
     * @return \App\Http\Resources\Spending\TransactionCategoryResource
     */
    public function update(TransactionCategoryUpdateRequest $request, TransactionCategory $transactionCategory)
    {
        // if status switched to false or type changed, then update all transactions with true status to false
        if (($transactionCategory->status && !$request->status) || ($transactionCategory->transaction_type !== $request->transactionType)) {
            foreach ($transactionCategory->transactions as $transaction) {
                $type = $transactionCategory->transaction_type;
                if ($transaction->status) {
                    if ($type === 'income') {
                        $transaction->account->balance -= $transaction->amount;
                    } else if ($type === 'expense') {
                        $transaction->account->balance += $transaction->amount;
                    }

                    if ($transaction->meta !== '{}') {
                        $meta = json_decode($transaction->meta);

                        if ($type === 'transfer') {
                            $toAccount = Account::find($meta->toAccountId);

                            $transaction->account->balance += $transaction->amount;
                            $toAccount->balance -= $transaction->amount;

                            $toAccount->save();
                        }
                    }

                    $transaction->account->save();
                    $transaction->update([
                        'status' => false,
                    ]);
                }
            }
        }

        $transactionCategory->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'transaction_type' => $request->transactionType,
        ]);


        return TransactionCategoryResource::make($transactionCategory);
    }

    /**
     * Delete transaction category
     *
     * @param \App\Models\Spending\TransactionCategory $transactionCategory
     * @return \App\Http\Resources\Spending\TransactionCategoryResource
     */
    public function destroy(TransactionCategory $transactionCategory)
    {
        $transactionCategory->delete();

        $this->removeCategoryIdFromConfig('spending_basic_transaction_categories', $transactionCategory->id);
        $this->removeCategoryIdFromConfig('spending_premium_transaction_categories', $transactionCategory->id);

        return TransactionCategoryResource::make($transactionCategory);
    }

    private function removeCategoryIdFromConfig(string $configKey, int $categoryId)
    {
        $configValue = DB::table('config')->where('key', $configKey)->first()->value;
        $categoryIds = json_decode($configValue, true);

        $updatedCategoryIds = array_filter($categoryIds, fn($id) => $id !== $categoryId);

        DB::table('config')->where('key', $configKey)->update(['value' => json_encode(array_values($updatedCategoryIds))]);
    }
}
