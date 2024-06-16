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

class ListMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User       $user;
    protected Restaurant $restaurant;
    protected Collection $menus;

    #[Test]
    public function an_authenticated_user_must_see_their_menus()
    {
        $response = $this->apiAs($this->user, 'get',
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'menus' => [
                    '*' => [
                        'id', 'restaurant_id', 'description', 'name', 'links'
                    ]
                ]
            ],
            'message', 'status', 'errors'
        ]);
        $response->assertJsonPath('data.menus.0.links.self',
            route('restaurants.menus.index', $this->restaurant));
        $response->assertJsonMissingPath('data.menus.0.plates');
    }

    #[Test]
    public function an_authenticated_user_must_see_their_paginated_menus()
    {
        $response = $this->apiAs($this->user, 'get',
            "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus");

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.menus');

        $response->assertJsonStructure([
            'data' => [
                'total',
                'current_page',
                'per_page',
                'count',
            ]
        ]);
        $response->assertJsonPath('data.total', 150);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.per_page', 15);
        $response->assertJsonPath('data.count', 15);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_see_any_menu()
    {
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/menus");

        $response->assertStatus(401);
    }


    protected function setUp(): void
    {
        parent::setUp();
        $this->user       = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => $this->user->id
        ]);
        $plates           = Plate::factory()->count(100)->create([
            'restaurant_id' => $this->restaurant->id
        ]);

        $this->menus = Menu::factory()->count(150)
                           ->hasAttached($plates->random(15))
                           ->create([
                               'restaurant_id' => $this->restaurant->id,
                           ]);
    }

}
