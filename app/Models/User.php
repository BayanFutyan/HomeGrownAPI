<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRoleEnum;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'profile_image',
        'address',
        'bio',  
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoleEnum::class,
        ];
    }

    // ============================================================
    // العلاقات
    // ============================================================

    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'following_id', 'follower_id')
                    ->withTimestamps(); // تم إزالة rating
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
                    ->withTimestamps(); // تم إزالة rating
    }

    // ============================================================
    // دوال مساعدة
    // ============================================================

    public function isAdmin(): bool
    {
        return $this->role === UserRoleEnum::ADMIN;
    }

    public function isArtisan(): bool
    {
        return $this->role === UserRoleEnum::ARTISAN;
    }

    public function isProjectOwner(): bool
    {
        return $this->role === UserRoleEnum::EXHIBITION_OWNER;
    }

    public function isNormalUser(): bool
    {
        return $this->role === UserRoleEnum::USER;
    }

    // ============================================================
    // دوال مساعدة إضافية
    // ============================================================

    public function getFollowersCountAttribute(): int
    {
        return $this->followers()->count();
    }

    public function getFollowingCountAttribute(): int
    {
        return $this->following()->count();
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'artisan_id');
    }

    public function fcmTokens()
    {
        return $this->hasMany(\App\Models\UserFcmToken::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class, 'user_id');
    }
}