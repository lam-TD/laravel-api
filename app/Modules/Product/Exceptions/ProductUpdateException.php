<?php

namespace App\Modules\Product;

use Ltd\Supports\Http\Api\Response\ApiResponse;
 

class ProductUpdateException extends \Exception
{
  public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
  {

  }

  public function render()
  {
    return ApiResponse::internalServerError('Product updation failed', 500);
  }
}