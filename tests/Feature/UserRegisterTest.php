<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_register(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        #haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(200); //created
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment([
            'message'   => 'OK', 'data' => [
                'user' => [
                    'id'        => 1,
                    'email'     => 'email@email.com',
                    'name'      => 'example',
                    'last_name' => 'example example',
                ]
            ], 'status' => 200
        ]);
        $this->assertDatabaseCount('users', 1);

        $this->assertDatabaseHas('users', [
            'email'     => 'email@email.com',
            'name'      => 'example',
            'last_name' => 'example example',
        ]);
    }

    #[Test]
    public function a_registered_user_can_login(): void
    {
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $this->postJson("{$this->apiBase}/users", $data);
        $response = $this->postJson("{$this->apiBase}/login", [
            'email'    => 'email@email.com',
            'password' => 'password',
        ]);
        #esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'email'     => '',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        # teniendo
        $data = [
            'email'     => 'asdasdasdasd',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);


        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);
    }

    #[Test]
    public function email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'email@email.com']);
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email has already been taken.']]]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => '',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);

        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_have_at_lease_8_characters(): void
    {
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'abcd',
            'name'      => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);
        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function name_must_be_required(): void
    {
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => '',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);
        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function name_must_have_at_lease_2_characters(): void
    {
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => 'e',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);
        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }


    #[Test]
    public function last_name_must_be_required(): void
    {
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => '',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);
        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    #[Test]
    public function last_name_must_have_at_lease_2_characters(): void
    {
        # teniendo
        $data = [
            'email'     => 'email@email.com',
            'password'  => 'password',
            'name'      => 'example',
            'last_name' => 'e',
        ];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/users", $data);
        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

}
