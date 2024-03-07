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

        $transactionCategory = TransactionCategory::find($request->transactionCategoryId);
        $account = Account::find($request->accountId);

        $transaction->transactionCategory()->associate($transactionCategory);
        $transaction->account()->associate($account);

        if ($request->status && $transactionCategory->transaction_type === 'income') {
            $account->balance += $request->amount;
        } else if ($request->status && $transactionCategory->transaction_type === 'expense') {
            $account->balance -= $request->amount;
        }

        if ($request->meta !== '{}') {
            $meta = json_decode($request->meta);

            if ($request->status && $transactionCategory->transaction_type === 'transfer') {
                $toAccount = Account::find($meta->toAccountId);

                $account->balance -= $request->amount;
                $toAccount->balance += $request->amount;

                $toAccount->save();
            }
        }

        $account->save();
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
        // revert current account's balance
        $type = $transaction->transactionCategory->transaction_type;

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
        }

        $transaction->account->save();

        // change updated account's balance
        $transactionCategory = TransactionCategory::find($request->transactionCategoryId);
        $account = Account::find($request->accountId);

        $transaction->transactionCategory()->associate($transactionCategory);
        $transaction->account()->associate($account);

        if ($request->status && $transactionCategory->transaction_type === 'income') {
            $account->balance += $request->amount;
        } else if ($request->status && $transactionCategory->transaction_type === 'expense') {
            $account->balance -= $request->amount;
        }

        if ($request->meta !== '{}') {
            $meta = json_decode($request->meta);

            if ($request->status && $transactionCategory->transaction_type === 'transfer') {
                $toAccount = Account::find($meta->toAccountId);

                $account->balance -= $request->amount;
                $toAccount->balance += $request->amount;

                $toAccount->save();
            }
        }

        $transaction->update([
            'status' => $request->status,
            'date' => $request->date,
            'amount' => $request->amount,
            'comment' => $request->comment,
            'meta' => $request->meta,
        ]);

        $account->save();
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
        // revert account's balance
        $type = $transaction->transactionCategory->transaction_type;

        if ($transaction->status && $type === 'expense') {
            $transaction->account->balance += $transaction->amount;
        } else if ($transaction->status && $type === 'income') {
            $transaction->account->balance -= $transaction->amount;
        }

        if ($transaction->meta !== '{}') {
            $meta = json_decode($transaction->meta);

            if ($transaction->status && $type === 'transfer') {
                $toAccount = Account::find($meta->toAccountId);

                $transaction->account->balance += $transaction->amount;
                $toAccount->balance -= $transaction->amount;

                $toAccount->save();
            }
        }

        $transaction->account->save();
        $transaction->delete();

        return TransactionResource::make($transaction);
    }
}
