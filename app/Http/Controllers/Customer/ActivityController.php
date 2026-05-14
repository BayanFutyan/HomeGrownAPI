<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * جلب نشاطات المستخدم
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        $query = Activity::with(['actor', 'target'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');
        
        // فلترة حسب النوع
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $activities = $query->paginate($request->per_page ?? 20);
        
        // تنسيق البيانات للـ Frontend
        $formattedActivities = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'actor_name' => $activity->actor?->name ?? 'User',
                'actor_image' => $activity->actor?->profile_image 
                    ? url('/storage/' . $activity->actor->profile_image) 
                    : null,
                'action_text' => $this->getActionText($activity),
                'target_title' => $activity->target_title,
                'time_ago' => $activity->created_at->diffForHumans(),
                'is_read' => $activity->isRead(),
                'type' => $activity->type,
                'icon' => $this->getIcon($activity->type),
                'icon_color' => $this->getIconColor($activity->type),
            ];
        });
        
        return response()->json([
            'data' => $formattedActivities,
            'unread_count' => Activity::where('user_id', $userId)->whereNull('read_at')->count(),
            'message' => 'Activities retrieved successfully'
        ]);
    }
    
    /**
     * تحديد النشاط كمقروء
     */
    public function markAsRead($id, Request $request)
    {
        $activity = Activity::where('user_id', $request->user()->id)
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
        Activity::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['message' => 'All activities marked as read']);
    }
    
    /**
     * الحصول على نص الإجراء
     */
    private function getActionText($activity): string
    {
        return match ($activity->type) {
            'follow' => 'started following you',
            'like_post' => 'liked your post',
            'like_product' => 'liked your product',
            'comment_post' => 'commented on your post',
            'comment_product' => 'commented on your product',
            'save_post' => 'saved your post',
            'save_product' => 'saved your product',
            default => 'interacted with you',
        };
    }
    
    /**
     * الحصول على أيقونة النشاط
     */
    private function getIcon(string $type): string
    {
        return match ($type) {
            'follow' => 'person_add',
            'like_post', 'like_product' => 'favorite',
            'comment_post', 'comment_product' => 'chat_bubble',
            'save_post', 'save_product' => 'bookmark',
            default => 'notifications',
        };
    }
    
    /**
     * الحصول على لون الأيقونة
     */
    private function getIconColor(string $type): string
    {
        return match ($type) {
            'follow' => '#4CAF50',
            'like_post', 'like_product' => '#E9896A',
            'comment_post', 'comment_product' => '#2196F3',
            'save_post', 'save_product' => '#FF9800',
            default => '#9E9E9E',
        };
    }
}