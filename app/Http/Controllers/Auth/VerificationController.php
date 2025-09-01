<?php
// app/Http/Controllers/Auth/VerificationController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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

        // Load roles and permissions like in the login endpoint
        $user = $user->loadMissing('roles', 'permissions');

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'valid' => true
        ]);
    }
}
