<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artisan_id',
        'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    /**
     * العلاقة مع المستخدم (الذي قام بالتقييم)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * العلاقة مع الحرفي (الذي تم تقييمه)
     */
    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }
}