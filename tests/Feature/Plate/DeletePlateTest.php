<?php

namespace Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeletePlateTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Plate      $plate;
    protected User       $user;

    #[Test]
    public function an_authenticated_user_can_delete_their_plates(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->apiAs($this->user, 'delete',
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'message' => 'OK',
        ]);
        $this->assertDatabaseMissing('plates', ['id' => $this->plate->id]);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_delete_any_plates()
    {
        $response = $this->deleteJson(
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        $response->assertStatus(401);
    }

    #[Test]
    public function an_authenticated_user_can_only_delete_their_plates(): void
    {
        //$this->withoutExceptionHandling();
        $user     = User::factory()->create();
        $response = $this->apiAs($user, 'delete',
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
