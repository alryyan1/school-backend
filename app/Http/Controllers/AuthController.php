<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required', // Changed from 'email' to 'username'
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation errors', 'errors' => $validator->errors()], 422);
        }

        // Check if the username and password match in the database
        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user(); // Get the authenticated User instance
        $user = Auth::user()->loadMissing('roles', 'permissions'); // Eager load Spatie roles and permissions

        $token = $user->createToken('auth_token')->plainTextToken;

        // Compute token expiration based on Sanctum config (minutes) if set
        $expirationMinutes = config('sanctum.expiration');
        $expiresAt = $expirationMinutes ? now()->addMinutes($expirationMinutes)->toIso8601String() : null;

        return response()->json([
            'user'=>new UserResource($user->loadMissing('roles', 'permissions')),
            'token' => $token,
            'token_type' => 'Bearer',
            'token_expires_at' => $expiresAt,
            'user_type' => null,
        ]);
    }

}
