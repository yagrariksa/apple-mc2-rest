<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reviews = Review::get();
        $category = ['pizza', 'burger', 'drink'];
        foreach ($reviews as $review) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                ReviewImage::create([
                    'review_id' => $review->id,
                    'filename' => "https://api.lorem.space/image/" . $category[array_rand($category)] . "?w=150&h=150"
                ]);
            }
        }
    }
}
