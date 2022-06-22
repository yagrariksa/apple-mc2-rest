<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\Review;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $faker = Factory::create();
        $f = Food::get();
        $u = User::get();
        $FDA = ['grabfood', 'gofood', 'shopeefood', 'travelokafood'];
        for ($i = 0; $i < 100; $i++) {
            Review::create([
                'uid' => Uuid::uuid4(),
                'user_id' => $u->random()->id,
                'food_id' => $f->random()->id,
                'desc' => $faker->paragraph(),
                'rating' => rand(1, 5),
                'price' => rand(10000, 40000),
                'FDA' => $FDA[array_rand($FDA)],
            ]);
        }
    }
}
