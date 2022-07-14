<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewFilterTest extends TestCase
{
    private function is_not_null(AssertableJson $json, $data)
    {
        if (sizeof($json->toArray()['data']) > 0) {
            $json
                ->has('message')
                ->whereAllType([
                    'message' => 'string',
                    'data' => 'array'
                ])
                ->has(
                    'data',
                    sizeof($data)
                )
                ->has(
                    'data.0',
                    fn ($review) =>
                    ReviewTest::is_review_belongs_to_food_belongs_to_restaurant($review)
                )
                ->has(
                    'data.0',
                    fn (AssertableJson $review) =>
                    $review
                        ->where('id', $data[0]->id)
                        ->etc()
                );
        } else {
            $json
                ->has('message')
                ->whereAllType([
                    'message' => 'string',
                    'data' => 'array'
                ]);
        }
    }
    public function test_filter_price()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review?pricestart=0&pricefinish=20000');

        $data_review = Review::where('price', '>=', '0')->where('price', '<=', '20000')->get();
        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_null($json, $data_review)
            );
    }

    public function test_filter_fda()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review?FDA=shopeefood');

        $data_review = Review::where('FDA', 'shopeefood')->get();
        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_null($json, $data_review)
            );
    }

    public function test_filter_rating()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review?rating=3');

        $data_review = Review::where('rating', '3')->get();
        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_null($json, $data_review)
            );
    }

    public function test_filterAll()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review?pricestart=0&pricefinish=20000&FDA=gofood&rating=2');

        $data_review = Review::where('price', '>=', '0')
            ->where('price', '<=', '20000')
            ->where('FDA', 'gofood')
            ->where('rating', '2')
            ->get();

        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_null($json, $data_review)
            );
    }

    public function test_filterNotFound()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review?pricestart=0&pricefinish=20000&FDA=gofood&rating=4');

        $response->assertStatus(404)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_null($json, null)
            );
    }
}
