<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\FileController;


Route::apiResource('/products', ProductController::class);
Route::post('/versions/{version}', [VersionController::class, 'update']);
Route::apiResource('/versions', VersionController::class)->except(['update']);
Route::apiResource('/files', FileController::class);
