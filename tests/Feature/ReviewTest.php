<?php

namespace Tests\Feature;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    public static function is_review(AssertableJson $json, $with_id = true)
    {
        if ($with_id) {
            $json
                ->has('id')
                ->whereType('id', 'integer');
        }

        $json
            ->hasAll([
                'desc', 'rating', 'price',
                'FDA', 'created_at', 'updated_at',
                'user', 'url', 'images', 'porsi'
            ])
            ->whereAllType([
                'desc' => 'string',
                'rating' => 'integer',
                'price' => 'integer',
                'FDA' => 'string',
                'created_at' => 'string',
                'updated_at' => 'string',
                'user' => 'array',
                'url' => 'array',
                'images' => 'array',
                'porsi' => 'string',
            ])
            ->has(
                'url',
                fn ($url) =>
                $url
                    ->hasAll([
                        'all', 'details'
                    ])
                    ->whereAllType([
                        'all' => 'string',
                        'all' => 'string',
                    ])
            );
    }
    public static function is_review_belongs_to_food_belongs_to_restaurant(AssertableJson $json, $with_id = true)
    {
        ReviewTest::is_review($json, $with_id);

        $json
            ->has(
                'food',
                fn ($food) =>
                FoodTest::is_food_belongs_to_restaurant($food)
            )
            ->whereType('food', 'array');
    }

    public static function is_only_review_by_user(AssertableJson $json)
    {
        $json
            ->has(
                'user',
                fn ($user) =>
                UserTest::is_user($user)
            );
    }
    public function test_get_all_review()
    {
        $response = $this->get('/api/review');

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
                    )
                    ->has(
                        'data.0',
                        fn ($review) =>
                        $this->is_review_belongs_to_food_belongs_to_restaurant($review)
                    )
            );
    }

    public function test_get_spesific_review()
    {
        $review = Review::first();
        $response = $this->get("/api/review/" . $review->id);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->has(
                        'data',
                        fn (AssertableJson $review) =>
                        $this->is_review_belongs_to_food_belongs_to_restaurant($review, false)
                    )
            );
    }

    public function test_get_spesific_review_not_found()
    {
        $response = $this->get("/api/review/100000");

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
                    ->has(
                        'data',
                        fn (AssertableJson $json) =>
                        $json->etc()
                    )
            );
    }
}
