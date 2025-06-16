<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // Import Role model
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of all available roles.
     * GET /api/roles
     */
    public function index()
    {
        // Ensure only admins can fetch all roles
        $this->authorize('manage users'); // Or a specific 'view roles' permission for admin

        $roles = Role::orderBy('name')->get(['id', 'name']); // Get only id and name
        return response()->json(['data' => $roles]);
    }
}