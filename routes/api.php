<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

// Без авторизации

Route::get('products', [ProductController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

Route::post('login', [AuthController::class, 'token']);

// С авторизацией

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class)
        ->except(['index', 'show']);
});
