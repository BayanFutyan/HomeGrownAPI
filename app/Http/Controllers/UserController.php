<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get authenticated user profile
     */
    public function profile(): JsonResponse
    {

        $user = \App\Models\User::find(1);
        // /** @var User|null $user */
        // $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        return response()->json([
            'data' => $user,
            'message' => 'Profile retrieved successfully'
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        // /** @var User|null $user */
        // $user = Auth::user();
        $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
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

        if ($request->has('profile_image')) {
            $updateData['profile_image'] = $request->profile_image;
        }

        $user->update($updateData);
        $user->refresh();

        return response()->json([
            'data' => $user,
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Get user's followers
     */
    public function followers(): JsonResponse
    {
        // /** @var User|null $user */
        // $user = Auth::user();
        $user = \App\Models\User::find(1);
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
        // /** @var User|null $user */
        // $user = Auth::user();
        $user = \App\Models\User::find(1);
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
