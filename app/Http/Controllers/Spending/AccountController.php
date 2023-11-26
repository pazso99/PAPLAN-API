<?php

namespace App\Http\Controllers\Spending;

use App\Http\Controllers\Controller;
use App\Models\Spending\Account;
use App\Http\Resources\Spending\AccountResource;
use App\Http\Requests\Spending\AccountCreateRequest;
use App\Http\Requests\Spending\AccountUpdateRequest;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    /**
     * Get all accounts
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return AccountResource::collection(Account::all());
    }

    /**
     * Save account
     *
     * @param \App\Http\Requests\Spending\AccountCreateRequest $request
     * @return \App\Http\Resources\Spending\AccountResource
     */
    public function store(AccountCreateRequest $request)
    {
        $account = Account::create([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'balance' => $request->balance,
        ]);

        return AccountResource::make($account);
    }

    /**
     * Get one account
     *
     * @param \App\Models\Spending\Account $account
     * @return \App\Http\Resources\Spending\AccountResource
     */
    public function show(Account $account)
    {
        return AccountResource::make($account);
    }

    /**
     * Update one account
     *
     * @param \App\Http\Requests\Spending\PurchasedItemUpdateRequest $request
     * @param \App\Models\Spending\Account $account
     * @return \App\Http\Resources\Spending\AccountResource
     */
    public function update(AccountUpdateRequest $request, Account $account)
    {
        $account->update([
            'status' => $request->status,
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'balance' => $request->balance,
        ]);

        return AccountResource::make($account);
    }

    /**
     * Delete account
     *
     * @param \App\Models\Spending\Account $account
     * @return \App\Http\Resources\Spending\AccountResource
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return AccountResource::make($account);
    }
}
