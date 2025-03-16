<?php

namespace App\Modules\Product;

use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform($product)
    {
        return $product->toArray();
    }
}
