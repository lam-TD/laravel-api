<?php

namespace App\Modules\Product\Exceptions;

use Ltd\Supports\Http\Api\Response\ApiResponse;

final class ProductCreateException extends \Exception
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null) {}

    public function render()
    {
        return ApiResponse::internalServerError('Product creation failed', 500);
    }
}
