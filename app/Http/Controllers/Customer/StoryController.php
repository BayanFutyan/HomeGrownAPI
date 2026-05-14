<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    /**
     * جلب كل القصص النشطة للمستخدم
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $stories = Story::with('user')
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($story) use ($userId) {
                return [
                    'id' => $story->id,
                    'user_id' => $story->user_id,
                    'user_name' => $story->user->name,
                    'user_image' => $story->user->profile_image,
                    'image' => $story->image,
                    'caption' => $story->caption,
                    'is_viewed' => $story->isViewedByUser($userId),
                    'views_count' => $story->views_count,
                    'created_at' => $story->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'data' => $stories,
            'message' => 'Stories retrieved successfully'
        ]);
    }

    /**
     * عرض قصة محددة
     */
    public function show($id, Request $request)
    {
        $story = Story::with('user')
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->findOrFail($id);
        
        return response()->json([
            'data' => [
                'id' => $story->id,
                'user_id' => $story->user_id,
                'user_name' => $story->user->name,
                'user_image' => $story->user->profile_image,
                'image' => $story->image,
                'caption' => $story->caption,
                'created_at' => $story->created_at->diffForHumans(),
            ],
            'message' => 'Story retrieved successfully'
        ]);
    }

    /**
     * تسجيل مشاهدة قصة
     */
    public function view(Request $request, $id)
    {
        $story = Story::findOrFail($id);
        $userId = $request->user()->id;
        
        // تأكد إن القصة لسا فعالة
        if ($story->expires_at->isPast()) {
            return response()->json(['message' => 'Story has expired'], 400);
        }
        
        // تأكد إن المستخدم ما شاف القصة قبل كذا
        $existingView = StoryView::where('story_id', $id)
            ->where('viewer_id', $userId)
            ->exists();
        
        if (!$existingView) {
            StoryView::create([
                'story_id' => $id,
                'viewer_id' => $userId,
                'viewed_at' => now(),
            ]);
        }
        
        return response()->json([
            'message' => 'Story viewed successfully',
            'views_count' => $story->views()->count()
        ]);
    }
}