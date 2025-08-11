<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Make sure this is imported
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /** List all roles */
    public function index()
    {
        // Consider adding authorization:
        // $this->authorize('view roles'); // Or check if user is admin: if (!auth()->user()->hasRole('admin')) abort(403);
        $roles = Role::withCount('permissions')->orderBy('name')->get();
        return response()->json(['data' => $roles]);
    }

    /**
     * List all available permissions.
     * GET /api/permissions
     */
    public function getAllPermissions() // This method was missing or incomplete
    {
        // Consider adding authorization:
        // $this->authorize('view permissions'); // Or check if user is admin
        // if (!auth()->user()->hasRole('admin') && !auth()->user()->can('manage roles')) { // Example check
        //     abort(403, 'You do not have permission to view all permissions.');
        // }

        $permissions = Permission::orderBy('name')->get(['id', 'name']); // Get only id and name
        return response()->json(['data' => $permissions]);
    }

    /** Store a new role */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {
            $role = Role::create(['name' => $request->input('name')]);
            $permissionNames = $request->input('permissions', []);
            if (!empty($permissionNames)) {
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->syncPermissions($permissions);
            }
            return response()->json(['data' => $role->load('permissions:id,name')], 201);
        });
    }

    /** Display a specific role with its permissions */
    public function show(Role $role)
    {
        // $this->authorize('view roles');
        // ... (rest of show method as before, ensure 'permissions:id,name' is loaded)
        return response()->json(['data' => $role->load('permissions:id,name')]);
    }

    /** Update an existing role */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes','required','string','max:255', Rule::unique('roles','name')->ignore($role->id)],
            'permissions' => 'sometimes|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request, $role) {
            if ($request->filled('name')) {
                $role->name = $request->input('name');
                $role->save();
            }

            if ($request->has('permissions')) {
                $permissionNames = $request->input('permissions', []);
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->syncPermissions($permissions); // Sync to match payload
            }

            return response()->json(['data' => $role->load('permissions:id,name')]);
        });
    }

    /** Delete a role */
    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }
}