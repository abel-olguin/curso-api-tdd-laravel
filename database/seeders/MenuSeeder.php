<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::with('plates')->get();

        foreach ($restaurants as $restaurant) {
            Menu::factory()->count(45)
                ->hasAttached(
                    Plate::factory()->count(5)->create(['restaurant_id' => $restaurant->id])
                )->create([
                    'restaurant_id' => $restaurant->id,
                ]);
        }
    }
}
