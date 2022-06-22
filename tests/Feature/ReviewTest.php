<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewTest extends TestCase
{
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
                        $review
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
                                $food
                                    ->has('name')
                                    ->whereType('name', 'string')
                                    ->has(
                                        'restaurant',
                                        fn ($restaurant) =>
                                        $restaurant
                                            ->hasAll([
                                                'name', 'location', 'district'
                                            ])
                                            ->whereAllType([
                                                'name' => 'string',
                                                'location' => 'string',
                                                'district' => 'string',
                                            ])
                                    )

                            )
                            ->has(
                                'user',
                                fn ($user) =>
                                $user
                                    ->hasAll([
                                        'name', 'email'
                                    ])
                                    ->whereAllType([
                                        'name' => 'string',
                                        'email' => 'string'
                                    ])
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
                            )
                    )
            );
    }
}
