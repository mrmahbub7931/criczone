<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LiveScoreController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// All routes here are already under the 'web' middleware group (see bootstrap/app.php)
// so session-based auth works out of the box.

// Public — no auth required
Route::get('pages/{slug}',              [PageController::class, 'show']);
Route::get('menus/{location}/items',    [MenuController::class, 'publicMenu']);
Route::get('articles/featured',         [ArticleController::class, 'featured']);
Route::get('articles/latest',           [ArticleController::class, 'latest']);
Route::get('articles/trending',         [ArticleController::class, 'trending']);
Route::get('articles/{slug}',           [ArticleController::class, 'showBySlug']);
Route::get('categories/public',         [CategoryController::class, 'publicList']);
Route::get('categories/{slug}/articles',[CategoryController::class, 'categoryArticles']);
Route::get('live-scores',               [LiveScoreController::class, 'publicList']);
Route::get('search',                    [ArticleController::class, 'search']);
Route::post('newsletter/subscribe',     [NewsletterController::class, 'subscribe']);
Route::get('newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe']);

Route::middleware('auth')->group(function () {

    // ── Dashboard overview (admin + editor) ───────────────────────────────
    Route::middleware('role:admin,editor')->get('dashboard', [DashboardController::class, 'index']);

    // ── Image upload (admin + editor) ─────────────────────────────────────
    Route::middleware('role:admin,editor')->group(function () {
        Route::post('upload-image', [UploadController::class, 'image']);
    });

    // ── Site Settings (admin only) ────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get ('settings',              [SettingController::class, 'index']);
        Route::put ('settings',              [SettingController::class, 'update']);
        Route::post('settings/upload-logo',  [SettingController::class, 'uploadLogo']);
    });

    // ── Pages (admin + editor; destroy admin only) ────────────────────────
    Route::middleware('role:admin,editor')->group(function () {
        Route::get   ('pages',        [PageController::class, 'index']);
        Route::post  ('pages',        [PageController::class, 'store']);
        Route::put   ('pages/{page}', [PageController::class, 'update']);
    });
    Route::middleware('role:admin')->delete('pages/{page}', [PageController::class, 'destroy']);

    // ── Articles (admin + editor) ──────────────────────────────────────────
    Route::middleware('role:admin,editor')->group(function () {
        Route::apiResource('articles', ArticleController::class)->except('show');
    });

    // ── Categories (admin + editor) ────────────────────────────────────────
    Route::middleware('role:admin,editor')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except('show');
    });

    // ── Roles ─────────────────────────────────────────────────────────────
    Route::apiResource('roles', RoleController::class);
    Route::get('roles/{role}/users', [RoleController::class, 'users']);

    // ── Users ─────────────────────────────────────────────────────────────
    Route::apiResource('users', UserController::class);
    Route::post  ('users/{user}/roles',        [UserController::class, 'assignRoles']);
    Route::put   ('users/{user}/roles',        [UserController::class, 'syncRoles']);
    Route::delete('users/{user}/roles/{role}', [UserController::class, 'removeRole']);

    // ── SMTP Test ────────────────────────────────────────────────────────
    Route::middleware('role:admin')->post('settings/test-smtp', [NewsletterController::class, 'testSmtp']);

    // ── Newsletter (admin) ────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get   ('admin/newsletter/subscribers',              [NewsletterController::class, 'index']);
        Route::delete('admin/newsletter/subscribers/{subscriber}', [NewsletterController::class, 'destroy']);
        Route::post  ('admin/newsletter/send',                     [NewsletterController::class, 'send']);
    });

    // ── Live Scores (admin) ───────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get   ('admin/live-scores',         [LiveScoreController::class, 'index']);
        Route::post  ('admin/live-scores',         [LiveScoreController::class, 'store']);
        Route::put   ('admin/live-scores/{score}', [LiveScoreController::class, 'update']);
        Route::delete('admin/live-scores/{score}', [LiveScoreController::class, 'destroy']);
    });

    // ── Menus (admin only) ────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get   ('menus',                    [MenuController::class, 'index']);
        Route::post  ('menus',                    [MenuController::class, 'store']);
        Route::put   ('menus/{menu}',             [MenuController::class, 'update']);
        Route::delete('menus/{menu}',             [MenuController::class, 'destroy']);
        Route::get   ('menus/{menu}/list',        [MenuController::class, 'items']);

        Route::post  ('menu-items',               [MenuItemController::class, 'store']);
        Route::put   ('menu-items/{item}',        [MenuItemController::class, 'update']);
        Route::delete('menu-items/{item}',        [MenuItemController::class, 'destroy']);
        Route::post  ('menu-items/reorder',       [MenuItemController::class, 'reorder']);
    });

});
