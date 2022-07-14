<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    public static function is_review(AssertableJson $json)
    {
        $json
            ->has('id')
            ->whereType('id', 'integer');

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
    public static function is_review_belongs_to_food_belongs_to_restaurant(AssertableJson $json)
    {
        ReviewTest::is_review($json);

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
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review');

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
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get("/api/review/" . $review->id);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->has(
                        'data',
                        fn (AssertableJson $review) =>
                        $this->is_review_belongs_to_food_belongs_to_restaurant($review)
                    )
                    ->has(
                        'data',
                        fn (AssertableJson $review) =>
                        $this->has_comment($review)
                    )
            );
    }

    public function test_get_spesific_review_not_found()
    {
        $user = User::whereNotNull('api_token')->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get("/api/review/100000");

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

    public function test_get_my_review()
    {
        $r = Review::with('user')->first();

        $user = User::find($r->user->id);
        if ($user->api_token == null) {
            $user->api_token = Str::random(24);
            $user->save();
        }
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review/my');

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->has('data')
                    ->whereType('data', 'array')
            );

        $response->assertJson(
            fn (AssertableJson $json) =>
            $this->has_review($json)
        );
    }

    public function test_get_my_null_review()
    {
        $user = User::whereNotNull('api_token')->first();
        $reviews = Review::where('user_id', $user->id)->get();
        foreach ($reviews as $r) {
            $r->delete();
        }
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->get('/api/review/my');

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->has('data')
                    ->whereType('data', 'array')
                    ->where('data', [])
            );
    }

    public function test_post_review()
    {
        Review::first()->delete();
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $images = [];
        for ($i = 0; $i < rand(1, 3); $i++) {
            // Storage::fake('avatars');

            $file = UploadedFile::fake()->image('review' . $i . '.jpg');
            array_push($images, $file);
        }
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/review', [
            'food_id' => $food->id,
            'desc' => Str::random(5),
            'rating' => rand(1, 5),
            'price' => rand(10000, 50000),
            'FDA' => $fda[array_rand($fda)],
            'porsi' => $p[array_rand($p)],
            'images[]' => $images
        ]);

        // $images[0]->name = 'asd';
        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereType('message', 'string')
                    ->where('message', 'success post new review')
                    ->has(
                        'data',
                        fn (AssertableJson $data) =>
                        ReviewTest::is_review_belongs_to_food_belongs_to_restaurant($data)
                    )
                    ->has(
                        'data',
                        fn (AssertableJson $data) =>
                        $data
                            ->has(
                                'images',
                                fn ($dataimage) =>
                                $this->image_matcher($dataimage, $images)
                            )
                            ->etc()

                    )
                    ->whereType('data', 'array')
            );
    }

    private function image_matcher(AssertableJson $images, $names)
    {
        // dd($names[0]);
        $imgarr = $images->toArray();
        for ($i = 0; $i < count($imgarr); $i++) {
            if (!str_contains($imgarr[$i], $names[$i]->name)) {
                $images->missing($i);
                $images->whereType($i, 'string');
            }
        }
        $images->etc();
    }

    public function test_post_review_no_image()
    {
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'food_id' => $food->id,
            'desc' => Str::random(5),
            'rating' => rand(1, 5),
            'price' => rand(10000, 50000),
            'FDA' => $fda[array_rand($fda)],
            'porsi' => $p[array_rand($p)]
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    public function test_post_review_no_foods()
    {
        $user = User::whereNotNull('api_token')->first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'desc' => Str::random(5),
            'rating' => rand(1, 5),
            'price' => rand(10000, 50000),
            'FDA' => $fda[array_rand($fda)],
            'porsi' => $p[array_rand($p)]
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    public function test_post_review_no_desc()
    {
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'food_id' => $food->id,
            'rating' => rand(1, 5),
            'price' => rand(10000, 50000),
            'FDA' => $fda[array_rand($fda)],
            'porsi' => $p[array_rand($p)]
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    public function test_post_review_no_rating()
    {
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'food_id' => $food->id,
            'desc' => Str::random(5),
            'price' => rand(10000, 50000),
            'FDA' => $fda[array_rand($fda)],
            'porsi' => $p[array_rand($p)]
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    public function test_post_review_no_price()
    {
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'food_id' => $food->id,
            'desc' => Str::random(5),
            'rating' => rand(1, 5),
            'FDA' => $fda[array_rand($fda)],
            'porsi' => $p[array_rand($p)]
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    public function test_post_review_no_fda()
    {
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $p = [
            'pelit', 'b aja',
            'pas', 'kuli'
        ];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'food_id' => $food->id,
            'desc' => Str::random(5),
            'rating' => rand(1, 5),
            'price' => rand(10000, 50000),
            'porsi' => $p[array_rand($p)]
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    public function test_post_review_no_porsi()
    {
        $user = User::whereNotNull('api_token')->first();
        $food = Food::first();
        $fda = ['gofood', 'grabfood', 'shopeefood'];
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->postJson('/api/review', [
            'food_id' => $food->id,
            'desc' => Str::random(5),
            'rating' => rand(1, 5),
            'price' => rand(10000, 50000),
            'FDA' => $fda[array_rand($fda)],
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->error_response($json, 'complete the field that required')
            );
    }

    private function has_review(AssertableJson $json)
    {
        if (count($json->toArray()['data']) > 0) {
            $json
                ->has(
                    'data.0',
                    fn (AssertableJson $review) =>
                    ReviewTest::is_review_belongs_to_food_belongs_to_restaurant($review)
                )
                ->etc();
        }
    }

    private function has_comment(AssertableJson $review)
    {
        if (count($review->toArray()['comments']) > 0) {
            $review
                ->has(
                    'comments',
                    fn ($comment) =>
                    CommentTest::is_comment($comment)
                );
        }
    }

    private function error_response(AssertableJson $json, $message)
    {
        $json
            ->hasAll('message', 'data')
            ->whereAllType([
                'message' => 'string',
                'data' => 'array'
            ])
            ->whereAll([
                'message' => $message,
            ])
            ->etc();
    }
}
