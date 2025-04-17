<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource; // Import UserResource
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Use AuthorizesRequests trait

class UserController extends Controller
{
    use AuthorizesRequests; // Enable authorization methods like $this->authorize()

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class); // Policy check
        $users = User::latest()->paginate(20); // Paginate users
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     * Handles registration AND admin creation.
     */
    public function store(Request $request)
    {
        // Allow admins to create any user, public registration might have different validation/defaults
        if (auth()->check()) { // If called by an authenticated user (admin)
            $this->authorize('create', User::class);
            $roleValidation = ['required', Rule::in(['admin', 'teacher', 'student', 'parent'])];
            $defaultRole = null; // Admin must specify role
        } else { // Public registration
            $roleValidation = ['sometimes', Rule::in(['student', 'parent'])]; // Limit roles for public registration
            $defaultRole = 'student'; // Default role for public registration
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()], // Requires password_confirmation field
            'role' => $roleValidation,
            'phone' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['male', 'female', 'ذكر', 'انثي'])], // Allow multiple gender values from different sources
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $user = User::create([
            'name' => $validatedData['name'],
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'] ?? $defaultRole, // Assign role or default
            'phone' => $validatedData['phone'] ?? null,
            'gender' => $validatedData['gender'] ?? null,
        ]);

        // For public registration, maybe log them in or send verification email?
        // For admin creation, just return the user

        return new UserResource($user); // 201 implicit
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user) // Route model binding
    {
        $this->authorize('view', $user);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     * Does NOT update the password here.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', 'required', Rule::in(['admin', 'teacher', 'student', 'parent'])],
            'phone' => 'nullable|string|max:20',
            'gender' => ['nullable', Rule::in(['male', 'female', 'ذكر', 'انثي'])],
            // DO NOT validate password here
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $user->update($validator->validated());

        return new UserResource($user->fresh());
    }

    /**
     * Update the password for the specified user.
     */
    public function updatePassword(Request $request, User $user)
    {
        // Use the same 'update' permission or a specific 'updatePassword' permission
        $this->authorize('update', $user); // Or $this->authorize('updatePassword', $user);

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::defaults()], // Requires password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق من كلمة المرور', 'errors' => $validator->errors()], 422);
        }

        // Update only the password
        $user->update([
            'password' => Hash::make($validator->validated()['password']),
        ]);

        return response()->json(['message' => 'تم تحديث كلمة المرور بنجاح.']);
    }


    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Prevent deleting the last admin? Or oneself? Add checks if needed.
        // if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
        //     return response()->json(['message' => 'لا يمكن حذف آخر مسؤول.'], 403);
        // }
        // if ($user->id === auth()->id()) {
        //     return response()->json(['message' => 'لا يمكنك حذف حسابك بنفسك.'], 403);
        // }

        $user->delete(); // Soft delete

        return response()->json(['message' => 'تم حذف المستخدم بنجاح.'], 200);
    }

    // You might already have a register method for public registration?
    // Ensure validation/logic aligns with the store method or separate them.
    // public function register(Request $request) { ... use store() or custom logic ... }
}
