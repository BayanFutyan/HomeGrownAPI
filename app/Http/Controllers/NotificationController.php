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
                $seller = null;
                $userProfileImage = null;
                
                // إذا كان فيه seller_id في البيانات
                if (isset($n->data['seller_id'])) {
                    $seller = User::find($n->data['seller_id']);
                    if ($seller && $seller->profile_image) {
                        // ✅ إذا كانت الصورة مخزنة كاملة مع المسار
                        if (str_contains($seller->profile_image, 'http')) {
                            $userProfileImage = $seller->profile_image;
                        } else {
                            // ✅ إذا كانت الصورة مخزنة كمسار نسبي
                            $userProfileImage = url($seller->profile_image);
                        }
                    }
                }
                
                // ✅ صورة افتراضية إذا ما في صورة
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