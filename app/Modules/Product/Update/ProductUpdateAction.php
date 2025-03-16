<?php

namespace App\Modules\Product\Update;

use App\Models\Product;

class ProductUpdateAction
{
    public function execute(ProductUpdateData $data): Product
    {
        $product = $data->product;
        $data->product->saveOrFail();

        return $product;
    }
}
