<?php

namespace App\Http\Controllers\Spending;

use App\Http\Controllers\Controller;
use App\Models\Spending\Transaction;
use App\Http\Resources\Spending\TransactionResource;
use App\Http\Requests\Spending\TransactionCreateRequest;
use App\Http\Requests\Spending\TransactionUpdateRequest;
use App\Models\Spending\Account;
use App\Models\Spending\TransactionCategory;

class TransactionController extends Controller
{
    /**
     * Get all transactions
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TransactionResource::collection(Transaction::all());
    }

    /**
     * Save transaction
     *
     * @param \App\Http\Requests\Spending\TransactionCreateRequest $request
     * @return \App\Http\Resources\Spending\TransactionResource
     */
    public function store(TransactionCreateRequest $request)
    {
        $transaction = Transaction::create([
            'status' => $request->status,
            'date' => $request->date,
            'amount' => $request->amount,
            'comment' => $request->comment,
            'meta' => $request->meta,
        ]);

        $transaction->transactionCategory()->associate(TransactionCategory::find($request->transactionCategoryId));
        $transaction->account()->associate(Account::find($request->accountId));

        $transaction->save();


        return TransactionResource::make($transaction);
    }

    /**
     * Get one transaction
     *
     * @param \App\Models\Spending\Transaction $transaction
     * @return \App\Http\Resources\Spending\TransactionResource
     */
    public function show(Transaction $transaction)
    {
        return TransactionResource::make($transaction);
    }

    /**
     * Update one transaction
     *
     * @param \App\Http\Requests\Spending\TransactionUpdateRequest $request
     * @param \App\Models\Spending\Transaction $transaction
     * @return \App\Http\Resources\Spending\TransactionResource
     */
    public function update(TransactionUpdateRequest $request, Transaction $transaction)
    {
        $transaction->update([
            'status' => $request->status,
            'date' => $request->date,
            'amount' => $request->amount,
            'comment' => $request->comment,
            'meta' => $request->meta,
        ]);

        $transaction->transactionCategory()->associate(TransactionCategory::find($request->transactionCategoryId));
        $transaction->account()->associate(Account::find($request->accountId));

        $transaction->save();

        return TransactionResource::make($transaction);
    }

    /**
     * Delete transaction
     *
     * @param \App\Models\Spending\Transaction $transaction
     * @return \App\Http\Resources\Spending\TransactionResource
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return TransactionResource::make($transaction);
    }
}
