<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class PurchasedItemUpdateRequest extends FormRequest
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
            'itemId' => ['required', 'integer', 'exists:App\Models\Inventory\Item,id'],
            'packageUnitId' => ['required', 'integer', 'exists:App\Models\Inventory\PackageUnit,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'integer', 'min:1'],
            'purchaseDate' => ['nullable', 'date'],
            'expirationDate' => ['nullable', 'date'],
            'leftoverAmountPercentage' => ['required', 'integer', 'between:0,100'],
        ];
    }
}
