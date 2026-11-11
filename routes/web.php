<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Maintenance page (always accessible)
Route::get('/maintenance', fn () => response()->view('maintenance'))->name('maintenance');

// Developer documentation — admin only
Route::get('/dev/docs', fn () => Inertia::render('DevDocs'))->middleware(['auth', 'role:admin'])->name('dev.docs');

// Short URL redirect
Route::get('/s/{code}', [ShortUrlController::class, 'redirect'])->name('short-url');

// Public
Route::get('/', fn () => Inertia::render('Home'))->name('home');
Route::get('/pages/{slug}', fn (string $slug) => Inertia::render('Pages/Show', ['slug' => $slug]))->name('page.show');

Route::get('/article/{slug}', [PageController::class, 'article'])->name('article.show');
Route::get('/category/{slug}', fn (string $slug) => Inertia::render('Category/Show', ['slug' => $slug]))->name('category');
Route::get('/tag/{slug}',      fn (string $slug) => Inertia::render('Tag/Show', ['slug' => $slug]))->name('tag');
Route::get('/live-scores',     fn () => Inertia::render('LiveScores'))->name('live-scores');
Route::get('/videos',          fn () => Inertia::render('Videos'))->name('videos');
Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/search',          fn () => Inertia::render('Search'))->name('search');
Route::get('/categories',      fn () => Inertia::render('Categories/Index'))->name('categories');
Route::get('/photo-of-day',             fn () => Inertia::render('PhotoOfDay/Index'))->name('photo-of-day');
Route::get('/photo-of-day/{slug}',      fn (string $slug) => Inertia::render('PhotoOfDay/Show', ['slug' => $slug]))->name('photo-of-day.show');
Route::get('/games',           fn () => Inertia::render('Games/Index'))->name('games');
Route::get('/games/super-over', fn () => Inertia::render('Games/SuperOver'))->name('games.super-over');

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
    Route::get('/videos',      fn () => Inertia::render('Dashboard/Videos/Index'))->name('dashboard.videos');
    Route::get('/galleries',   fn () => Inertia::render('Dashboard/Galleries/Index'))->name('dashboard.galleries');
    Route::get('/menus',       fn () => Inertia::render('Dashboard/Menus/Index'))->name('dashboard.menus');

    Route::get('/promotions',   fn () => Inertia::render('Dashboard/Promotions'))->name('dashboard.promotions');
    Route::get('/translations', fn () => Inertia::render('Dashboard/Translations'))->name('dashboard.translations');

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('/tags',  fn () => Inertia::render('Dashboard/Tags/Index'))->name('dashboard.tags');
        Route::get('/users', fn () => Inertia::render('Dashboard/Users/Index'))->name('dashboard.users');
        Route::get('/roles', fn () => Inertia::render('Dashboard/Roles/Index'))->name('dashboard.roles');
    });
});
