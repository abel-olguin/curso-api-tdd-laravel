<?php

namespace Auth;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_can_modify_their_data(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'name'      => 'newname',
            'last_name' => 'new lastname',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
        #esperando
        $response->assertStatus(200); //created
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonPath('data.user',[
                    'id'        => 1,
                    'email'     => 'example@example.com',
                    'name'      => 'newname',
                    'last_name' => 'new lastname',
                    'roles'     => [Roles::USER->value],
            ]);

        $this->assertDatabaseMissing('users', [
            'email'     => 'example@example.com',
            'name'      => 'User',
            'last_name' => 'Test',
        ]);
    }

    #[Test]
    public function an_authenticated_user_cannot_modify_their_email(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'email'     => 'newemail@example.com',
            'name'      => 'newname',
            'last_name' => 'new lastname',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        #esperando
        $response->assertStatus(200); //created
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonPath('data.user', [
                    'id'        => 1,
                    'email'     => 'example@example.com',
                    'name'      => 'newname',
                    'last_name' => 'new lastname',
                    'roles'     => [Roles::USER->value]
            ]);
    }

    #[Test]
    public function an_aunthenticated_user_cannot_modify_their_password(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'password'  => 'newpassword',
            'name'      => 'newname',
            'last_name' => 'new lastname',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);

        #esperando
        $response->assertStatus(200); //created
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $user = User::find(1);
        $this->assertFalse(Hash::check('newpassword', $user->password));
    }


    #[Test]
    public function name_must_be_required(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $data = [
            'name'      => '',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function name_must_have_at_lease_2_characters(): void
    {
        # teniendo
        $data = [
            'name'      => 'e',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }


    #[Test]
    public function last_name_must_be_required(): void
    {
        # teniendo
        $data = [
            'name'      => 'example',
            'last_name' => '',
        ];

        # haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    #[Test]
    public function last_name_must_have_at_lease_2_characters(): void
    {
        # teniendo
        $data = [
            'name'      => 'example',
            'last_name' => 'e',
        ];

        # haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/profile", $data);
        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->seed(UserSeeder::class);
    }
}
