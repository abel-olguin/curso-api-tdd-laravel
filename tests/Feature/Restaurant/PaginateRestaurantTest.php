<?php

namespace Tests\Feature\Restaurant;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_must_see_their_restaurants(): void
    {
        $response = $this->apiAs(User::find(1), 'get', "{$this->apiBase}/restaurants");
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.restaurants');
        $response->assertJsonStructure([
            'data' => [
                'restaurants',
                'total',
                'current_page',
                'per_page',
                'total_pages',
                'count'
            ],
            'message', 'status', 'errors'
        ]);

        $response->assertJsonPath('data.total', 150);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.per_page', 15);
        $response->assertJsonPath('data.total_pages', 10);
        $response->assertJsonPath('data.count', 15);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->restaurants = Restaurant::factory()->count(150)->create([
            'user_id' => 1,
        ]);
    }
}
