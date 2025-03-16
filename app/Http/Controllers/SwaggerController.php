<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class SwaggerController extends Controller
{
    public function index()
    {
        return view('swagger');
    }

    public function spec()
    {
        $openapi = \OpenApi\Generator::scan([app_path()]);
        // dd($openapi->toJson());
        return response()->json(json_decode($openapi->toJson(), true));
    }
}