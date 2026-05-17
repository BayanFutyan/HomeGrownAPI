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
                    ->withPivot('rating')
                    ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'following_id')
                    ->withPivot('rating')
                    ->withTimestamps();
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
// دوال مساعدة
// ============================================================

// ... الدوال الموجودة (isAdmin, isArtisan, etc)

/**
 * Get followers count
 */
public function getFollowersCountAttribute(): int
{
    return $this->followers()->count();
}

/**
 * Get following count
 */
public function getFollowingCountAttribute(): int
{
    return $this->following()->count();
}
}