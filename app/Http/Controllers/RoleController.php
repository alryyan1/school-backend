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
        // $this->authorize('create roles');
        // ... (rest of store method as before)
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
        // $this->authorize('edit roles');
        // ... (rest of update method as before)
    }

    /** Delete a role */
    public function destroy(Role $role)
    {
        // $this->authorize('delete roles');
        // ... (rest of destroy method as before)
    }
}