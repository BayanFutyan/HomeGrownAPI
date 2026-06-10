<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['user_id', 'likeable_type', 'likeable_id'];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($like) {
            if ($like->likeable_type === Product::class) {
                $like->likeable->increment('likes_count');
            }
        });

        static::deleted(function ($like) {
            if ($like->likeable_type === Product::class) {
                $like->likeable->decrement('likes_count');
            }
        });
    }
}