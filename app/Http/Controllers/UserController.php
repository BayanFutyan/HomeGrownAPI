<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'bio' => 'nullable|string|max:1000',      // ✅ أضف هذا
            'profile_image' => 'nullable|string',
            'role' => ['required', Rule::in(UserRoleEnum::values())],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),  // ✅ استخدم bcrypt لتشفير كلمة المرور
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'bio' => $validated['bio'] ?? null,           // ✅ أضف هذا
            'profile_image' => $validated['profile_image'] ?? null,
            'role' => $validated['role'],
        ]);

        $token = $user->createToken('homegrown_token')->plainTextToken;

        return response()->json([
            'message' => 'Registered successfully',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $token = $user->createToken('homegrown_token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user profile
     */
public function profile(): JsonResponse
{
    /** @var User|null $user */
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized. Please login.'], 401);
    }

    return response()->json([
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'bio' => $user->bio,
            // ✅ أصلح مسار الصورة - أزل /storage/
            'profile_image' => $user->profile_image 
                ? url('/' . $user->profile_image)  // ✅ بدون /storage/
                : null,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ],
        'message' => 'Profile retrieved successfully'
    ]);
}

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'bio' => 'nullable|string|max:1000',           // ✅ أضف هذا
            'profile_image' => 'nullable|string',
        ]);

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->has('phone')) {
            $updateData['phone'] = $request->phone;
        }

        if ($request->has('address')) {
            $updateData['address'] = $request->address;
        }

        if ($request->has('bio')) {                        // ✅ أضف هذا
            $updateData['bio'] = $request->bio;
        }

        if ($request->has('profile_image')) {
            $updateData['profile_image'] = $request->profile_image;
        }

        $user->update($updateData);
        $user->refresh();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'bio' => $user->bio,                       // ✅ أضف هذا
'profile_image' => $user->profile_image ? url('/' . $user->profile_image) : null,                'role' => $user->role,
            ],
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Get user's followers
     */
    public function followers(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $followers = $user->followers()->get();

        return response()->json([
            'data' => $followers,
            'message' => 'Followers retrieved successfully'
        ]);
    }

    /**
     * Get users that this user is following
     */
    public function following(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $following = $user->following()->get();

        return response()->json([
            'data' => $following,
            'message' => 'Following retrieved successfully'
        ]);
    }
}