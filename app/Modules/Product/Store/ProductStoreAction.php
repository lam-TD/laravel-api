<?php

namespace App\Modules\Product\Store;

use App\Models\Product;
use App\Modules\Product\Exceptions\ProductCreateException;

class ProductStoreAction
{
    public function execute(ProductStoreData $data): Product
    {
        $logo = $data->logo;
        $product = $data->product;
        // store the logo if it exists
        if ($logo) {
            if ($logo->storeAs('logos', $logo->hashName())) {
                $product->logo = $logo->hashName();
            } else {
                throw new ProductCreateException('Failed to store the logo.');
            }
        }

        $product->saveOrFail();

        return $data->product;
    }
}
