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
use Illuminate\Support\Facades\Log;
use App\Models\Rating;

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

        
            $averageRating = Rating::where('artisan_id', $user->id)->avg('rating');
            $ratingsCount = Rating::where('artisan_id', $user->id)->count();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'bio' => $user->bio,
                'profile_image' => $user->profile_image
                    ? url('/' . $user->profile_image)
                    : null,
                'role' => $user->role,
                'followers_count' => $user->followers()->count(),  // 🔥 جديدة
                'following_count' => $user->following()->count(),
                'average_rating' => round($averageRating ?? 0, 1),
                'ratings_count' => $ratingsCount, // 🔥 جديدة
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
    'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
    'phone' => 'nullable|string|max:20',
    'address' => 'nullable|string|max:500',
    'bio' => 'nullable|string|max:1000',
    'profile_image' => 'nullable|string',
]);

        $updateData = [];

        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        if ($request->has('email')) {
    $updateData['email'] = $request->email;
}

        if ($request->has('phone')) {
            $updateData['phone'] = $request->phone;
        }

        if ($request->has('address')) {
            $updateData['address'] = $request->address;
        }

        if ($request->has('bio')) {
            $updateData['bio'] = $request->bio;
        }

        // ✅ معالجة الصورة (Base64)
// ✅ معالجة الصورة (Base64)
if ($request->has('profile_image') && $request->profile_image) {
    try {
        $imageData = $request->profile_image;

        // استخراج نوع الصورة من Base64
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]);

            // التحقق من نوع الصورة
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return response()->json(['message' => 'Invalid image type'], 400);
            }

            // فك تشفير Base64
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                return response()->json(['message' => 'Invalid base64 data'], 400);
            }

            // إنشاء اسم فريد داخل public/images/avatars
            $imageName = 'avatars/' . time() . '_' . uniqid() . '.' . $type;

            $fullPath = public_path('images/' . $imageName);

            // إنشاء المجلد إذا لم يكن موجود
            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // حفظ الصورة
            file_put_contents($fullPath, $imageData);

            // حفظ المسار في الداتابيس
            $updateData['profile_image'] = 'images/' . $imageName;

            // حذف الصورة القديمة إذا وجدت
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                @unlink(public_path($user->profile_image));
            }

        } else {
            return response()->json(['message' => 'Invalid image format'], 400);
        }

    } catch (\Exception $e) {
        Log::error('Image upload error: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to upload image: ' . $e->getMessage()], 500);
    }
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
                'bio' => $user->bio,
                'profile_image' => $user->profile_image ? url($user->profile_image) : null,
                'role' => $user->role,
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
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

    public function searchArtisans(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $query = User::where('role', UserRoleEnum::ARTISAN);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('bio', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        $artisans = $query->limit(10)->get()->map(function ($artisan) {
            return [
                'id' => $artisan->id,
                'name' => $artisan->name,
                'email' => $artisan->email,
                'phone' => $artisan->phone,
                'address' => $artisan->address,
                'bio' => $artisan->bio,
                'role' => $artisan->role,
                'profile_image' => $artisan->profile_image
                    ? url('/' . $artisan->profile_image)
                    : null,
                'followers_count' => $artisan->followers()->count(),
                'following_count' => $artisan->following()->count(),
                'created_at' => $artisan->created_at,
                'updated_at' => $artisan->updated_at,
            ];
        });

        return response()->json([
            'data' => $artisans,
            'message' => 'Artisans retrieved successfully',
        ]);
    }

    /**
     * عرض بيانات حرفي معين (للمستخدم العادي)
     */
    /**
     * عرض بيانات حرفي معين
     */
    public function getArtisanProfile($id): JsonResponse
    {
        // ✅ استخدم 'artisan' بدل UserRoleEnum::ARTISAN
        $artisan = User::where('role', 'artisan')->findOrFail($id);

        $averageRating = Rating::where('artisan_id', $id)->avg('rating');
        $ratingsCount = Rating::where('artisan_id', $id)->count();

        return response()->json([
            'data' => [
                'id' => $artisan->id,
                'name' => $artisan->name,
                'email' => $artisan->email,
                'phone' => $artisan->phone,
                'profile_image' => $artisan->profile_image
                    ? url('/' . $artisan->profile_image)
                    : null,
                'address' => $artisan->address,
                'bio' => $artisan->bio,
                'role' => $artisan->role,
                'followers_count' => $artisan->followers()->count(),
                'following_count' => $artisan->following()->count(),
                'average_rating' => round($averageRating ?? 0, 1),
                'ratings_count' => $ratingsCount,
                'created_at' => $artisan->created_at,
                'updated_at' => $artisan->updated_at,
            ],
            'message' => 'Artisan profile retrieved successfully'
        ]);
    }

    /**
     * تقييم حرفي
     */
    public function rateArtisan(Request $request, $id): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $userId = $request->user()->id;

        if ($userId == $id) {
            return response()->json(['message' => 'You cannot rate yourself'], 403);
        }

        // ✅ استخدم 'artisan' بدل UserRoleEnum::ARTISAN
        $artisan = User::where('role', 'artisan')->findOrFail($id);

        $rating = Rating::updateOrCreate(
            ['user_id' => $userId, 'artisan_id' => $id],
            ['rating' => $request->rating]
        );

        return response()->json([
            'message' => 'Rating saved successfully',
            'data' => $rating,
        ]);
    }

    /**
     * جلب تقييم المستخدم الحالي لحرفي معين
     */
    public function getMyRatingForArtisan($id, Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // ✅ استخدم Rating بدل Follower
        $rating = Rating::where('user_id', $userId)
            ->where('artisan_id', $id)
            ->first();

        return response()->json([
            'rating' => $rating?->rating,
        ]);
    }

    /**
     * جلب منتجات حرفي معين
     */
    public function getArtisanProducts($id, Request $request): JsonResponse
    {
        // ✅ استخدم 'artisan' بدل UserRoleEnum::ARTISAN
        $artisan = User::where('role', 'artisan')->findOrFail($id);

        $products = \App\Models\Product::where('seller_id', $artisan->id)
            ->with(['seller', 'offer'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }

    /**
     * جلب منشورات حرفي معين
     */
    public function getArtisanPosts($id, Request $request): JsonResponse
    {
        // ✅ استخدم 'artisan' بدل UserRoleEnum::ARTISAN
        $artisan = User::where('role', 'artisan')->findOrFail($id);

        $posts = \App\Models\Post::where('user_id', $artisan->id)
            ->with(['user', 'likes', 'comments'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $posts,
            'message' => 'Posts retrieved successfully'
        ]);
    }

    /**
     * متابعة حرفي
     */
    public function followArtisan($id, Request $request): JsonResponse
    {
        $user = $request->user();
        $artisan = User::where('role', UserRoleEnum::ARTISAN)->findOrFail($id);

        // منع متابعة النفس
        if ($user->id == $artisan->id) {
            return response()->json(['message' => 'Cannot follow yourself'], 400);
        }

        // التحقق إذا كان يتابع بالفعل
        $existing = \App\Models\Follower::where('follower_id', $user->id)
            ->where('following_id', $artisan->id)
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'Already following'], 400);
        }

        \App\Models\Follower::create([
            'follower_id' => $user->id,
            'following_id' => $artisan->id,
        ]);

        // ✅ تسجيل النشاط
        \App\Models\Activity::create([
            'user_id' => $artisan->id,
            'actor_id' => $user->id,
            'type' => 'follow',
            'target_type' => 'User',
            'target_id' => $artisan->id,
            'target_title' => $artisan->name,
        ]);

        return response()->json(['message' => 'Followed successfully']);
    }

    /**
     * إلغاء متابعة حرفي
     */
    public function unfollowArtisan($id, Request $request): JsonResponse
    {
        $user = $request->user();
        $artisan = User::where('role', UserRoleEnum::ARTISAN)->findOrFail($id);

        $follower = \App\Models\Follower::where('follower_id', $user->id)
            ->where('following_id', $artisan->id)
            ->first();

        if (!$follower) {
            return response()->json(['message' => 'Not following'], 400);
        }

        $follower->delete();

        return response()->json(['message' => 'Unfollowed successfully']);
    }

    /**
     * التحقق من حالة المتابعة لحرفي
     */
    public function checkFollowArtisan($id, Request $request): JsonResponse
    {
        $user = $request->user();

        $isFollowing = \App\Models\Follower::where('follower_id', $user->id)
            ->where('following_id', $id)
            ->exists();

        return response()->json([
            'is_following' => $isFollowing
        ]);
    }
}
