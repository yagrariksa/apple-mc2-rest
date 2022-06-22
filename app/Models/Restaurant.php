<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'location',
        'district',
    ];

    public function foods()
    {
        return $this->hasMany(Food::class, 'restauran_id','id');
    }

    public function reviews()
    {
        $data = [];
        $foods = $this->foods();
        foreach ($foods as $food) {
            array_push($data, $food->reviews);
        }
        return $data;
    }
}
