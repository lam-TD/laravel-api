<?php
namespace Ltd\Supports\Http\Api\Response;

use Illuminate\Support\Facades\Facade;

final class ApiResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ApiResponse';
    }

}