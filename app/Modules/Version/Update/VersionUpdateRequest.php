<?php

namespace App\Modules\Version\Update;

use Illuminate\Foundation\Http\FormRequest;

class VersionUpdateRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255|unique:versions,name,'.$this->route('version')->id,
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:0,1',
            'importance' => 'nullable|integer|in:0,1,2,3',
            'product_id' => 'sometimes|required|exists:products,id',
            'files' => 'nullable|array',
            'files.*' => 'nullable|file',
            'files.update_patch' => 'nullable|file',
            'files.release_note' => 'nullable|file',
        ];
    }
}
