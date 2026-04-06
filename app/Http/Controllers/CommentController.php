<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Add a new comment
     */
    public function store(Request $request): JsonResponse
    {
       // /** @var User|null $user */
        //$user = Auth::user();
         $user = \App\Models\User::find(1);
        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Please login.'], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'comment' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            'product_id' => $request->product_id,
            'user_id' => $user->id,
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
            'likes_count' => 0,
        ]);

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

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}