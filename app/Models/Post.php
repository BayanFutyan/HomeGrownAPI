<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
         'user_id', 
        'content',
        'occasion',
        'likes_count',
        'comments_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $appends = ['comments_count'];


    // العلاقات
   public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // هل المستخدم معجب بهذا المنشور؟
    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function saves()
{
    return $this->morphMany(Save::class, 'saveable');
}

public function isSavedByUser($userId): bool
{
    return $this->saves()->where('user_id', $userId)->exists();
}


public function getCommentsCountAttribute()
{
    return $this->comments()->count();
}
}