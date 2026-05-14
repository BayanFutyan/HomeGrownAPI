<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seller_id' => $this->seller_id,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'image' => $this->image ? url('/storage/' . $this->image) : null,
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,  // ✅ من $appends
            'is_sale' => (bool) $this->is_sale,
            'sales_count' => $this->sales_count,
            'is_liked' => $this->when(
                $request->user(),
                fn() => $this->isLikedByUser($request->user()->id),
                false
            ),
            'is_saved' => $this->when(
                $request->user(),
                fn() => $this->isSavedByUser($request->user()->id),
                false
            ),
            'seller' => [
                'id' => $this->seller?->id,
                'name' => $this->seller?->name,
                'profile_image' => $this->seller?->profile_image 
                    ? url('/storage/' . $this->seller->profile_image) 
                    : null,
            ],
            'offer' => $this->offer ? [
                'discount_value' => $this->offer->discount_value,
                'discounted_price' => $this->getDiscountedPriceAttribute(),
                'end_date' => $this->offer->end_date,
            ] : null,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}