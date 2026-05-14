<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentable_type',
        'commentable_id', 
        'user_id',
        'comment',
        'parent_id',
        'likes_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
    ];

    // ============================================================
    // العلاقات
    // ============================================================

    /**
     * العلاقة مع المنتج (طريقة قديمة)
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * ✅ العلاقة متعددة الأشكال (للتعليقات على المنتجات والمنشورات)
     * هذه العلاقة المطلوبة لـ morphMany في Product و Post
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * المستخدم الذي كتب التعليق
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * التعليق الأصلي (إذا كان هذا التعليق رداً)
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * الردود على هذا التعليق
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}