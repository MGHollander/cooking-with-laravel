<?php

use Hidehalo\Nanoid\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('public_id', 15)->nullable()->after('id');
        });

        $client = new Client();
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';

        DB::table('recipes')->orderBy('id')->chunk(100, function ($recipes) use ($client, $alphabet) {
            foreach ($recipes as $recipe) {
                DB::table('recipes')
                    ->where('id', $recipe->id)
                    ->update(['public_id' => $client->formattedId($alphabet, 15)]);
            }
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->string('public_id', 15)->nullable(false)->change();
            $table->unique('public_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};
