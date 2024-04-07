<?php

namespace App\Http\Requests\Spending;

use Illuminate\Foundation\Http\FormRequest;

class MonthMetadataCalculateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'year' => ['required'],
            'month' => ['required'],
        ];
    }
}
