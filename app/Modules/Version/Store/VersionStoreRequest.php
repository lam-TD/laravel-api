<?php

namespace App\Modules\Version\Store;

use Illuminate\Foundation\Http\FormRequest;

class VersionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Any preparation logic if needed
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:versions,name',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:0,1',
            'importance' => 'nullable|integer|in:0,1,2,3',
            'product_id' => 'required|exists:products,id',
            'files' => 'required|array',
            'files.*' => 'required|file',
            'files.update_patch' => 'required|file',
            'files.release_note' => 'required|file',
        ];
    }
}
