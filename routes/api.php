<?php

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

// ── Roles ──────────────────────────────────────────────────────────────────
Route::apiResource('roles', RoleController::class);

// Users in a role
Route::get('roles/{role}/users', [RoleController::class, 'users']);

// ── Users ──────────────────────────────────────────────────────────────────
Route::apiResource('users', UserController::class);

// Role assignment
Route::post  ('users/{user}/roles',              [UserController::class, 'assignRoles']);
Route::put   ('users/{user}/roles',              [UserController::class, 'syncRoles']);
Route::delete('users/{user}/roles/{role}',       [UserController::class, 'removeRole']);
