<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'user_id',
        'food_id',
        'desc',
        'images',
        'rating',
        'price',
        'FDA',
        'porsi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class, 'review_id', 'id');
    }
}
