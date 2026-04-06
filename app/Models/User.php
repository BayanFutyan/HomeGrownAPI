<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRoleEnum;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'phone',
        'profile_image',
        'address',
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
        return $this->role === UserRoleEnum::PROJECT_OWNER;
    }

    public function isNormalUser(): bool
    {
        return $this->role === UserRoleEnum::USER;
    }
}