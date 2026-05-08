<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'status',
        'location',
        'participants_count',
        'type',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}