<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExhibitionInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhibition_id',
        'user_id',
    ];

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}