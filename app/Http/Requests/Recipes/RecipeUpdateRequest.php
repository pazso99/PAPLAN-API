<?php

namespace App\Http\Requests\Recipes;

use Illuminate\Foundation\Http\FormRequest;

class RecipeUpdateRequest extends FormRequest
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
            'time' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}
