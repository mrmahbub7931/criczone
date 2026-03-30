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

// Dashboard (SPA)
Route::prefix('dashboard')->group(function () {
    Route::get('/',            fn() => Inertia::render('Dashboard/Overview'))->name('dashboard');
    Route::get('/articles',    fn() => Inertia::render('Dashboard/Articles'))->name('dashboard.articles');
    Route::get('/categories',  fn() => Inertia::render('Dashboard/Categories'))->name('dashboard.categories');
    Route::get('/live-scores', fn() => Inertia::render('Dashboard/LiveScores'))->name('dashboard.live-scores');
    Route::get('/settings',    fn() => Inertia::render('Dashboard/Settings'))->name('dashboard.settings');
});
