<?php

namespace Ltd\Supports\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fields' => 'nullable|array',
            'include' => 'nullable|string',
        ];
    }
}
