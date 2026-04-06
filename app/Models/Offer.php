<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    // ============================================================
    // الأعمدة المسموح بتعبئتها
    // ============================================================
    protected $fillable = [
        'id',
        'product_id',
        'discount_value',
        'start_date',
        'end_date',
        'discounted_price',
    ];

    // ============================================================
    // تحويل أنواع البيانات
    // ============================================================
    protected $casts = [
        'discount_value' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ============================================================
    // العلاقات
    // ============================================================

    /**
     * المنتج المرتبط بهذا العرض
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ============================================================
    // دوال مساعدة
    // ============================================================

    /**
     * هل العرض فعال؟
     */
   public function isActive(): bool
{
    $today = today();

    return $this->start_date <= $today && $this->end_date >= $today;
}
}