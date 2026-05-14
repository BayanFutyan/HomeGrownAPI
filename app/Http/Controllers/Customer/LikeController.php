<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Product;
use App\Models\Post;
use App\Helpers\ActivityHelper;  // ✅ أضف هذا
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * إضافة إعجاب
     */
    public function store(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer|exists:products,id',
        ]);
        
        $userId = $request->user()->id;
        $likeableType = $request->likeable_type;
        $likeableId = $request->likeable_id;
        
        // التحقق من وجود الإعجاب مسبقاً
        $existing = Like::where('user_id', $userId)
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->first();
        
        if ($existing) {
            return response()->json(['message' => 'الإعجاب موجود مسبقاً'], 200);
        }
        
        $like = Like::create([
            'user_id' => $userId,
            'likeable_type' => $likeableType,
            'likeable_id' => $likeableId,
        ]);
        
        // تحديث عدد الإعجابات في جدول المنتج
        if ($likeableType === 'App\\Models\\Product') {
            $count = Like::where('likeable_type', $likeableType)
                ->where('likeable_id', $likeableId)
                ->count();
            Product::where('id', $likeableId)->update(['likes_count' => $count]);
            
            // ✅ تسجيل نشاط: إعجاب بمنتج
            $product = Product::find($likeableId);
            if ($product && $product->seller_id != $userId) {
                ActivityHelper::log(
                    $product->seller_id,      // صاحب المنتج
                    $userId,                   // الشخص اللي عمل الإعجاب
                    'like_product',            // نوع النشاط
                    'Product',                 // نوع الهدف
                    $likeableId,               // معرف الهدف
                    $product->name             // عنوان الهدف
                );
            }
        } elseif ($likeableType === 'App\\Models\\Post') {
            // ✅ تسجيل نشاط: إعجاب بمنشور
            $post = Post::find($likeableId);
            if ($post && $post->user_id != $userId) {
                ActivityHelper::log(
                    $post->user_id,            // صاحب المنشور
                    $userId,                   // الشخص اللي عمل الإعجاب
                    'like_post',               // نوع النشاط
                    'Post',                    // نوع الهدف
                    $likeableId,               // معرف الهدف
                    substr($post->content, 0, 50)  // عنوان الهدف (أول 50 حرف)
                );
            }
        }
        
        return response()->json([
            'message' => 'تم الإعجاب بنجاح',
            'like_id' => $like->id
        ], 201);
    }
    
    /**
     * حذف إعجاب
     */
    public function destroy(Request $request, $likeId)
    {
        $like = Like::where('user_id', $request->user()->id)
            ->where('id', $likeId)
            ->firstOrFail();
        
        $likeableType = $like->likeable_type;
        $likeableId = $like->likeable_id;
        
        $like->delete();
        
        // تحديث عدد الإعجابات
        if ($likeableType === 'App\\Models\\Product') {
            $count = Like::where('likeable_type', $likeableType)
                ->where('likeable_id', $likeableId)
                ->count();
            Product::where('id', $likeableId)->update(['likes_count' => $count]);
        }
        
        return response()->json(['message' => 'تم إلغاء الإعجاب بنجاح']);
    }
    
    /**
     * عرض إعجاباتي
     */
    public function myLikes(Request $request)
    {
        $likes = Like::where('user_id', $request->user()->id)
            ->with('likeable')
            ->latest()
            ->paginate($request->per_page ?? 15);
        
        return response()->json($likes);
    }
    
    /**
     * التحقق من الإعجاب
     */
    public function check(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer',
        ]);
        
        $exists = Like::where('user_id', $request->user()->id)
            ->where('likeable_type', $request->likeable_type)
            ->where('likeable_id', $request->likeable_id)
            ->exists();
        
        return response()->json(['is_liked' => $exists]);
    }
}