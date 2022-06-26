<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RestaurantTest extends TestCase
{

    public static function is_restaurant(AssertableJson $json)
    {
        $json
            ->has('id')
            ->whereType('id', 'integer');

        $json
            ->hasAll([
                'name', 'location', 'district', 'url'
            ])
            ->whereAllType([
                'name' => 'string',
                'location' => 'string',
                'district' => 'string',
                'url' => 'array',
            ])
            ->has(
                'url',
                fn (AssertableJson $url) =>
                $url
                    ->hasAll(['all', 'details'])
                    ->whereAllType([
                        'all' => 'string',
                        'details' => 'string',
                    ])
            )
            ->etc();
    }

    public static function is_restaurant_has_many_foods(AssertableJson $json)
    {
        RestaurantTest::is_restaurant($json);
        $json
            ->has('foods')
            ->whereType('foods', 'array');

        if (count($json->toArray()['foods']) > 0) {
            $json
                ->has(
                    'foods.0',
                    fn (AssertableJson $food) =>
                    FoodTest::is_food_has_many_review($food)
                );
        }
    }

    public function test_get_all_restaurant()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/restaurant');

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
                        $this->is_restaurant_has_many_foods($restaurant)
                    )
            );
    }

    public function test_get_one_restaurant()
    {
        $restaurant = Restaurant::first();
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get("/api/restaurant/" . $restaurant->id);

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
                        $this->is_restaurant_has_many_foods($restaurant)
                    )
            );
    }

    public function test_get_one_restaurant_not_found()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get("/api/restaurant/100000");

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
                        $restaurant
                            ->etc()
                    )
                    ->where('data', [])
            );
    }
}
