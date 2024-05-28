<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\UserSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected $token = '';
    protected $email = '';

    #[Test]
    public function an_existing_user_can_reset_their_password(): void
    {
        $this->sendResetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email'                 => $this->email,
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $user = User::find(1);
        $this->assertTrue(Hash::check('newpassword', $user->password));
    }

    #[Test]
    public function email_must_be_required(): void
    {
        #teniendo
        $data = ['email' => ''];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        #teniendo
        $data = ['email' => 'notanemail'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);
    }

    #[Test]
    public function email_must_be_an_existing_email(): void
    {
        #teniendo
        $data = ['email' => 'notexisting@example.com'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The selected email is invalid.']]]);
    }

    #[Test]
    public function email_must_be_associated_with_the_token(): void
    {
        $this->sendResetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}", [
            'email'                 => 'fake@email.com',
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword'
        ]);
        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'data', 'status']);
        $response->assertJsonFragment([
            'message' => 'Invalid email'
        ]);
    }


    #[Test]
    public function password_must_be_required(): void
    {
        // no es necesario usar $this->sendResetPassword(); aquí

        $response = $this->putJson("{$this->apiBase}/reset-password?token=testToken", [
            'email'                 => 'test@email.com',
            'password'              => '',
            'password_confirmation' => 'newpassword'
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    #[Test]
    public function password_must_be_confirmed(): void
    {
        // no es necesario usar $this->sendResetPassword(); aquí

        $response = $this->putJson("{$this->apiBase}/reset-password?token=testToken", [
            'email'                 => 'test@email.com',
            'password'              => 'newpassword',
            'password_confirmation' => ''
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
        $response->assertJsonFragment([
            'errors' => [
                'password' => [
                    'The password field confirmation does not match.'
                ]
            ]
        ]);
    }

    #[Test]
    public function token_must_be_a_valid_token(): void
    {
        $this->sendResetPassword();

        $response = $this->putJson("{$this->apiBase}/reset-password?token={$this->token}dasdasddasd", [
            'email'                 => $this->email,
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword'
        ]);
        $response->assertStatus(500);
        $response->assertJsonStructure(['message', 'data', 'status']);
        $response->assertJsonFragment([
            'message' => 'Invalid token'
        ]);
    }


    public function sendResetPassword()
    {
        //$this->withoutExceptionHandling();
        Notification::fake();
        # teniendo
        $data = ['email' => 'example@example.com'];

        # haciendo
        $response = $this->postJson("{$this->apiBase}/reset-password", $data);

        #esperando
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'OK']);
        $user = User::find(1);

        Notification::assertSentTo([$user], function (ResetPasswordNotification $notification) {
            $url   = $notification->url;
            $parts = parse_url($url);
            parse_str($parts['query'], $query);
            $this->token = $query['token'];
            $this->email = $query['email'];
            return str_contains($url, 'http://front.app/reset-password?token=');
        });
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
}
