<?php

namespace App\Modules\Product\Update;

use App\Models\Product;
use Illuminate\Http\UploadedFile;

class ProductUpdateData
{
    public function __construct(public readonly Product $product, public ?UploadedFile $logo = null) {}

    public static function fromRequest(ProductUpdateRequest $request): self
    {
        $product = $request->route('product');
        $product->fill($request->validated());

        return new self($product, $request->file('logo'));
    }
}
