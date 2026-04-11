<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'detail_name',
        'detail_value',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}