<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'likeable_type' => $this->likeable_type,
            'likeable_id' => $this->likeable_id,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}