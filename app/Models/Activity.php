<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'target_type',
        'target_id',
        'target_title',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function target()
    {
        return $this->morphTo();
    }

    // هل تمت القراءة؟
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    // تحديد كمقروء
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }
}