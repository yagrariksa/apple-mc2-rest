<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'restaurant_id',
        'name',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'food_id', 'id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    protected $table = 'foods';
}
