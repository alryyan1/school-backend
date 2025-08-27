<?php
// app/Http/Controllers/Auth/VerificationController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    /**
     * Verify the authenticated user's token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => true,
                'user' => null,
                'valid' => false,
            ], 200);
        }

        $spatieRoles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();
        $isAdmin = $spatieRoles && $spatieRoles->contains('admin');
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $isAdmin ? 'admin' : null,
                'permissions' => method_exists($user, 'getAllPermissions') ? $user->getAllPermissions()->pluck('name') : [],
            ],
            'valid' => true
        ]);
    }
}
