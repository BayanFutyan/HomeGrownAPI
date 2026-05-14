<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * عرض كل المنشورات مع دعم الفلاتر والبحث والترتيب
     */
    public function index(Request $request)
    {
        $query = Post::with(['user', 'likes', 'comments']);
        
        // ✅ 1. فلترة حسب المناسبة (occasion) - لـ ExploreFilterChips
        if ($request->has('occasion') && $request->occasion && $request->occasion !== 'all') {
            $query->where('occasion', $request->occasion);
        }
        
        // ✅ 2. البحث في محتوى المنشور (Search) - لـ ExploreSearchBar
        if ($request->has('search') && $request->search) {
            $query->where('content', 'like', '%' . $request->search . '%');
        }
        
        // ✅ 3. الترتيب (Sorting)
        $sort = $request->get('sort', 'latest');
        
        switch ($sort) {
            case 'trending':
                $query->orderBy('likes_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $posts = $query->paginate($request->per_page ?? 10);
        
        return PostResource::collection($posts);
    }

    /**
     * عرض منشور محدد
     */
    public function show($id)
    {
        $post = Post::with(['user', 'likes', 'comments.user'])
            ->findOrFail($id);

        return new PostResource($post);
    }

    /**
     * إنشاء منشور جديد (للمستخدم العادي)
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|min:2',
            'occasion' => 'nullable|string|max:255',
        ]);
        
        $post = Post::create([
            'user_id' => $request->user()->id,
            'content' => $request->content,
            'occasion' => $request->occasion,
            'likes_count' => 0,
            'comments_count' => 0,
        ]);
        
        return response()->json([
            'message' => 'Post created successfully',
            'post' => new PostResource($post->load('user'))
        ], 201);
    }

    /**
     * تحديث منشور (لصاحبه فقط)
     */
    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', $request->user()->id)->findOrFail($id);
        
        $request->validate([
            'content' => 'required|string|min:2',
            'occasion' => 'nullable|string|max:255',
        ]);
        
        $post->content = $request->content;
        $post->occasion = $request->occasion;
        $post->save();
        
        return response()->json([
            'message' => 'Post updated successfully',
            'post' => new PostResource($post->load('user'))
        ]);
    }

    /**
     * حذف منشور (لصاحبه فقط)
     */
    public function destroy(Request $request, $id)
    {
        $post = Post::where('user_id', $request->user()->id)->findOrFail($id);
        $post->delete();
        
        return response()->json(['message' => 'Post deleted successfully']);
    }

    /**
     * جلب منشورات مستخدم معين (للبروفايل)
     */
    public function getUserPosts($userId, Request $request)
    {
        $posts = Post::with(['user', 'likes', 'comments'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 10);
        
        return PostResource::collection($posts);
    }

    /**
     * إعجاب بمنشور
     */
    public function like(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $userId = $request->user()->id;

        $existingLike = Like::where('user_id', $userId)
            ->where('likeable_type', 'App\\Models\\Post')
            ->where('likeable_id', $id)
            ->exists();

        if ($existingLike) {
            return response()->json(['message' => 'Already liked'], 400);
        }

        Like::create([
            'user_id' => $userId,
            'likeable_type' => 'App\\Models\\Post',
            'likeable_id' => $id,
        ]);

        $post->increment('likes_count');

        return response()->json([
            'message' => 'Post liked successfully',
            'likes_count' => $post->fresh()->likes_count
        ]);
    }

    /**
     * إلغاء إعجاب بمنشور
     */
    public function unlike(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $userId = $request->user()->id;

        $like = Like::where('user_id', $userId)
            ->where('likeable_type', 'App\\Models\\Post')
            ->where('likeable_id', $id)
            ->first();

        if (!$like) {
            return response()->json(['message' => 'Not liked'], 400);
        }

        $like->delete();
        $post->decrement('likes_count');

        return response()->json([
            'message' => 'Post unliked successfully',
            'likes_count' => $post->fresh()->likes_count
        ]);
    }

    /**
     * إضافة تعليق على منشور
     */
    public function addComment(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $request->validate([
            'comment' => 'required|string|min:2',
            'parent_id' => 'nullable|exists:comments,id',
        ]);
        
        $comment = Comment::create([
            'commentable_type' => 'App\\Models\\Post',
            'commentable_id' => $id,
            'user_id' => $request->user()->id,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id ?? null,
            'likes_count' => 0,
        ]);
        
        $post->increment('comments_count');
        
        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ], 201);
    }

    /**
     * جلب تعليقات منشور
     */
    public function getComments($id)
    {
        $post = Post::findOrFail($id);
        
        $comments = Comment::where('commentable_type', 'App\\Models\\Post')
            ->where('commentable_id', $id)
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(request()->per_page ?? 10);
        
        return response()->json([
            'data' => $comments,
            'message' => 'Comments retrieved successfully'
        ]);
    }

    /**
     * حذف تعليق (لصاحب التعليق فقط)
     */
    public function deleteComment(Request $request, $commentId)
    {
        $comment = Comment::findOrFail($commentId);
        
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        if ($comment->commentable_type === 'App\\Models\\Post') {
            $post = Post::find($comment->commentable_id);
            if ($post) {
                $post->decrement('comments_count');
            }
        }
        
        $comment->delete();
        
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}