<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Category pages
Route::get('/category/{slug}', function (string $slug) {
    return Inertia::render('Home', [
        'category' => $slug,
    ]);
})->name('category');
