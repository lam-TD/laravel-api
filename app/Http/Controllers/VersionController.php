<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Modules\Version\Domain\DTOs\VersionData;
use App\Modules\Version\Domain\Services\VersionService;
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
        new OA\Property(property: 'order', type: 'integer'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time')
    ]
)]
class VersionController extends ResourceController
{
    protected array $allowedSorts = ['id', 'name', 'importance', 'status', 'order'];
    protected array $allowedIncludes = ['product', 'files'];

    public function __construct(private readonly VersionService $versionService)
    {
        parent::__construct();
    }

    public function model(): string
    {
        return Version::class;
    }

    protected function transformer(): TransformerAbstract
    {
        return new VersionTransformer();
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
        description: 'Sort field (id, name, importance, status, order)',
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
            required: ['name', 'product_id'],
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string', nullable: true),
                new OA\Property(property: 'status', type: 'integer', enum: [0, 1]),
                new OA\Property(property: 'importance', type: 'integer', enum: [0, 1, 2, 3]),
                new OA\Property(property: 'product_id', type: 'integer'),
                new OA\Property(property: 'order', type: 'integer', nullable: true),
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
    #[OA\Response(response: 201, description: 'Version created successfully')]
    #[OA\Response(response: 422, description: 'Validation error')]
    public function store(ResourceItemRequest $request)
    {
        $versionData = VersionData::fromRequest($request);
        $version = $this->versionService->createVersion($versionData);
        
        $this->request = $request;
        $resource = $this->resourceItem($version->getKey())->toArray();
        return ApiResponse::created(...$resource);
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
                new OA\Property(property: 'order', type: 'integer', nullable: true),
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
    public function update(ResourceItemRequest $request, Version $version)
    {
        $versionData = VersionData::fromRequest($request);
        $version = $this->versionService->updateVersion($version, $versionData);
        
        $this->request = $request;
        $resource = $this->resourceItem($version->getKey())->toArray();
        return ApiResponse::ok(...$resource);
    }

    #[OA\Delete(
        path: '/api/versions/{version}',
        summary: 'Delete a version',
        description: 'Delete a version and its associated files',
        tags: ['Versions']
    )]
    #[OA\Parameter(
        name: 'version',
        in: 'path',
        description: 'Version ID',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(response: 200, description: 'Version deleted successfully')]
    #[OA\Response(response: 404, description: 'Version not found')]
    public function destroy(Version $version)
    {
        $this->versionService->deleteVersion($version);
        return ApiResponse::ok();
    }
}
