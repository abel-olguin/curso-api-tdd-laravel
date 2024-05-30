<?php

namespace Auth;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_existing_user_can_login(): void
    {
        $this->withoutExceptionHandling();
        # teniendo
        $credentials = ['email' => 'example@example.com', 'password' => 'password'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function a_non_existing_user_cannot_login(): void
    {
        # teniendo
        $credentials = ['email' => 'example@nonexisting.com', 'password' => 'password'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(401);
        $response->assertJsonFragment(['status' => 401, 'message' => 'Unauthorized']);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        # teniendo
        $credentials = ['password' => 'password'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        # teniendo
        $credentials = ['email' => 'adasdasasd', 'password' => 'password'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);
    }

    #[Test]
    public function email_must_be_a_string(): void
    {
        # teniendo
        $credentials = ['email' => 123123123, 'password' => 'password'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);
        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        # teniendo
        $credentials = ['email' => 'example@nonexisting.com'];

        # haciendo
        $response = $this->postJson('/api/v1/login', $credentials);

        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_have_at_lease_8_characters(): void
    {
        # teniendo
        $credentials = ['email' => 'example@nonexisting.com', 'password' => 'abcd'];

        # haciendo
        $response = $this->postJson('/api/v1/login', $credentials);
        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
}
