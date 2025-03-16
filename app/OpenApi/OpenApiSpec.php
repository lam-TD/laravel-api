<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Laravel API Documentation',
    description: 'API documentation for Laravel API'
)]

#[OA\Server(
    url: 'http://localhost',
    description: 'Local Server'
)]

#[OA\PathItem(path: '/api')]

class OpenApiSpec
{
    // This class is used only for OpenAPI annotations
} 