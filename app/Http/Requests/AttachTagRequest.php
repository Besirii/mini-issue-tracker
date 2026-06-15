<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachTagRequest extends FormRequest
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
        return [
            'tag_id' => ['required', 'integer', 'exists:tags,id'],
        ];
    }
}
