<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    // ============================================================
    // الأعمدة المسموح بتعبئتها (Mass Assignment)
    // ============================================================
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

    // ============================================================
    // تحويل أنواع البيانات
    // ============================================================
    protected $casts = [
        'price' => 'decimal:2',
        'is_sale' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ============================================================
    // العلاقات (Relationships)
    // ============================================================

    /**
     * الحرفي (صاحب المنتج)
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * العرض الخاص بالمنتج (واحد لواحد)
     */
    public function offer()
    {
        return $this->hasOne(Offer::class);
    }

    /**
     * التعليقات على المنتج
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * التفاصيل الإضافية للمنتج
     */
    public function details()
    {
        return $this->hasMany(ProductDetail::class);
    }

    // ============================================================
    // دوال مساعدة (Accessors & Mutators)
    // ============================================================

    /**
     * حساب السعر بعد الخصم
     */
    public function getDiscountedPriceAttribute()
    {
        if ($this->offer && $this->offer->discount_value) {
            return $this->price - ($this->price * $this->offer->discount_value / 100);
        }
        return $this->price;
    }

    /**
     * هل المنتج عليه عرض فعال؟
     */
    public function getHasActiveOfferAttribute()
    {
        if (!$this->offer) return false;
        
        $now = now();
        return $this->offer->start_date <= $now && $this->offer->end_date >= $now;
    }
}