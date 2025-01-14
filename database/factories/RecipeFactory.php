<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'        => $this->faker->sentence,
            'user_id'      => 1,
            'servings'     => $this->faker->numberBetween(1, 10),
            'difficulty'   => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'ingredients'  => $this->faker->paragraph,
            'instructions' => $this->faker->paragraph,
        ];
    }
}
