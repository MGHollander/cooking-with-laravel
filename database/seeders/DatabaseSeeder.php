<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment('production')) {
            if (!$this->command->confirm('Are you sure you want to seed on production?')) {
                return;
            }
        }

        $this->call([
            UserSeeder::class,
            RecipeSeeder::class,
        ]);
    }
}
