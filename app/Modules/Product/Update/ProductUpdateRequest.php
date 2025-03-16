<?php

namespace App\Modules\Product\Update;

use App\Modules\Product\ProductRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:products,name,'.$this->route('product')->id,
            'status' => 'nullable|integer|in:0,1',
            'logo_color' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ] + ProductRule::forLogo();
    }
}
