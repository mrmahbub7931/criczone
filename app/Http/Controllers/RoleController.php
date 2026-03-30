<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * List all roles with user counts.
     */
    public function index(): JsonResponse
    {
        $roles = Role::withCount('users')->orderBy('name')->get();

        return response()->json($roles);
    }

    /**
     * Create a new role.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:50', 'unique:roles,name', 'regex:/^[a-z0-9_\-]+$/'],
            'display_name' => ['required', 'string', 'max:100'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $role = Role::create($data);

        return response()->json($role, 201);
    }

    /**
     * Show a single role with its users.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json($role->load('users'));
    }

    /**
     * Update a role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $data = $request->validate([
            'name'         => ['sometimes', 'string', 'max:50', Rule::unique('roles')->ignore($role->id), 'regex:/^[a-z0-9_\-]+$/'],
            'display_name' => ['sometimes', 'string', 'max:100'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $role->update($data);

        return response()->json($role);
    }

    /**
     * Delete a role (detaches users automatically via cascade).
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->users()->detach();
        $role->delete();

        return response()->json(['message' => 'Role deleted.']);
    }

    /**
     * List all users that belong to this role.
     */
    public function users(Role $role): JsonResponse
    {
        $users = $role->users()->select('users.id', 'name', 'email', 'status', 'avatar')->get();

        return response()->json($users);
    }
}
