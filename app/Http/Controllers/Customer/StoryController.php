<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    private function imageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return str_replace('/storage/images/', '/images/', $path);
        }

        $path = ltrim($path, '/');
        $path = str_replace('storage/images/', 'images/', $path);

        return url($path);
    }

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
                    'user_name' => $story->user?->name ?? 'User',
                    'user_image' => $this->imageUrl($story->user?->profile_image),
                    'image' => $this->imageUrl($story->image),
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
                'user_name' => $story->user?->name ?? 'User',
                'user_image' => $this->imageUrl($story->user?->profile_image),
                'image' => $this->imageUrl($story->image),
                'caption' => $story->caption,
                'created_at' => $story->created_at->diffForHumans(),
            ],
            'message' => 'Story retrieved successfully'
        ]);
    }

    public function view(Request $request, $id)
    {
        $story = Story::findOrFail($id);
        $userId = $request->user()->id;

        if ($story->expires_at->isPast()) {
            return response()->json(['message' => 'Story has expired'], 400);
        }

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