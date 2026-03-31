<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * List all users with their roles.
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->role, fn ($q, $r) => $q->whereHas('roles', fn ($rq) => $rq->where('name', $r)))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json($users);
    }

    /**
     * Create a new user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'status'   => ['sometimes', Rule::in(['active', 'inactive', 'banned'])],
            'avatar'   => ['sometimes', 'nullable', 'string', 'max:500'],
            'roles'    => ['sometimes', 'array'],
            'roles.*'  => ['integer', 'exists:roles,id'],
        ]);

        $user = User::create($data);

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return response()->json($user->load('roles'), 201);
    }

    /**
     * Show a single user with roles.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user->load('roles'));
    }

    /**
     * Update a user's profile and roles.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'nullable', Password::min(8)->letters()->numbers()],
            'status'   => ['sometimes', Rule::in(['active', 'inactive', 'banned'])],
            'avatar'   => ['sometimes', 'nullable', 'string', 'max:500'],
            'roles'    => ['sometimes', 'array'],
            'roles.*'  => ['integer', 'exists:roles,id'],
        ]);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (array_key_exists('roles', $data)) {
            $user->syncRoles($data['roles']);
        }

        return response()->json($user->load('roles'));
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->roles()->detach();
        $user->delete();

        return response()->json(['message' => 'User deleted.']);
    }

    /**
     * Assign roles to a user (additive — does not remove existing).
     */
    public function assignRoles(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'roles'   => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->roles()->syncWithoutDetaching($request->roles);

        return response()->json($user->load('roles'));
    }

    /**
     * Sync roles on a user (replaces existing).
     */
    public function syncRoles(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'roles'   => ['required', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->syncRoles($request->roles);

        return response()->json($user->load('roles'));
    }

    /**
     * Remove a specific role from a user.
     */
    public function removeRole(User $user, Role $role): JsonResponse
    {
        $user->roles()->detach($role);

        return response()->json($user->load('roles'));
    }
}
