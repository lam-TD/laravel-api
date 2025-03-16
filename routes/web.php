<?php

use App\Http\Controllers\SwaggerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/documentation', [SwaggerController::class, 'index'])->name('swagger.index');
Route::get('/api/documentation/spec', [SwaggerController::class, 'spec'])->name('swagger.spec');
