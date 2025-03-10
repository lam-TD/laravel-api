<?php

namespace App\Modules\Product\Store;

use App\Modules\Product\ProductRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest {
  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    if(! $this->input('logo_color')) {
      $this->merge([
        'logo_color' => fake()->hexColor,
      ]);
    }
  }

  public function rules()
  {
    return [
      'name' => 'required|string|max:255|unique:products,name',
      'status' => 'nullable|integer|in:0,1',
      'logo_color' => 'nullable|string|max:255',
      'description' => 'nullable|string|max:255',
    ] + ProductRule::forLogo();
  }
}