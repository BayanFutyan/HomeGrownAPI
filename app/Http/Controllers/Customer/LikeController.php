<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Product;
use App\Models\Post;
use App\Helpers\ActivityHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\User;
use App\Services\FirebaseNotificationService;

class LikeController extends Controller
{
    /**
     * إضافة إعجاب
     */
    public function store(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer',
        ]);
        
        $userId = $request->user()->id;
        $likeableType = $request->likeable_type;
        $likeableId = $request->likeable_id;
        
        // ✅ تحويل القيمة إلى الصيغة الصحيحة
        if ($likeableType === 'product') {
            $likeableType = 'App\\Models\\Product';
        } elseif ($likeableType === 'post') {
            $likeableType = 'App\\Models\\Post';
        }
        
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
        
        // ✅ تحديث عدد الإعجابات في جدول المنتج
        if ($likeableType === 'App\\Models\\Product') {
            $product = Product::find($likeableId);
            if ($product) {
                $newCount = $product->likes()->count();
                $product->likes_count = $newCount;
                $product->save();
                
                Log::info('Product likes updated', [
                    'product_id' => $likeableId,
                    'new_count' => $newCount
                ]);
            }
            
            // ✅ تسجيل نشاط
            $product = Product::find($likeableId);
            if ($product && $product->seller_id != $userId) {
                ActivityHelper::log(
                    $product->seller_id,
                    $userId,
                    'like_product',
                    'Product',
                    $likeableId,
                    $product->name
                );
            }

            if ($product && $product->seller_id != $userId) {

    $actor = User::find($userId);
    $seller = User::find($product->seller_id);

    if (
        $actor &&
        $seller &&
        $actor->role?->value === 'user' &&
        $seller->role?->value === 'artisan'
    ) {
        $title = '';
        $body = $actor->name . ' liked your product ' . $product->name;

        $data = [
            'type' => 'product_like',
            'product_id' => $product->id,
            'seller_id' => $seller->id,
            'actor_id' => $actor->id,
            'click_action' => 'product_page',
        ];

        Notification::create([
            'user_id' => $seller->id,
            'title' => $title,
            'body' => $body,
            'type' => 'product_like',
            'data' => $data,
            'is_read' => false,
        ]);

        $tokens = $seller->fcmTokens()->pluck('token')->toArray();

        if (!empty($tokens)) {
            $firebaseService = new FirebaseNotificationService();
            $firebaseService->send($tokens, $title, $body, $data);
        }
    }
}
        } elseif ($likeableType === 'App\\Models\\Post') {
            $post = Post::find($likeableId);
            if ($post && $post->user_id != $userId) {
                ActivityHelper::log(
                    $post->user_id,
                    $userId,
                    'like_post',
                    'Post',
                    $likeableId,
                    substr($post->content, 0, 50)
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
    public function destroy(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer',
        ]);

        $likeableType = $request->likeable_type;
        $likeableId = $request->likeable_id;
        
        // ✅ تحويل القيمة إلى الصيغة الصحيحة للبحث
        if ($likeableType === 'product') {
            $likeableType = 'App\\Models\\Product';
        } elseif ($likeableType === 'post') {
            $likeableType = 'App\\Models\\Post';
        }

        $like = Like::where('user_id', $request->user()->id)
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->firstOrFail();

        $like->delete();

        // ✅ تحديث عدد الإعجابات
        if ($likeableType === 'App\\Models\\Product') {
            $product = Product::find($likeableId);
            if ($product) {
                $newCount = $product->likes()->count();
                $product->likes_count = $newCount;
                $product->save();
            }
        }

        return response()->json(['message' => 'تم إلغاء الإعجاب بنجاح']);
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
        
        $likeableType = $request->likeable_type;
        
        // ✅ تحويل القيمة إلى الصيغة الصحيحة للبحث
        if ($likeableType === 'product') {
            $likeableType = 'App\\Models\\Product';
        } elseif ($likeableType === 'post') {
            $likeableType = 'App\\Models\\Post';
        }
        
        $like = Like::where('user_id', $request->user()->id)
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $request->likeable_id)
            ->first();
        
        return response()->json([
            'is_liked' => $like !== null,
            'like_id' => $like?->id
        ]);
    }
}