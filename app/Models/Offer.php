<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'discount_value',
        'start_date',
        'end_date',
        'discounted_price',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ✅ Model Events: تتغير is_sale تلقائياً عند حفظ أو حذف العرض
    protected static function booted()
    {
        // عند إنشاء أو تحديث العرض
        static::saved(function ($offer) {
            $offer->product->updateSaleStatus();
        });
        
        // عند حذف العرض
        static::deleted(function ($offer) {
            $offer->product->updateSaleStatus();
        });
    }

    /**
     * العلاقة مع المنتج
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * التحقق من أن العرض لا يزال فعالاً
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }
}