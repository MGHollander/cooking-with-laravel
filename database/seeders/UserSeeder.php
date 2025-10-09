<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $email = 'test@example.com';
        if (User::where('email', $email)->doesntExist()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => $email,
                'password' => Hash::make('password'),
            ]);
        }
    }
}
