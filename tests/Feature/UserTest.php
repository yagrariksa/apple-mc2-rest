<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'ViviHasanah@gmail.com',
            'password' => 'password'
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_authenticated($json)
            );
    }

    public function test_login_fail_password()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'ViviHasanah@gmail.com',
            'password' => 'pw'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_authenticated($json)
            );
    }

    public function test_login_fail_email()
    {
        $response2 = $this->postJson('/api/login', [
            'email' => 'ViviHasanah2@gmail.com',
            'password' => 'password'
        ]);

        $response2
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_not_authenticated($json)
            );
    }

    public function test_login_fail_no_email()
    {
        $response2 = $this->postJson('/api/login', [
            'password' => 'password'
        ]);

        $response2
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_login_fail($json)
            );
    }

    public function test_login_fail_no_password()
    {
        $response2 = $this->postJson('/api/login', [
            'email' => 'ViviHasanah2@gmail.com',
        ]);

        $response2
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_login_fail($json)
            );
    }

    public function test_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'EkoSumeko',
            'email' => 'EkoSumeko' . rand(1, 10000) . '@gmail.com',
            'password' => 'password'
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json
                    ->has('message')
                    ->whereAllType([
                        'message' => 'string',
                        'data' => 'array'
                    ])
                    ->where('message', 'success create new user')
                    ->has(
                        'data',
                        fn (AssertableJson $data) =>
                        $data
                            ->has('api_token')
                            ->whereAllType([
                                'api_token' => 'string',
                                'user' => 'array'
                            ])
                            ->has('user', fn ($user) =>
                            $this->is_user($user))
                    )
            );
    }

    public function test_register_fail_no_name()
    {
        $response = $this->postJson('/api/register', [
            'email' => 'EkoSumeko' . rand(1, 100) . '@gmail.com',
            'password' => 'password'
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_register_fail($json)
            );
    }
    public function test_register_fail_no_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'EkoSumeko',
            'password' => 'password'
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_register_fail($json)
            );
    }

    public function test_register_fail_no_password()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'EkoSumeko',
            'email' => 'EkoSumeko' . rand(1, 100) . '@gmail.com',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_register_fail($json)
            );
    }

    public function test_register_fail_email_is_taken()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'EkoSumeko',
            'email' => 'ViviHasanah@gmail.com',
            'password' => 'password'
        ]);

        $response
            ->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) =>
                $this->is_register_fail($json)
            );
    }



    public function is_user(AssertableJson $user)
    {
        $user
            ->hasAll([
                'name', 'email'
            ])
            ->whereAllType([
                'name' => 'string',
                'email' => 'string'
            ]);
    }

    protected function is_authenticated(AssertableJson $json)
    {
        $json
            ->has('message')
            ->whereAllType([
                'message' => 'string',
                'data' => 'array'
            ])
            ->has('data', fn (AssertableJson $data) =>
            $data
                ->has('api_token')
                ->whereAllType([
                    'api_token' => 'string',
                    'user' => 'array'
                ])
                ->has(
                    'user',
                    fn (AssertableJson $user) =>
                    $this->is_user($user)
                )
                ->etc());
    }

    protected function is_not_authenticated(AssertableJson $json)
    {
        $json
            ->has('message')
            ->where('message', 'you are not authenticated, please correct your email or password')
            ->whereAllType([
                'message' => 'string',
                'data' => 'array',
            ])
            ->has('data');
    }

    protected function is_register_fail(AssertableJson $json)
    {
        $json
            ->has('message')
            ->whereAllType([
                'message' => 'string',
                'data' => 'array'
            ])
            ->where('message', 'you are not successfully register, please try again')
            ->has(
                'data',
                fn ($data) =>
                $data
                    ->etc()
            );
    }

    protected function is_login_fail(AssertableJson $json)
    {
        $json
            ->has('message')
            ->whereAllType([
                'message' => 'string',
                'data' => 'array'
            ])
            ->where('message', 'email and password are required')
            ->has(
                'data',
                fn ($data) =>
                $data
                    ->etc()
            );
    }
}
