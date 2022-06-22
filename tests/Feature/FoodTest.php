<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FoodTest extends TestCase
{
    public static function is_food(AssertableJson $json)
    {
        $json
            ->has('name')
            ->whereType('name', 'string')
            ->has(
                'restaurant',
                fn ($restaurant) =>
                RestaurantTest::is_restaurant($restaurant)
            );
    }
}
