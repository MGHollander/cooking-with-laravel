<?php

use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [RecipeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/recepten/toevoegen', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recepten', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recepten/{recipe}/bewerken', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::patch('/recepten/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recepten/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');
});

Route::get('/recepten/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');

Route::get('/zoeken', [SearchController::class, 'index'])->name('search');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/gebruikers/wachtwoord-wijzigen', [ChangePasswordController::class, 'edit'])->name('users.password.edit');
    Route::post('/gebruikers/wachtwoord-wijzigen', [ChangePasswordController::class, 'update'])->name('users.password.update');

    Route::get('/gebruikers', [UserController::class, 'index'])->name('users.index');
    Route::get('/gebruikers/toevoegen', [UserController::class, 'create'])->name('users.create');
    Route::get('/gebruikers/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/gebruikers', [UserController::class, 'store'])->name('users.store');
    Route::get('/gebruikers/{user}/bewerken', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/gebruikers/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/gebruikers/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
