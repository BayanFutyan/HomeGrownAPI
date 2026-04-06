<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    // ============================================================
    // الأعمدة المسموح بتعبئتها
    // ============================================================
    protected $fillable = [
        'id',
        'follower_id',
        'following_id',
        'rating',
    ];

    // ============================================================
    // تحويل أنواع البيانات
    // ============================================================
    protected $casts = [
        'rating' => 'integer',
    ];

    // ============================================================
    // العلاقات
    // ============================================================

    /**
     * المستخدم الذي يتابع
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * المستخدم الذي يتم متابعته
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}