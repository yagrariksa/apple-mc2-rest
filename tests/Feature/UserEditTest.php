<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserEditTest extends TestCase
{
    public function test_edit_profile()
    {
        $user = User::whereNotNull('api_token')->first();
        $file = UploadedFile::fake()->image('profile' . rand(1, 3000) . '.jpg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/user', [
            'name' => 'change',
            'email' => 'email@gmail.com',
            'password' => 'password',
            'image' => $file,
            'role' => 'change-role',
        ]);


        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'success edit profile')
                    ->has(
                        'data',
                        fn (AssertableJson $user) =>
                        UserTest::is_user($user)
                    )
            );
    }

    public function test_edit_profileOnlyName()
    {
        $user = User::whereNotNull('api_token')->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/user', [
            'name' => 'change',
        ]);


        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'success edit profile')
                    ->has(
                        'data',
                        fn (AssertableJson $user) =>
                        UserTest::is_user($user)
                    )
            );
    }

    public function test_edit_profileOnlyEmail()
    {
        $user = User::whereNotNull('api_token')->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/user', [
            'email' => 'email@gmail.com',
        ]);


        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'success edit profile')
                    ->has(
                        'data',
                        fn (AssertableJson $user) =>
                        UserTest::is_user($user)
                    )
            );
    }

    public function test_edit_profileOnlyPassword()
    {
        $user = User::whereNotNull('api_token')->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/user', [
            'password' => 'password',
        ]);


        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'success edit profile')
                    ->has(
                        'data',
                        fn (AssertableJson $user) =>
                        UserTest::is_user($user)
                    )
            );
    }

    public function test_edit_profileOnlyImage()
    {
        $user = User::whereNotNull('api_token')->first();
        $file = UploadedFile::fake()->image('profile' . rand(1, 3000) . '.jpg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/user', [
            'image' => $file,
        ]);


        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'success edit profile')
                    ->has(
                        'data',
                        fn (AssertableJson $user) =>
                        UserTest::is_user($user)
                    )
            );
    }

    public function test_edit_profileOnlyRole()
    {
        $user = User::whereNotNull('api_token')->first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user->api_token
        ])->post('/api/user', [
            'role' => 'change-role',
        ]);


        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->where('message', 'success edit profile')
                    ->has(
                        'data',
                        fn (AssertableJson $user) =>
                        UserTest::is_user($user)
                    )
            );
    }
}
