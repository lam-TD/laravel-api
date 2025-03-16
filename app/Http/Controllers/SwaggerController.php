<?php

namespace App\Http\Controllers;

class SwaggerController extends Controller
{
    public function index()
    {
        return view('swagger');
    }

    public function spec()
    {
        $openapi = \OpenApi\Generator::scan([app_path()]);

        return response()->json(json_decode($openapi->toJson(), true));
    }
}
