<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SwaggerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/documentation', [SwaggerController::class, 'index'])->name('swagger.index');
Route::get('/api/documentation/spec', [SwaggerController::class, 'spec'])->name('swagger.spec');
