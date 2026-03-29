<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| CricZone - Cricket News Portal
| Each route renders an Inertia page component from resources/js/Pages/
|
*/

// Home page
Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// Category pages (placeholder for future implementation)
Route::get('/category/{slug}', function (string $slug) {
    return Inertia::render('Home', [
        'category' => $slug,
    ]);
})->name('category');
