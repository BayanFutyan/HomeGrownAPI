<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'occasion' => $this->occasion,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'is_liked' => $request->user()
                ? $this->isLikedByUser($request->user()->id)
                : false,
            // تغيير من 'admin' إلى 'user'
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'profile_image' => $this->user?->profile_image
                    ? url('/storage/' . $this->user->profile_image)
                    : null,
            ],

            'is_saved' => $request->user()
                ? $this->isSavedByUser($request->user()->id)
                : false,
            'saves_count' => $this->saves()->count(),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
