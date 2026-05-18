<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ActivityHelper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    /**
     * Get all followers of a user
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', Auth::id());

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $followers = $user->followers()->get();

        return response()->json([
            'data' => $followers,
            'message' => 'Followers retrieved successfully'
        ]);
    }

    /**
     * Follow a user
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $request->validate([
            'following_id' => 'required|exists:users,id|different:user_id',
        ]);

        $followingId = $request->following_id;

        // Check if already following
        if ($user->following()->where('following_id', $followingId)->exists()) {
            return response()->json(['message' => 'Already following this user'], 400);
        }

        // ✅ حذف 'rating' لأنه موجود الآن في جدول منفصل
        $user->following()->attach($followingId);

        // ✅ تسجيل نشاط المتابعة
        $followedUser = User::find($followingId);
        if ($followedUser && $followedUser->id != $user->id) {
            ActivityHelper::log(
                $followedUser->id,     // الشخص اللي تمت متابعته
                $user->id,             // الشخص اللي قام بالمتابعة
                'follow',              // نوع النشاط
                'User',                // نوع الهدف
                $followingId,          // معرف الهدف
                $followedUser->name    // عنوان الهدف (اسم المستخدم)
            );
        }

        return response()->json([
            'message' => 'User followed successfully'
        ], 201);
    }

    /**
     * Unfollow a user
     */
    public function destroy($id): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        // Check if following
        if (!$user->following()->where('following_id', $id)->exists()) {
            return response()->json(['message' => 'Not following this user'], 400);
        }

        $user->following()->detach($id);

        return response()->json([
            'message' => 'User unfollowed successfully'
        ]);
    }
}