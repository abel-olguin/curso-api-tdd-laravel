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

class CreateMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User       $user;
    protected Restaurant $restaurant;
    protected Collection $plates;

    #[Test]
    public function an_authenticated_user_can_create_a_menu(): void
    {
        $this->withoutExceptionHandling();
        $data     = [
            'name'        => 'menu name',
            'description' => 'menu description',
            'plate_ids'   => $this->plates->pluck('id'),
        ];
        $response =
            $this->apiAs($this->user, 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'menu' => [
                    'id', 'name', 'description', 'plates' => [
                        '*' => ['name', 'description', 'price']
                    ]
                ]
            ],
            'message', 'errors', 'status'
        ]);

        $firstPlate = $this->plates->first();

        $response->assertJsonPath('data.menu.plates.0', [
            'name'        => $firstPlate->name,
            'description' => $firstPlate->description,
            'price'       => (string)$firstPlate->price,
        ]);

        $this->assertDatabaseHas('menus', [
            'restaurant_id' => $this->restaurant->id,
            'name'          => 'menu name',
            'description'   => 'menu description',
        ]);

        foreach ($this->plates as $plate) {
            $this->assertDatabaseHas('menus_plates', [
                'menu_id'  => 1,
                'plate_id' => $plate->id,
            ]);
        }

    }

    #[Test]
    public function menu_plates_should_not_be_duplicates(): void
    {
        $this->withoutExceptionHandling();
        $data     = [
            'name'        => 'menu name',
            'description' => 'menu description',
            'plate_ids'   => [$this->plates->first()->id, $this->plates->first()->id],
        ];
        $response =
            $this->apiAs($this->user, 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);
        $response->assertStatus(200);

        $this->assertDatabaseCount('menus_plates', 1);
        $this->assertTrue(Menu::first()->plates()->count() == 1);

    }

    #[Test]
    public function a_unauthenticated_user_cannot_create_a_menu(): void
    {
        //$this->withoutExceptionHandling();
        $data     = [
            'name'        => 'new menu name',
            'description' => 'new menu description',
            'plate_ids'   => $this->plates->pluck('id'),
        ];
        $response =
            $this->postJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);
        $response->assertStatus(401);
    }

    #[Test]
    public function menu_name_is_required()
    {
        #teniendo
        $data = [
            'name'        => '',
            'description' => 'Description test',
            'plate_ids'   => $this->plates->pluck('id'),
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name']]);
    }

    #[Test]
    public function menu_description_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => '',
            'plate_ids'   => $this->plates->pluck('id'),
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['description']]);
    }

    #[Test]
    public function menu_plates_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'plate_ids'   => [],
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['plate_ids']]);
    }

    #[Test]
    public function menu_plates_must_exists()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'plate_ids'   => [100],
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['plate_ids.0']]);
    }

    #[Test]
    public function restaurant_must_belongs_to_user()
    {
        //$this->withoutExceptionHandling();
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'plate_ids'   => [1],
        ];

        #haciendo
        $user     = User::factory()->create();
        $response =
            $this->apiAs($user, 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);
        $response->assertStatus(403);
    }

    #[Test]
    public function menu_plates_must_belongs_to_user()
    {
        //$this->withoutExceptionHandling();
        $plate = Plate::factory()->create();

        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'plate_ids'   => [$plate->id],
        ];

        #haciendo
        $response =
            $this->apiAs($this->user, 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus", $data);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['plate_ids.0']]);
    }

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->user       = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plates     = Plate::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
    }
}
