<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User & Role Management API
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api automatically by Laravel.
|
*/

// All API routes use the web (session) guard since the app uses session-based auth
Route::middleware('auth:web')->group(function () {

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
