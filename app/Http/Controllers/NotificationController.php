<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Notification;
use App\Models\UserFcmToken;
use App\Services\FirebaseNotificationService;

class NotificationController extends Controller
{
    // إنشاء إشعار تجريبي


public function storeTest(FirebaseNotificationService $firebase)
{
    $notification = Notification::create([
        'user_id' => 1,
        'title' => 'إشعار تجريبي',
        'body'  => 'هذا إشعار وصل من Laravel عبر Firebase',
        'type'  => 'test',
        'data'  => [
            'click_action' => 'test_screen',
        ],
    ]);

    $tokens = UserFcmToken::where('user_id', 1)
        ->pluck('token')
        ->toArray();

    $firebase->send(
        $tokens,
        $notification->title,
        $notification->body,
        [
            'type' => $notification->type,
            'click_action' => 'test_screen',
        ]
    );

    return response()->json([
        'message' => 'Notification saved and sent successfully',
        'tokens_count' => count($tokens),
    ]);
}

    // جلب إشعارات المستخدم
    public function index()
    {
        return Notification::where('user_id', 1)
            ->latest()
            ->get();
    }

    // تعليم كمقروء
    public function markAsRead($id)
    {
        $n = Notification::findOrFail($id);
        $n->update(['is_read' => true]);
        return response()->json(['message' => 'updated']);
    }
}
