<?php

namespace Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PlateListTest extends TestCase
{
    use RefreshDatabase;

    protected $plates;
    protected $restaurant;

    #[Test]
    public function an_authenticated_user_must_see_their_plates(): void
    {
        $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.plates');
        $response->assertJsonStructure([
            'data' => [
                'plates' => [
                    '*' => ['id', 'restaurant_id', 'name', 'description', 'price', 'image', 'links']
                ],
            ]
        ]);
        $response->assertJsonPath('data.plates.0.links.self',
            route('restaurants.plates.show', [$this->restaurant, $this->restaurant->plates()->first()]));
        foreach (range(0, 14) as $platePosition) {
            $response->assertJsonPath("data.plates.{$platePosition}.restaurant_id", $this->restaurant->id);
        }
    }

    #[Test]
    public function a_unauthenticated_user_cannot_see_the_list_of_plates(): void
    {
        //$this->withoutExceptionHandling();
        # teniendo
        #haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");

        #esperando
        $response->assertStatus(401); //created
    }

    #[Test]
    public function an_authenticated_user_must_see_only_their_restaurants(): void
    {
        $user     = User::factory()->create();
        $response = $this->apiAs($user, 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");

        $response->assertStatus(403);
    }

    #[Test]
    public function a_user_must_see_their_paginated_plates(): void
    {
        $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates");
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.plates');
        $response->assertJsonStructure([
            'data' => [
                'plates',
                'total',
                'current_page',
                'per_page',
                'total_pages',
                'count'
            ],
            'message', 'status', 'errors'
        ]);

        $response->assertJsonPath('data.total', 15);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.per_page', 15);
        $response->assertJsonPath('data.total_pages', 1);
        $response->assertJsonPath('data.count', 15);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1
        ]);
        $this->plates     = Plate::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant,
        ]);
    }
}
