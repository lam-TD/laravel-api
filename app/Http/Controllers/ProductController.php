<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Ltd\Supports\Http\Api\Response\ApiResponse;
use Ltd\Supports\Http\Controllers\ResourceController;
use Ltd\Supports\Http\Requests\ResourceCollectionRequest;
use League\Fractal\TransformerAbstract;
use App\Modules\Product\ProductTransformer;
use App\Modules\Product\Store\ProductStoreData;
use App\Modules\Product\Store\ProductStoreAction;
use App\Modules\Product\Store\ProductStoreRequest;
use App\Modules\Product\Update\ProductUpdateRequest;
use App\Modules\Product\Update\ProductUpdateData;
use App\Modules\Product\Update\ProductUpdateAction;
use Ltd\Supports\Http\Requests\ResourceItemRequest;

class ProductController extends ResourceController
{

    protected array $allowedSorts = ['id',];

    public function model(): string
    {
        return Product::class;
    }

    protected function transformer(): TransformerAbstract
    {
        return new ProductTransformer();
    }

    public function index(ResourceCollectionRequest $request)
    {
        $this->request = $request;
        $resource = $this->resourceCollection()->toArray();
        return ApiResponse::ok(...$resource);
    }

    public function show(ResourceItemRequest $request, Product $product)
    {
        $this->request = $request;
        $resource = $this->resourceItem($product->getKey())->toArray();
        return ApiResponse::ok(...$resource);
    }

    public function store(ProductStoreRequest $request)
    {
        $action = new ProductStoreAction();
        $action->execute(ProductStoreData::fromRequest($request));
        return ApiResponse::created();
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $action = new ProductUpdateAction();
        $action->execute(ProductUpdateData::fromRequest($request));   
        return ApiResponse::ok();
    }
}
