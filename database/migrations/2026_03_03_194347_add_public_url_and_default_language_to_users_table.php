<?php

use App\Models\User;
use Hidehalo\Nanoid\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('public_url', 50)->nullable()->unique()->after('email');
            $table->string('default_language', 2)->default('nl')->after('public_url');
        });

        // Generate public_url for existing users
        $client = new Client;
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';

        User::withTrashed()->each(function (User $user) use ($client, $alphabet) {
            $suffix = $client->formattedId($alphabet, 10);
            $name = Str::slug($user->name);
            $name = Str::limit($name, 39, '');
            $user->update([
                'public_url' => $name.'-'.$suffix,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('public_url', 50)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['public_url', 'default_language']);
        });
    }
};
