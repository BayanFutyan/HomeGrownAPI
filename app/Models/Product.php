<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // ✅ إضافة comments_count إلى الـ JSON تلقائياً
    protected $appends = ['comments_count'];

    protected $fillable = [
        'id',
        'seller_id',
        'name',
        'category',
        'description',
        'price',
        'stock',
        'image',
        'likes_count',
        'is_sale',
        'sales_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_sale' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ============================================================
    // العلاقات
    // ============================================================

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * العروض (كل العروض - تاريخية)
     */
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    /**
     * العرض النشط الحالي (واحد فقط)
     * ✅ هذا يحافظ على التوافق مع الكود القديم
     */
    public function offer()
    {
        return $this->hasOne(Offer::class)->where('end_date', '>=', now());
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    public function saves()
    {
        return $this->morphMany(Save::class, 'saveable');
    }

    // ============================================================
    // دوال مساعدة (Accessors)
    // ============================================================

    /**
     * ✅ عدد التعليقات (يضاف تلقائياً في الـ JSON)
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getDiscountedPriceAttribute()
    {
        if ($this->offer && $this->offer->discount_value) {
            return $this->price - ($this->price * $this->offer->discount_value / 100);
        }
        return $this->price;
    }

    public function getHasActiveOfferAttribute()
    {
        return $this->offer !== null;
    }

    public function updateSaleStatus(): void
    {
        $hasActiveOffer = $this->offer()->exists();
        
        if ($this->is_sale != $hasActiveOffer) {
            $this->update(['is_sale' => $hasActiveOffer]);
        }
    }

    public function getActiveOffer()
    {
        return $this->offer;
    }

    public function hasActiveOffer(): bool
    {
        return $this->offer !== null;
    }

    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isSavedByUser($userId): bool
    {
        return $this->saves()->where('user_id', $userId)->exists();
    }

        // ============================================================
    // ✅ أحداث النموذج (Model Events) لتحديث likes_count تلقائياً
    // ============================================================

    /**
     * تحديث عدد الإعجابات في جدول المنتجات
     */
    public function updateLikesCount()
    {
        $count = $this->likes()->count();
        $this->timestamps = false; // لمنع تحديث updated_at
        $this->update(['likes_count' => $count]);
        $this->timestamps = true;
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // عند إضافة Like جديد
        static::created(function ($product) {
            // لا نحتاج لهذا
        });
    }
}