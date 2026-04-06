<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    // ============================================================
    // الأعمدة المسموح بتعبئتها
    // ============================================================
    protected $fillable = [
        'id',
        'product_id',
        'detail_name',
        'detail_value',
    ];

    // ============================================================
    // العلاقات
    // ============================================================

    /**
     * المنتج المرتبط بهذا التفصيل
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}