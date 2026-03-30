<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// All routes here are already under the 'web' middleware group (see bootstrap/app.php)
// so session-based auth works out of the box.

Route::middleware('auth')->group(function () {

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

});
