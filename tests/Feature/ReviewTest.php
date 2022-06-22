<?php

namespace Tests\Feature;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    public static function is_review(AssertableJson $json)
    {
        $json
            ->hasAll([
                'desc', 'rating', 'price',
                'FDA', 'created_at', 'updated_at',
                'food', 'user', 'url'
            ])
            ->whereAllType([
                'desc' => 'string',
                'rating' => 'integer',
                'price' => 'integer',
                'FDA' => 'string',
                'created_at' => 'string',
                'updated_at' => 'string',
                'food' => 'array',
                'user' => 'array',
                'url' => 'array',
            ])
            ->has(
                'food',
                fn ($food) =>
                FoodTest::is_food($food)
            )
            ->has(
                'user',
                fn ($user) =>
                UserTest::is_user($user)
            )
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
                        100,
                        fn ($review) =>
                        $this->is_review($review)
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
                        $this->is_review($review)
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
