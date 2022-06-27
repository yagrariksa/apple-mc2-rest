<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Assert;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class FoodTest extends TestCase
{
    public static function is_food(AssertableJson $json)
    {
        $json
            ->has('id')
            ->whereType('id', 'integer');

        $json
            ->has('name')
            ->whereType('name', 'string');
    }
    public static function is_food_belongs_to_restaurant(AssertableJson $json)
    {
        FoodTest::is_food($json);

        $json
            ->has(
                'restaurant',
                fn ($restaurant) =>
                RestaurantTest::is_restaurant($restaurant)
            );
    }

    public static function is_food_has_many_review(AssertableJson $json)
    {
        FoodTest::is_food($json);

        $json
            ->has('reviews')
            ->whereType('reviews', 'array');

        if (count($json->toArray()['reviews']) > 0) {
            $json
                ->has(
                    'reviews.0',
                    fn (AssertableJson $review) =>
                    ReviewTest::is_review($review)
                );
        }
    }
}
