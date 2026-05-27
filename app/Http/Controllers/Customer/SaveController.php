<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Save;
use App\Models\Post;
use App\Models\Product;
use App\Helpers\ActivityHelper;
use Illuminate\Http\Request;

class SaveController extends Controller
{
    /**
     * حفظ عنصر (منشور أو منتج)
     */
    public function store(Request $request)
    {
        $request->validate([
            'saveable_type' => 'required|string|in:App\\Models\\Post,App\\Models\\Product',
            'saveable_id' => 'required|integer',
        ]);

        $userId = $request->user()->id;
        $saveableType = $request->saveable_type;
        $saveableId = $request->saveable_id;

        // التحقق من وجود الحفظ مسبقاً
        $existing = Save::where('user_id', $userId)
            ->where('saveable_type', $saveableType)
            ->where('saveable_id', $saveableId)
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'Already saved'], 400);
        }

        $save = Save::create([
            'user_id' => $userId,
            'saveable_type' => $saveableType,
            'saveable_id' => $saveableId,
        ]);

        // ✅ تسجيل نشاط الحفظ
        $targetUserId = null;
        $targetTitle = null;
        $activityType = null;
        $targetType = null;

        if ($saveableType === 'App\\Models\\Product') {
            $product = Product::find($saveableId);
            if ($product && $product->seller_id != $userId) {
                $targetUserId = $product->seller_id;
                $targetTitle = $product->name;
                $activityType = 'save_product';
                $targetType = 'Product';
            }
        } elseif ($saveableType === 'App\\Models\\Post') {
            $post = Post::find($saveableId);
            if ($post && $post->user_id != $userId) {
                $targetUserId = $post->user_id;
                $targetTitle = substr($post->content, 0, 50);
                $activityType = 'save_post';
                $targetType = 'Post';
            }
        }

        if ($targetUserId && $activityType) {
            ActivityHelper::log(
                $targetUserId,       // صاحب المحتوى
                $userId,             // الشخص اللي حفظ
                $activityType,       // نوع النشاط (save_post / save_product)
                $targetType,         // نوع الهدف
                $saveableId,         // معرف الهدف
                $targetTitle         // عنوان الهدف
            );
        }

        return response()->json([
            'message' => 'Saved successfully',
            'save_id' => $save->id
        ], 201);
    }

    /**
     * إلغاء حفظ عنصر
     */
    public function destroy(Request $request, $saveId)
    {
        $save = Save::where('user_id', $request->user()->id)
            ->where('id', $saveId)
            ->firstOrFail();

        $save->delete();

        return response()->json(['message' => 'Unsaved successfully']);
    }

    /**
     * عرض قائمة المحفوظات للمستخدم
     */
    public function mySaves(Request $request)
    {
        $saves = Save::where('user_id', $request->user()->id)
            ->with('saveable')
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json($saves);
    }

    /**
     * التحقق من حالة الحفظ لعنصر معين
     */
    public function check(Request $request)
    {
        $request->validate([
            'saveable_type' => 'required|string',
            'saveable_id' => 'required|integer',
        ]);

        $save = Save::where('user_id', $request->user()->id)
            ->where('saveable_type', $request->saveable_type)
            ->where('saveable_id', $request->saveable_id)
            ->first();

        return response()->json([
            'is_saved' => $save ? true : false,
            'save_id' => $save?->id,
        ]);
    }

    /**
     * جلب عدد مرات حفظ عنصر معين
     */
    public function count(Request $request)
    {
        $request->validate([
            'saveable_type' => 'required|string',
            'saveable_id' => 'required|integer',
        ]);

        $count = Save::where('saveable_type', $request->saveable_type)
            ->where('saveable_id', $request->saveable_id)
            ->count();

        return response()->json(['saves_count' => $count]);
    }
}
