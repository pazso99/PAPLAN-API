<?php

namespace App\Http\Requests\Spending;

use Illuminate\Foundation\Http\FormRequest;

class TransactionUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => ['required', 'boolean'],
            'date' => ['required', 'date'],
            'amount' => ['required', 'integer', 'min:0'],
            'accountId' => ['required', 'integer', 'exists:App\Models\Spending\Account,id'],
            'transactionCategoryId' => ['required', 'integer', 'exists:App\Models\Spending\TransactionCategory,id'],
            'comment' => ['nullable', 'string'],
            'meta' => ['nullable', 'json'],
        ];
    }
}
