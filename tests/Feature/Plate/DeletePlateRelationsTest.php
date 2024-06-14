<?php

namespace Plate;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeletePlateRelationsTest extends TestCase
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
        $this->assertDatabaseMissing('menus_plates', ['plate_id' => $this->plate->id]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user       = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plate      = Plate::factory()->hasAttached(Menu::factory())
                                 ->create(['restaurant_id' => $this->restaurant->id]);
    }

}
