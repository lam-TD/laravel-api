<?php

namespace App\Modules\Product\Store;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

class ProductStoreData {
  public function __construct(public readonly Product $product, public UploadedFile|null $logo = null)
  {
    
  }

  public static function fromRequest(ProductStoreRequest $request): self
  {
    $request->validated();

    $product = new Product();
    $product->fill([
      'name' => $request->input('name'),
      'status' => $request->input('status' ),
      'logo_color' => $request->input('logo_color'),
      'description' => $request->input('description'),
    ]);

    return new self($product, $request->file('logo'));
  }
}