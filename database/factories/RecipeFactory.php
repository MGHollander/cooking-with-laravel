<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
    public function definition()
    {
        $title = ucfirst(fake()->words(rand(4, 8), true));

        return [
            'user_id' => User::all()->first(),
            'title' => $title,
            'slug' => Str::of($title)->slug('-'),
            'image' => fake()->imageUrl(640, 480),
        ];
    }
}
