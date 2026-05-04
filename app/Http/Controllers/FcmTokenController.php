<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFcmToken;

class FcmTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string',
        ]);

        UserFcmToken::updateOrCreate(
            [
                'user_id' => 1,
                //'user_id' => auth()->id(),
                'token' => $request->token,
            ],
            [
                'device_type' => $request->device_type,
            ]
        );

        return response()->json([
            'message' => 'FCM token saved successfully'
        ]);
    }
}