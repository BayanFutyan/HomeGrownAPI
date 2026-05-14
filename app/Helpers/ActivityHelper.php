<?php

namespace App\Helpers;

use App\Models\Activity;

class ActivityHelper
{
    /**
     * تسجيل نشاط جديد
     */
    public static function log($userId, $actorId, $type, $targetType, $targetId, $targetTitle = null)
    {
        // لا تسجل نشاط إذا كان المستخدم هو نفس الشخص
        if ($userId == $actorId) {
            return;
        }
        
        // تجنب التكرار (نفس النوع ونفس الهدف خلال 5 دقائق)
        $exists = Activity::where('user_id', $userId)
            ->where('actor_id', $actorId)
            ->where('type', $type)
            ->where('target_id', $targetId)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();
        
        if ($exists) {
            return;
        }
        
        Activity::create([
            'user_id' => $userId,
            'actor_id' => $actorId,
            'type' => $type,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_title' => $targetTitle,
            'read_at' => null,
        ]);
    }
}