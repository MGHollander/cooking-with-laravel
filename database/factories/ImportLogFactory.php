<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImportLog>
 */
class ImportLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
            'source' => $this->faker->randomElement(['structured-data', 'firecrawl', 'open-ai']),
            'user_id' => User::factory(),
            'recipe_id' => null, // Default to null, can be overridden
            'parsed_data' => [
                'title' => $this->faker->sentence(3),
                'description' => $this->faker->paragraph(),
                'keywords' => $this->faker->words(3, true),
                'ingredients' => $this->faker->sentences(5),
                'steps' => $this->faker->sentences(8),
                'prepTime' => 'PT' . $this->faker->numberBetween(5, 60) . 'M',
                'cookTime' => 'PT' . $this->faker->numberBetween(15, 120) . 'M',
                'totalTime' => 'PT' . $this->faker->numberBetween(30, 180) . 'M',
                'yield' => $this->faker->numberBetween(2, 8),
                'difficulty' => $this->faker->randomElement(['easy', 'average', 'difficult']),
                'images' => [$this->faker->imageUrl()],
                'url' => $this->faker->url(),
            ],
        ];
    }

    /**
     * Indicate that the import log has an associated recipe.
     */
    public function withRecipe(): static
    {
        return $this->state(fn (array $attributes) => [
            'recipe_id' => Recipe::factory(),
        ]);
    }
}
