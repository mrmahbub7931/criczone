<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Maintenance page (always accessible)
Route::get('/maintenance', fn () => response()->view('maintenance'))->name('maintenance');

// Public
Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/pages/{slug}', fn (string $slug) => Inertia::render('Pages/Show', ['slug' => $slug]))->name('page.show');

Route::get('/article/{slug}', fn (string $slug) => Inertia::render('Article/Show', ['slug' => $slug]))->name('article.show');
Route::get('/category/{slug}', fn (string $slug) => Inertia::render('Category/Show', ['slug' => $slug]))->name('category');
Route::get('/live-scores',     fn () => Inertia::render('LiveScores'))->name('live-scores');
Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/search',          fn () => Inertia::render('Search'))->name('search');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard — requires login + admin or editor role
Route::prefix('dashboard')->middleware(['auth', 'role:admin,editor'])->group(function () {
    Route::get('/',            fn () => Inertia::render('Dashboard/Overview'))->name('dashboard');
    Route::get('/articles',    fn () => Inertia::render('Dashboard/Articles/Index'))->name('dashboard.articles');
    Route::get('/pages',       fn () => Inertia::render('Dashboard/Pages/Index'))->name('dashboard.pages');
    Route::get('/categories',  fn () => Inertia::render('Dashboard/Categories'))->name('dashboard.categories');
    Route::get('/live-scores', fn () => Inertia::render('Dashboard/LiveScores'))->name('dashboard.live-scores');
    Route::get('/settings',    fn () => Inertia::render('Dashboard/Settings'))->name('dashboard.settings');
    Route::get('/menus',       fn () => Inertia::render('Dashboard/Menus/Index'))->name('dashboard.menus');

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', fn () => Inertia::render('Dashboard/Users/Index'))->name('dashboard.users');
        Route::get('/roles', fn () => Inertia::render('Dashboard/Roles/Index'))->name('dashboard.roles');
    });
});
