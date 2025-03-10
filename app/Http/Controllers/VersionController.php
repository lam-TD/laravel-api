<?php

namespace App\Http\Controllers;

use App\Models\Version;
use Ltd\Supports\Http\Api\Response\ApiResponse;
use Ltd\Supports\Http\Controllers\ResourceController;
use Ltd\Supports\Http\Requests\ResourceCollectionRequest;
use League\Fractal\TransformerAbstract;
use App\Modules\Version\VersionTransformer;
use App\Modules\Version\Store\VersionStoreData;
use App\Modules\Version\Store\VersionStoreAction;
use App\Modules\Version\Store\VersionStoreRequest;
use App\Modules\Version\Update\VersionUpdateRequest;
use App\Modules\Version\Update\VersionUpdateData;
use App\Modules\Version\Update\VersionUpdateAction;
use Ltd\Supports\Http\Requests\ResourceItemRequest;

class VersionController extends ResourceController
{
    protected array $allowedSorts = ['id', 'name', 'importance', 'status'];
    protected array $allowedIncludes = ['product', 'files'];

    public function model(): string
    {
        return Version::class;
    }

    protected function transformer(): TransformerAbstract
    {
        return new VersionTransformer();
    }

    public function index(ResourceCollectionRequest $request)
    {
        $this->request = $request;
        $resource = $this->resourceCollection()->toArray();
        return ApiResponse::ok(...$resource);
    }

    public function show(ResourceItemRequest $request, Version $version)
    {
        $this->request = $request;
        $resource = $this->resourceItem($version->getKey())->toArray();
        return ApiResponse::ok(...$resource);
    }

    public function store(VersionStoreRequest $request)
    {
        $action = new VersionStoreAction();
        $action->execute(VersionStoreData::fromRequest($request));
        return ApiResponse::created();
    }

    public function update(VersionUpdateRequest $request, Version $version)
    {
        $action = new VersionUpdateAction();
        $action->execute(VersionUpdateData::fromRequest($request));   
        return ApiResponse::ok();
    }
}
