<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RestaurantTest extends TestCase
{

    public static function is_restaurant(AssertableJson $json, $with_id = true)
    {
        if ($with_id) {
            $json
                ->has('id')
                ->whereType('id', 'integer');
        }

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

    public static function is_restaurant_has_many_foods(AssertableJson $json, $with_id = true)
    {
        RestaurantTest::is_restaurant($json, $with_id);
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
                        $this->is_restaurant_has_many_foods($restaurant)
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
                        $this->is_restaurant_has_many_foods($restaurant, false)
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
