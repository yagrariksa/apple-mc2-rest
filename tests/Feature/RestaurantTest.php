<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RestaurantTest extends TestCase
{

    public static function is_restaurant(AssertableJson $json)
    {
        $json
            ->hasAll([
                'name', 'location', 'district'
            ])
            ->whereAllType([
                'name' => 'string',
                'location' => 'string',
                'district' => 'string',
            ]);
    }

    public static function is_restaurant_full_data(AssertableJson $json)
    {
        $json
            ->hasAll([
                'name', 'location', 'district', 'foods'
            ])
            ->whereAllType([
                'name' => 'string',
                'location' => 'string',
                'district' => 'string',
                'foods' => 'array',
            ]);
    }

    public function test_get_all_restaurant()
    {
        $response = $this->get('/api/restaurant');

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereAllType([
                        'message' => 'string',
                        'data' => 'array'
                    ])
                    ->has('data')
                    ->has(
                        'data.0',
                        fn (AssertableJson $restaurant) =>
                        $this->is_restaurant_full_data($restaurant)
                    )
            );
    }

    public function test_get_one_restaurant()
    {
        $restaurant = Restaurant::first();
        $response = $this->get("/api/restaurant/" . $restaurant->id);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereAllType([
                        'message' => 'string',
                        'data' => 'array'
                    ])
                    ->has(
                        'data',
                        fn (AssertableJson $restaurant) =>
                        $this->is_restaurant_full_data($restaurant)
                    )
            );
    }

    public function test_get_one_restaurant_not_found()
    {
        $response = $this->get("/api/restaurant/100000");

        $response
            ->assertStatus(404)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereAllType([
                        'message' => 'string',
                        'data' => 'array'
                    ])
                    ->where('message', 'item not found')
                    ->has(
                        'data',
                        fn (AssertableJson $restaurant) =>
                        $restaurant->etc()
                    )
            );
    }
}
