<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // ============================================================
    // الأعمدة المسموح بتعبئتها
    // ============================================================
    protected $fillable = [
        'id',
        'product_id',
        'user_id',
        'comment',
        'parent_id',
        'likes_count',
    ];

    // ============================================================
    // تحويل أنواع البيانات
    // ============================================================
    protected $casts = [
        'likes_count' => 'integer',
    ];

    // ============================================================
    // العلاقات
    // ============================================================

    /**
     * المنتج المرتبط بهذا التعليق
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
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