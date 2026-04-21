<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/product/{id}', fn ($id) => Inertia::render('ProductShow', [
    'id' => $id,
]))->name('product.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('auth/Login'))->name('login');
});

Route::prefix('admin')->group(function () {
    Route::get('/products', fn () => Inertia::render('admin/ProductsIndex'))->name('admin.products.index');

    Route::get('/products/create', fn () => Inertia::render('admin/ProductForm'))->name('admin.products.create');

    Route::get('/products/{id}/edit', fn ($id) => Inertia::render('admin/ProductForm', [
        'id' => $id,
    ]))->name('admin.products.edit');
});
