<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Modules\Version\Store\VersionStoreAction;
use App\Modules\Version\Store\VersionStoreData;
use App\Modules\Version\Store\VersionStoreRequest;
use App\Modules\Version\Update\VersionUpdateAction;
use App\Modules\Version\Update\VersionUpdateData;
use App\Modules\Version\Update\VersionUpdateRequest;
use App\Modules\Version\VersionTransformer;
use League\Fractal\TransformerAbstract;
use Ltd\Supports\Http\Api\Response\ApiResponse;
use Ltd\Supports\Http\Controllers\ResourceController;
use Ltd\Supports\Http\Requests\ResourceCollectionRequest;
use Ltd\Supports\Http\Requests\ResourceItemRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Version',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'status', type: 'integer', enum: [0, 1]),
        new OA\Property(property: 'importance', type: 'integer', enum: [0, 1, 2, 3]),
        new OA\Property(property: 'product_id', type: 'integer'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time')
    ]
)]

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
        return new VersionTransformer;
    }

    #[OA\Get(
        path: '/api/versions',
        summary: 'List all versions',
        description: 'Get a list of all versions with optional filtering and sorting',
        tags: ['Versions']
    )]
    #[OA\Parameter(
        name: 'sort',
        in: 'query',
        description: 'Sort field (id, name, importance, status)',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'include',
        in: 'query',
        description: 'Include related resources (product, files)',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful operation',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Version')),
                new OA\Property(property: 'meta', type: 'object'),
            ]
        )
    )]
    public function index(ResourceCollectionRequest $request)
    {
        $this->request = $request;
        $resource = $this->resourceCollection()->toArray();

        return ApiResponse::ok(...$resource);
    }

    #[OA\Get(
        path: '/api/versions/{version}',
        summary: 'Get a specific version',
        description: 'Get detailed information about a specific version',
        tags: ['Versions']
    )]
    #[OA\Parameter(
        name: 'version',
        in: 'path',
        description: 'Version ID',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'include',
        in: 'query',
        description: 'Include related resources (product, files)',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful operation',
        content: new OA\JsonContent(ref: '#/components/schemas/Version')
    )]
    #[OA\Response(response: 404, description: 'Version not found')]
    public function show(ResourceItemRequest $request, Version $version)
    {
        $this->request = $request;
        $resource = $this->resourceItem($version->getKey())->toArray();

        return ApiResponse::ok(...$resource);
    }

    #[OA\Post(
        path: '/api/versions',
        summary: 'Create a new version',
        description: 'Create a new version with files',
        tags: ['Versions']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['name', 'product_id', 'files'],
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'status', type: 'integer', enum: [0, 1]),
                new OA\Property(property: 'importance', type: 'integer', enum: [0, 1, 2, 3]),
                new OA\Property(property: 'product_id', type: 'integer'),
                new OA\Property(
                    property: 'files',
                    type: 'object',
                    required: ['update_patch', 'release_note'],
                    properties: [
                        new OA\Property(property: 'update_patch', type: 'string', format: 'binary'),
                        new OA\Property(property: 'release_note', type: 'string', format: 'binary')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 201, description: 'Version created successfully')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function store(VersionStoreRequest $request)
    {
        $action = new VersionStoreAction;
        $action->execute(VersionStoreData::fromRequest($request));

        return ApiResponse::created();
    }

    #[OA\Put(
        path: '/api/versions/{version}',
        summary: 'Update a version',
        description: 'Update an existing version and its files',
        tags: ['Versions']
    )]
    #[OA\Parameter(
        name: 'version',
        in: 'path',
        description: 'Version ID',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'status', type: 'integer', enum: [0, 1]),
                new OA\Property(property: 'importance', type: 'integer', enum: [0, 1, 2, 3]),
                new OA\Property(property: 'product_id', type: 'integer'),
                new OA\Property(
                    property: 'files',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'update_patch', type: 'string', format: 'binary'),
                        new OA\Property(property: 'release_note', type: 'string', format: 'binary')
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Version updated successfully')]
    #[OA\Response(response: 404, description: 'Version not found')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function update(VersionUpdateRequest $request, Version $version)
    {
        $action = new VersionUpdateAction;
        $action->execute(VersionUpdateData::fromRequest($request));

        return ApiResponse::ok();
    }
}
