<?php

namespace Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteRestaurantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_must_delete_their_restaurants(): void
    {
        $response = $this->apiAs(User::find(1), 'delete', "{$this->apiBase}/restaurants/{$this->restaurant->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'OK']);
        $this->assertDatabaseCount('restaurants', 0);
    }

    #[Test]
    public function a_unauthenticated_user_cannot_edit_a_restaurant(): void
    {
        // $this->withoutExceptionHandling();
        # teniendo
        #haciendo
        $response = $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}");

        #esperando
        $response->assertStatus(401); //created
        $this->assertDatabaseCount('restaurants', 1);
    }

    #[Test]
    public function an_authenticated_user_must_delete_only_their_restaurants(): void
    {
        $user     = User::factory()->create();
        $response = $this->apiAs($user, 'delete', "{$this->apiBase}/restaurants/{$this->restaurant->id}");

        $response->assertStatus(403);
        $this->assertDatabaseCount('restaurants', 1);

    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1,
        ]);
    }
}
