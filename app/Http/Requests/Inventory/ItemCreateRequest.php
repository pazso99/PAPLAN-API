<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ItemCreateRequest extends FormRequest
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
            'itemTypeId' => ['required', 'integer', 'exists:App\Models\Inventory\ItemType,id'],
            'packageUnitIds' => ['nullable', 'array'],
            'packageUnitIds.*' => ['exists:App\Models\Inventory\PackageUnit,id'],
            'expectedLifetimeInDays' => ['nullable', 'integer', 'min:0'],
            'recommendedStock' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
