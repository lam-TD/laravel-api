<?php

namespace App\Modules\Product\Update;

use App\Modules\Product\ProductRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateLogoRequest extends FormRequest {
  public function authorize()
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    
  }

  public function rules()
  {
    return ProductRule::forUpdateLogo();
  }
}