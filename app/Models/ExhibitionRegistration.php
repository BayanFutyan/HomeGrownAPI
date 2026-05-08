<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitionRegistration extends Model
{
    protected $fillable = [
        'exhibition_id',
        'seller_id',
        'type',
        'status',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function exhibition()
    {
        return $this->belongsTo(Exhibition::class);
    }
    
}