<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image',
        'caption',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(StoryView::class);
    }

    // هل القصة انتهت؟
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // هل المستخدم شاهد هذه القصة؟
    public function isViewedByUser($userId): bool
    {
        return $this->views()->where('viewer_id', $userId)->exists();
    }

    // عدد المشاهدات
    public function getViewsCountAttribute(): int
    {
        return $this->views()->count();
    }
}