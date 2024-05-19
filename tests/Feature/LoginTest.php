<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function an_existing_user_can_login(): void
    {
        $this->withoutExceptionHandling();
        # teniendo
        $credentials = ['email' => 'example@example.com', 'password' => 'password'];

        # haciendo
        $response = $this->post("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }


    #[Test]
    public function a_non_existing_user_cannot_login(): void
    {
        # teniendo
        $credentials = ['email' => 'example@nonexisting.com', 'password' => 'asdsad'];

        # haciendo
        $response = $this->post('/api/v1/login', $credentials);

        #esperando

        $response->assertStatus(401);
        $response->assertJsonFragment(['status' => 401, 'message' => 'Unauthorized']);
    }


    public function email_must_be_required(): void
    {
        # teniendo
        $credentials = ['password' => 'asdsad'];

        # haciendo
        $response = $this->post('/api/v1/login', $credentials);

        #esperando

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }


    public function password_must_be_required(): void
    {
        # teniendo
        $credentials = ['email' => 'example@nonexisting.com'];

        # haciendo
        $response = $this->post('/api/v1/login', $credentials);

        #esperando

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }
}
