<?php

namespace App\Http\Requests\Spending;

use Illuminate\Foundation\Http\FormRequest;

class CategoryGroupUpdateRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'transactionCategoryIds' => ['nullable', 'array'],
            'transactionCategoryIds.*' => ['exists:App\Models\Spending\TransactionCategory,id'],
        ];
    }
}
