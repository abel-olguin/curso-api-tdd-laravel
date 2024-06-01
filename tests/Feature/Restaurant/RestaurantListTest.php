<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RestaurantListTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_authenticated_user_must_see_their_restaurants(): void
    {
        $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants");

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data.restaurants');
    }

    #[Test]
    public function a_unauthenticated_user_cannot_edit_a_restaurant(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        #haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants");

        #esperando
        $response->assertStatus(401); //created
    }

    #[Test]
    public function an_authenticated_user_must_see_only_their_restaurants(): void
    {
        $user     = User::factory()->create();
        $response = $this->apiAs($user, 'get', "{$this->apiBase}/restaurants");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data.restaurants');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurants = Restaurant::factory()->count(10)->create([
            'user_id' => 1,
        ]);
    }
}
