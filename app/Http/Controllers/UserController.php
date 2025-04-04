<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Get all users with Arabic support
    public function index(Request $request)
    {
        $users = User::when($request->search, function($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%')
                      ->orWhere('email', 'like', '%'.$request->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'تم جلب البيانات بنجاح',
            'data' => $users
        ]);
    }

    // Create new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'role' => ['required', Rule::in(['admin', 'teacher', 'student', 'parent'])],
            // 'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'حقل الاسم مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            // 'role.in' => 'دور المستخدم غير صالح'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'تم إنشاء المستخدم بنجاح',
            'data' => $user
        ], 201);
    }

    // Get single user
    public function show(User $user)
    {
        return response()->json([
            'message' => 'تم جلب بيانات المستخدم',
            'data' => $user
        ]);
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => ['sometimes', Rule::in(['admin', 'teacher', 'student', 'parent'])],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'تم تحديث بيانات المستخدم',
            'data' => $user
        ]);
    }

    // Delete user (soft delete)
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'تم حذف المستخدم بنجاح'
        ]);
    }

}
