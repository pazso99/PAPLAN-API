<?php

namespace App\Http\Controllers\Spending;

use App\Http\Controllers\Controller;
use App\Models\Spending\TransactionCategory;
use App\Http\Resources\Spending\TransactionCategoryResource;
use App\Http\Requests\Spending\TransactionCategoryCreateRequest;
use App\Http\Requests\Spending\TransactionCategoryUpdateRequest;
use Illuminate\Support\Str;

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

        return TransactionCategoryResource::make($transactionCategory);
    }
}
