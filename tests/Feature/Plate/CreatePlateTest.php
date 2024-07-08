<?php

namespace Tests\Feature\Plate;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreatePlateTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    #[Test]
    public function an_autheticated_user_can_create_a_plate(): void
    {
        //$this->withoutExceptionHandling();
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '$123',
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['plate' => ['id', 'restaurant_id', 'name', 'description', 'price', 'image']],
            'message', 'status', 'errors'
        ]);
        $plate = Plate::find(1);
        $response->assertJsonFragment([
            'data' => [
                'plate' => [
                    ...$data,
                    'id'            => 1,
                    'restaurant_id' => $this->restaurant->id,
                    'links'         => ['parent' => route('restaurants.show', $this->restaurant)],
                    'image' => $plate->image,
                ]
            ]
        ]);
        $this->assertDatabaseHas('plates', [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '$123']);
        $this->assertDatabaseMissing('plates', [
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ]);
        $this->assertNotNull($plate->image);
    }

    #[Test]
    public function a_unautheticated_user_cannot_update_a_plate(): void
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '$123',
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ];

        #haciendo
        $response =
            $this->postJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);
        $response->assertStatus(401);
    }

    #[Test]
    public function a_autheticated_user_can_only_update_their_plates(): void
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '$123',
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ];
        $user = User::factory()->create();
        #haciendo
        $response =
            $this->apiAs($user, 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);
        $response->assertStatus(403);
    }

    #[Test]
    public function plate_name_is_required()
    {
        #teniendo
        $data = [
            'name'        => '',
            'description' => 'Description test',
            'price'       => '$123',
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name']]);
    }

    #[Test]
    public function plate_description_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => '',
            'price'       => '$123',
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['description']]);
    }

    #[Test]
    public function plate_price_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '',
            'image' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyAQMAAAAk8RryAAAAAXNSR0IB2cksfwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAANQTFRFAAAAp3o92gAAAA1JREFUGBljGAWDCgAAAZAAAcH2qj4AAAAASUVORK5CYII='
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price']]);
    }

    #[Test]
    public function plate_image_is_required()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '123',
            'image'       => '',
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['image']]);
    }

    #[Test]
    public function plate_image_must_be_valid()
    {
        #teniendo
        $data = [
            'name'        => 'Name test',
            'description' => 'Description test',
            'price'       => '123',
            'image'       => '1234',
        ];

        #haciendo
        $response =
            $this->apiAs(User::find(1), 'post', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['image']]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1
        ]);
    }
}
