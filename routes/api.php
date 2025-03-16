<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VersionController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/products', ProductController::class);
Route::post('/versions/{version}', [VersionController::class, 'update']);
Route::apiResource('/versions', VersionController::class)->except(['update']);
Route::apiResource('/files', FileController::class);
