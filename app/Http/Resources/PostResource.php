<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $userId = $user ? $user->id : null;
        
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'occasion' => $this->occasion ?? 'general',
            'likes_count' => $this->likes_count ?? 0,
            'comments_count' => $this->comments_count ?? 0,
            'is_liked_by_user' => $userId ? $this->isLikedByUser($userId) : false,
            'is_saved_by_user' => $userId ? $this->isSavedByUser($userId) : false,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'profile_image' => $this->user->profile_image,
                'email' => $this->user->email,
            ] : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toDateTimeString() : null,
        ];
    }
}