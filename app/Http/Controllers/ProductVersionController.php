<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Modules\Version\VersionStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductVersionController extends Controller
{
    public function index(Request $request, Product $product)
    {
        $storage = VersionStorage::fromProduct($product)
            ->getByName($request->query('from'))
            ->toStreamDownload('abc.zip', function () {
                // write log
                Log::info('Download version');
            });
        dd($storage);

    }
}
