<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\Restaurant;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $r = Restaurant::get();
        foreach ($r as $resto) {
            for ($i = 0; $i < 5; $i++) {
                Food::create([
                    'restaurant_id' => $resto->id,
                    'uid' => Uuid::uuid4(),
                    'name' => $faker->colorName() . " " . $faker->lastName()
                ]);
            }
        }
    }
}
