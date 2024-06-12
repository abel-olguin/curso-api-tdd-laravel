<?php

namespace Tests\Feature\Menu;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User       $user;
    protected Restaurant $restaurant;
    protected Collection $plates;
    protected Menu       $menu;

    #[Test]
    public function an_authenticated_user_can_delete_a_menu(): void
    {
        $this->withoutExceptionHandling();

        $response =
            $this->apiAs($this->user, 'delete',
                "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'OK']);

        $this->assertDatabaseMissing('menus', [
            'id' => $this->menu->id,
        ]);

        $this->assertDatabaseMissing('menus_plates', ['menu_id' => $this->menu->id]);
    }

    #[Test]
    public function a_unauthenticated_user_cannot_delete_any_menu(): void
    {
        //$this->withoutExceptionHandling();

        $response =
            $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}");
        $response->assertStatus(401);
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->user       = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plates     = Plate::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->menu       = Menu::factory()
                                ->hasAttached($this->plates)
                                ->create([
                                    'restaurant_id' => $this->restaurant->id,
                                ]);
    }
}
