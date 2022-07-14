<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

class CommentTest extends TestCase
{
    public static function is_comment(AssertableJson $comment)
    {
        $comment
            ->hasAll(['id', 'comment', 'rid'])
            ->whereAllType([
                'id' => 'integer',
                'comment' => 'string',
                'rid' => 'integer',
            ]);
    }

    public function test_example()
    {
        $user = User::whereNotNull('api_token')->first();
        $review = Review::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token,
        ])->postJson('/api/review/' . $review->id, [
            'comment' => Str::random(16),
        ]);

        $response->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->has('data')
                    ->whereAllType([
                        'message' => 'string',
                        'data' => 'array'
                    ])
                    ->etc()
            );
    }
}
