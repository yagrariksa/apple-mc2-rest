<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Faker\Factory;
use Faker\Provider\id_ID\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('id_ID');
        $district = [
            'Gubeng',
            'Gunung Anyar',
            'Sukolilo',
            'Tambaksari',
            'Mulyorejo',
            'Rungkut',
            'Tenggilis Mejoyo',
        ];
        for ($i = 0; $i < 5; $i++) {
            $d = $district[array_rand($district)];
            Restaurant::create([
                'uid' => Uuid::uuid4(),
                'name' => $faker->firstName() . " " . $faker->colorName(),
                'location' => $faker->streetName() . ", " . $d,
                'district' => $d
            ]);
        }
    }
}
