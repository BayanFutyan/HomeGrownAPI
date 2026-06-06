<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteArtisan extends Model
{
    protected $fillable = [
        'owner_id',
        'artisan_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function artisan()
    {
        return $this->belongsTo(User::class, 'artisan_id');
    }
}