<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plate>
 */
class PlateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => fn() => Restaurant::factory()->create(),
            'price'         => fake()->numberBetween(100, 1000),
            'name'          => fake()->words(2, true),
            'description'   => fake()->text,
        ];
    }
}
