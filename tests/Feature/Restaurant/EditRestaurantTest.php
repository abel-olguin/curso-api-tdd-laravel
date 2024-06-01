<?php

namespace Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RestaurantSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditRestaurantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_can_edit_a_restaurant(): void
    {
        $this->withoutExceptionHandling();
        # teniendo

        $data = [
            'name'        => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

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
        $this->assertDatabaseMissing('restaurants', [
            'name'        => 'Restaurant',
            'description' => 'Restaurant description',
        ]);
    }


    #[Test]
    public function the_slug_must_not_change_if_the_name_is_the_same(): void
    {
        $this->withoutExceptionHandling();
        # teniendo

        $data = [
            'name'        => 'Restaurant',
            'description' => 'New restaurant description',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        #esperando
        $response->assertStatus(200); //created

        $response->assertJsonStructure([
            'message', 'data' => [
                'restaurant' => ['id', 'name', 'slug', 'description']
            ], 'errors', 'status'
        ]);

        $this->assertDatabaseCount('restaurants', 1);
        $restaurant = Restaurant::find(1);
        $this->assertTrue($restaurant->slug === $this->restaurant->slug);
        $this->assertDatabaseMissing('restaurants', [
            'name'        => 'Restaurant',
            'description' => 'Restaurant description',
        ]);
    }

    #[Test]
    public function a_unauthenticated_user_cannot_edit_a_restaurant(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo

        $data = [
            'name'        => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        #haciendo
        $response = $this->putJson("{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        #esperando
        $response->assertStatus(401); //created
    }

    #[Test]
    public function a_user_should_only_update_their_restaurants(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        $restaurant = Restaurant::factory()->create();
        $data       = [
            'name'        => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        #haciendo
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$restaurant->id}", $data);

        #esperando
        $response->assertStatus(403); //created
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
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);
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
        $response = $this->apiAs(User::find(1), 'put', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        #esperando

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id'     => 1,
            'name'        => 'Restaurant',
            'slug'        => 'restaurant',
            'description' => 'Restaurant description',
        ]);
    }

}
