<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Post;
use App\Helpers\ActivityHelper;  // ✅ أضف هذا
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Services\FirebaseNotificationService;

class CommentController extends Controller
{
    /**
     * Add a new comment
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'post_id' => 'nullable|exists:posts,id',
            'comment' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // تحديد نوع التعليق (على منتج أو منشور)
        $commentableType = null;
        $commentableId = null;
        $targetTitle = null;

        if ($request->has('product_id') && $request->product_id) {
            $commentableType = 'App\\Models\\Product';
            $commentableId = $request->product_id;
            $product = Product::find($commentableId);
            $targetTitle = $product?->name;
        } elseif ($request->has('post_id') && $request->post_id) {
            $commentableType = 'App\\Models\\Post';
            $commentableId = $request->post_id;
            $post = Post::find($commentableId);
            $targetTitle = $post ? substr($post->content, 0, 50) : null;
        } else {
            return response()->json(['message' => 'Either product_id or post_id is required'], 422);
        }

        $comment = Comment::create([
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
            'likes_count' => 0,
        ]);


        if ($request->parent_id) {
    $parentComment = Comment::with('user')->find($request->parent_id);

    if ($parentComment && $parentComment->user_id != $user->id) {
        $product = null;

        if ($commentableType === 'App\\Models\\Product') {
            $product = Product::find($commentableId);
        }

        $title = '';
        $body = $user->name . ' replied to your comment on ' . ($product?->name ?? 'a product');

        $data = [
            'type' => 'product_comment_reply',
            'comment_id' => $comment->id,
            'parent_id' => $parentComment->id,
            'product_id' => $product?->id,
            'seller_id' => $user->id,
            'click_action' => 'product_page',
        ];

        Notification::create([
            'user_id' => $parentComment->user_id,
            'title' => $title,
            'body' => $body,
            'type' => 'product_comment_reply',
            'data' => $data,
            'is_read' => false,
        ]);

        $tokens = $parentComment->user?->fcmTokens()->pluck('token')->toArray() ?? [];

        if (!empty($tokens)) {
            $firebaseService = new FirebaseNotificationService();
            $firebaseService->send($tokens, $title, $body, $data);
        }
    }
}
        // ✅ تسجيل نشاط التعليق
        $targetUserId = null;
        $activityType = null;

        if ($commentableType === 'App\\Models\\Product') {
            $product = Product::find($commentableId);
            if ($product && $product->seller_id != $user->id) {
                $targetUserId = $product->seller_id;
                $activityType = 'comment_product';
            }
        } elseif ($commentableType === 'App\\Models\\Post') {
            $post = Post::find($commentableId);
            if ($post && $post->user_id != $user->id) {
                $targetUserId = $post->user_id;
                $activityType = 'comment_post';
            }
        }

        if ($targetUserId && $activityType) {
            ActivityHelper::log(
                $targetUserId,      // صاحب المحتوى
                $user->id,          // الشخص اللي كتب التعليق
                $activityType,      // نوع النشاط
                $commentableType === 'App\\Models\\Product' ? 'Product' : 'Post',
                $commentableId,
                $targetTitle
            );
        }

        return response()->json([
            'data' => $comment->load('user'),
            'message' => 'Comment added successfully'
        ], 201);
    }

    /**
     * Delete a comment
     */
    public function destroy($id): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'You can only delete your own comments'], 403);
        }

        // ✅ قبل الحذف، نقص عدد التعليقات إذا كان على منشور
        if ($comment->commentable_type === 'App\\Models\\Post') {
            $post = Post::find($comment->commentable_id);
            if ($post) {
                $post->decrement('comments_count');
            }
        } elseif ($comment->commentable_type === 'App\\Models\\Product') {
            // إذا كان على منتج، نقدر ننقص Product comments_count إذا وجد
            // Product::where('id', $comment->commentable_id)->decrement('comments_count');
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Toggle like on a comment
     */
    public function toggleLike($id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Please login first'], 401);
        }
        
        $comment = Comment::find($id);
        
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }
        
        $isLiked = false;
        
        // التبديل بين 0 و 1
        if ($comment->likes_count == 1) {
            $comment->update(['likes_count' => 0]);
            $isLiked = false;
        } else {
            $comment->update(['likes_count' => 1]);
            $isLiked = true;
            
            // ✅ تسجيل نشاط الإعجاب بالتعليق
            $targetUserId = null;
            $activityType = null;
            
            if ($comment->user_id != $user->id) {
                $targetUserId = $comment->user_id;
                $activityType = 'like_comment';
                
                ActivityHelper::log(
                    $targetUserId,
                    $user->id,
                    $activityType,
                    'Comment',
                    $comment->id,
                    substr($comment->comment, 0, 50)
                );
            }
        }
        
        return response()->json([
            'message' => $isLiked ? 'Like added' : 'Like removed',
            'likes_count' => $comment->likes_count,
            'is_liked' => $isLiked
        ]);
    }
}