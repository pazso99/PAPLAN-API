<?php

namespace App\Http\Requests\Notes;

use Illuminate\Foundation\Http\FormRequest;

class NoteCreateRequest extends FormRequest
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
            'dueDate' => ['nullable', 'date'],
            'priority' => ['nullable', 'string', 'in:none,low,medium,high,critical'],
            'description' => ['nullable', 'string'],
        ];
    }
}
