<?php

namespace App\Http\Requests\Spending;

use Illuminate\Foundation\Http\FormRequest;

class SettingsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'configs' => ['required'],
            'actualBalances' => ['required'],
        ];
    }
}
