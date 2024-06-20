<?php

namespace Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditPlateTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Plate      $plate;

    #[Test]
    public function an_autheticated_user_can_edit_a_plate(): void
    {
        $this->withoutExceptionHandling();
        #teniendo
        $data = [
            'name'        => 'NEW Name test',
            'description' => 'NEW Description test',
            'price'       => 'NEW $123',
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'put',
                "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['plate' => ['id', 'restaurant_id', 'name', 'description', 'price']],
            'message', 'status', 'errors'
        ]);
        $response->assertJsonFragment([
            'data' => [
                'plate' => [
                    ...$data,
                    'id'            => $this->plate->id,
                    'restaurant_id' => $this->restaurant->id,
                    'links'         => ['parent' => route('restaurants.show', $this->restaurant)],
                ]
            ]
        ]);
        $this->assertDatabaseMissing('plates', [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '$123',
        ]);
    }

    #[Test]
    public function a_unautheticated_user_cannot_update_a_plate(): void
    {
        #teniendo
        $data = [
            'name'        => 'New Name test',
            'description' => 'New Description test',
            'price'       => 'New $123',
        ];

        #haciendo
        $response =
            $this->putJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);
        $response->assertStatus(401);
    }

    #[Test]
    public function a_autheticated_user_can_only_update_their_plates(): void
    {
        #teniendo
        $data = [
            'name'        => 'New Name test',
            'description' => 'New Description test',
            'price'       => 'New $123',
        ];
        $user = User::factory()->create();
        #haciendo
        $response =
            $this->apiAs($user, 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}",
                $data);
        $response->assertStatus(403);
    }

    #[Test]
    public function plate_name_is_required()
    {
        #teniendo
        $data = [
            'name'        => '',
            'description' => 'New Description test',
            'price'       => 'New $123',
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'put',
                "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name']]);
    }

    #[Test]
    public function plate_description_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'New Name test',
            'description' => '',
            'price'       => 'New $123',
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'put',
                "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['description']]);
    }

    #[Test]
    public function plate_price_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'New Name test',
            'description' => 'New Description test',
            'price'       => '',
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'put',
                "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1
        ]);

        $this->plate = Plate::factory()->create([
            'restaurant_id' => $this->restaurant->id
        ]);
    }
}
