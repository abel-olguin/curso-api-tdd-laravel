<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowPlateTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Plate      $plate;
    protected User       $user;

    #[Test]
    public function an_authenticated_user_can_see_their_plates(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->apiAs($this->user, 'get',
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'plate' => [
                    'id',
                    'restaurant_id',
                    'name',
                    'description',
                    'price',
                    'image',
                    'links'
                ]
            ],
            'message', 'status', 'errors'
        ]);
        $response->assertJsonFragment([
            'data' => [
                'plate' => [
                    'id'            => $this->plate->id,
                    'restaurant_id' => $this->plate->restaurant_id,
                    'name'          => $this->plate->name,
                    'description'   => $this->plate->description,
                    'image'         => $this->plate->image,
                    'price'         => (string)$this->plate->price,
                    'links'         => [
                        'parent' => route('restaurants.show', $this->restaurant),
                    ],
                ]
            ],
        ]);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_see_any_plates()
    {
        $response = $this->getJson(
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(401);
    }

    #[Test]
    public function an_authenticated_user_can_only_see_their_plates(): void
    {
        //$this->withoutExceptionHandling();
        $user     = User::factory()->create();
        $response = $this->apiAs($user, 'get',
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");
        $response->assertStatus(403);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user       = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plate      = Plate::factory()->create(['restaurant_id' => $this->restaurant->id]);
    }

}
