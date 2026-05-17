<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * جلب النشاطات التي قام بها المستخدم الحالي
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $query = Activity::with(['actor'])
            ->where('actor_id', $userId)
            ->orderBy('created_at', 'desc');
        
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $activities = $query->paginate($request->per_page ?? 20);
        
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'type' => $activity->type,
                'actor_id' => $activity->actor?->id,
                'actor_name' => $activity->actor?->name ?? 'User',
                'actor_image' => $activity->actor?->profile_image 
                    ? url('/' . $activity->actor->profile_image) 
                    : null,
                'target_id' => $activity->target_id,
                'target_title' => $activity->target_title,
                'created_at' => $activity->created_at->toISOString(),
                'read_at' => $activity->read_at,
            ];
        });
        
        return response()->json([
            'data' => $formattedActivities,
            'unread_count' => Activity::where('actor_id', $userId)
                ->whereNull('read_at')
                ->count(),
            'message' => 'Activities retrieved successfully'
        ]);
    }
    
    /**
     * جلب النشاطات حسب النوع
     */
   public function getByType(Request $request, $type)
{
    $userId = $request->user()->id;

    $types = match ($type) {
        'like' => ['like_post', 'like_product'],
        'comment' => ['comment_post', 'comment_product'],
        'save_post' => ['save_post'],
        'save_product' => ['save_product'],
        'follow' => ['follow'],
        default => [$type],
    };

    $query = Activity::with(['actor'])
        ->where('actor_id', $userId)
        ->whereIn('type', $types)
        ->orderBy('created_at', 'desc');

    $activities = $query->paginate($request->per_page ?? 20);

    $formattedActivities = $activities->map(function ($activity) {
        return [
            'id' => $activity->id,
            'type' => $activity->type,
            'actor_id' => $activity->actor?->id,
            'actor_name' => $activity->actor?->name ?? 'User',
            'actor_image' => $activity->actor?->profile_image
                ? url('/' . $activity->actor->profile_image)
                : null,
            'target_id' => $activity->target_id,
            'target_title' => $activity->target_title,
            'created_at' => $activity->created_at->toISOString(),
            'read_at' => $activity->read_at,
        ];
    });

    return response()->json([
        'data' => $formattedActivities,
        'message' => 'Activities retrieved successfully'
    ]);
}
    
    /**
     * تحديد النشاط كمقروء
     */
    public function markAsRead($id, Request $request)
    {
        $activity = Activity::where('actor_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();
        
        $activity->markAsRead();
        
        return response()->json(['message' => 'Activity marked as read']);
    }
    
    /**
     * تحديد كل النشاطات كمقروءة
     */
    public function markAllAsRead(Request $request)
    {
        Activity::where('actor_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['message' => 'All activities marked as read']);
    }
}