<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\UserFcmToken;
use App\Services\FirebaseNotificationService;
use App\Models\User;

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

    // جلب إشعارات المستخدم الحالي
public function index(Request $request)
{
    $user = $request->user();

    $notifications = Notification::where('user_id', $user->id)
        ->latest()
        ->get()
        ->map(function ($n) {
            $sender = null;
            $userProfileImage = null;

            $data = is_array($n->data) ? $n->data : json_decode($n->data, true);

            $senderId =
                $data['seller_id'] ??
                $data['actor_id'] ??
                $data['artisan_id'] ??
                $data['owner_id'] ??
                null;

            if ($senderId) {
                $sender = User::find($senderId);

                if ($sender && $sender->profile_image) {
                    if (str_contains($sender->profile_image, 'http')) {
                        $userProfileImage = $sender->profile_image;
                    } else {
                        $userProfileImage = url($sender->profile_image);
                    }
                }
            }

            if (!$userProfileImage) {
                $userProfileImage = url('images/avatars/avatar2.jpg');
            }

            return [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'is_read' => $n->is_read,
                'time' => $n->created_at->diffForHumans(),
                'user_profile_image' => $userProfileImage,
                'data' => $data,
            ];
        });

    return response()->json(['data' => $notifications]);
}

    // تعليم إشعار كمقروء
    public function markAsRead(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json(['message' => 'Notification marked as read']);
    }
}