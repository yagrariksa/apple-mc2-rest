<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('id_ID');
        for ($i = 0; $i < 50; $i++) {
            $name = $faker->name();
            $names = explode(' ', $name);
            User::create([
                'name' => $name,
                'email' => join('', [$names[0], $names[1], '@gmail.com']),
                'password' => Hash::make('password'),
            ]);
        }
    }
}
