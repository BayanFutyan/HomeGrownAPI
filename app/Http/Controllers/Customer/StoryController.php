<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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


    public function store(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        'caption' => 'nullable|string|max:255',
    ]);

    $file = $request->file('image');
    $fileName = 'story_' . time() . '.' . $file->getClientOriginalExtension();

    $file->move(public_path('images/stories'), $fileName);

    $story = Story::create([
        'user_id' => $request->user()->id,
        'image' => 'images/stories/' . $fileName,
        'caption' => $request->caption,
        'expires_at' => now()->addDay(),
        'is_active' => true,
    ]);

    return response()->json([
        'data' => [
            'id' => $story->id,
            'user_id' => $story->user_id,
            'user_name' => $request->user()->name,
            'user_image' => $this->imageUrl($request->user()->profile_image),
            'image' => $this->imageUrl($story->image),
            'caption' => $story->caption,
            'is_viewed' => false,
            'views_count' => 0,
            'created_at' => $story->created_at->diffForHumans(),
        ],
        'message' => 'Story created successfully'
    ], 201);
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



    public function myStories(Request $request)
{
    $userId = $request->user()->id;

    $stories = Story::withCount('views')
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($story) {
            return [
                'id' => $story->id,
                'user_id' => $story->user_id,
                'image' => $this->imageUrl($story->image),
                'caption' => $story->caption,
                'views_count' => $story->views_count,
                'is_active' => (bool) $story->is_active,
                'is_expired' => $story->expires_at->isPast(),
                'expires_at' => $story->expires_at->toDateTimeString(),
                'created_at' => $story->created_at->diffForHumans(),
            ];
        });

    return response()->json([
        'data' => $stories,
        'message' => 'My stories retrieved successfully'
    ]);
}

public function views(Request $request, $id)
{
    $story = Story::where('user_id', $request->user()->id)
        ->findOrFail($id);

    $views = StoryView::with('viewer')
        ->where('story_id', $id)
        ->orderBy('viewed_at', 'desc')
        ->get()
        ->map(function ($view) {
            return [
                'id' => $view->id,
                'viewer_id' => $view->viewer_id,
                'viewer_name' => $view->viewer?->name ?? 'User',
                'viewer_image' => $this->imageUrl($view->viewer?->profile_image),
                'viewed_at' => $view->viewed_at,
            ];
        });

    return response()->json([
        'data' => $views,
        'views_count' => $views->count(),
        'message' => 'Story views retrieved successfully'
    ]);
}

public function destroy(Request $request, $id)
{
    $story = Story::where('user_id', $request->user()->id)
        ->findOrFail($id);

    if ($story->image && !str_starts_with($story->image, 'http')) {
        $path = public_path($story->image);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    $story->delete();

    return response()->json([
        'message' => 'Story deleted successfully'
    ]);
}
}