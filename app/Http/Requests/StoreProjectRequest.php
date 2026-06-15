<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date'],
        ];

        // Only enforce the ordering when a start date was actually provided.
        if ($this->filled('start_date')) {
            $rules['deadline'][] = 'after_or_equal:start_date';
        }

        return $rules;
    }
}
