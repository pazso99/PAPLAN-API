<?php

namespace App\Http\Requests\Spending;

use Illuminate\Foundation\Http\FormRequest;

class MonthMetadataUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'integer', 'exists:App\Models\Spending\MonthlyMetadata,id'],
            'year' => ['required', 'string'],
            'month' => ['required', 'string'],
            'totalBalance' => ['required', 'integer'],
            'totalIncome' => ['nullable', 'integer'],
            'totalBasicExpense' => ['nullable', 'integer'],
            'totalPremiumExpense' => ['nullable', 'integer'],
            'accounts' => ['required', 'array'],
            'accounts.*.id' => ['exists:App\Models\Spending\MonthlyMetadataAccount,id'],
            'accounts.*.balance' => ['required', 'integer'],
            'accounts.*.income' => ['nullable', 'integer'],
            'accounts.*.basicExpense' => ['nullable', 'integer'],
            'accounts.*.premiumExpense' => ['nullable', 'integer'],
        ];
    }
}
