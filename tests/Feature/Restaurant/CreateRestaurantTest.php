<?php

namespace Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_create_a_restaurant(): void
    {
        $this->withoutExceptionHandling();
        # teniendo
        $data = [
            'name'        => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants", $data);

        #esperando
        $response->assertStatus(200); //created

        $response->assertJsonStructure([
            'message', 'data' => [
                'restaurant' => ['id', 'name', 'slug', 'description']
            ], 'errors', 'status'
        ]);

        $this->assertDatabaseCount('restaurants', 1);
        $restaurant = Restaurant::first();
        $this->assertStringContainsString('new-restaurant', $restaurant->slug);
        $this->assertDatabaseHas('restaurants', [
            'id'          => 1,
            'user_id'     => 1,
            'name'        => 'New restaurant',
            'description' => 'New restaurant description',
        ]);
    }

    #[Test]
    public function a_unauthenticated_user_cannot_create_a_restaurant(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        #haciendo
        $response = $this->postJson("{$this->apiBase}/restaurants");

        #esperando
        $response->assertStatus(401); //created
    }
    
    #[Test]
    public function name_must_be_required(): void
    {
        # teniendo
        $data = [
            'name'        => '',
            'description' => 'New restaurant description',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants", $data);


        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    #[Test]
    public function description_must_be_required(): void
    {
        # teniendo
        $data = [
            'name'        => 'New restaurant',
            'description' => '',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants", $data);

        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

}
