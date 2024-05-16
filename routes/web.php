<?php

use App\Http\Controllers\Blade\RecipeController as BladeRecipeController;
use App\Http\Controllers\Inertia\PrivacyController;
use App\Http\Controllers\Inertia\Recipe\ImportController;
use App\Http\Controllers\Inertia\Recipe\RecipeController;
use App\Http\Controllers\Inertia\SearchController;
use App\Http\Controllers\Inertia\User\ChangePasswordController;
use App\Http\Controllers\Inertia\User\UserController;
use App\Http\Controllers\InertiaMinimal\RecipeController as InertiaMinimalRecipeController;
use App\Livewire\Recipe\Index;
use App\Livewire\Recipe\Show;
use Illuminate\Support\Facades\Route;

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

// START Inertia routes

Route::get('/', [RecipeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/recepten/toevoegen', [RecipeController::class, 'create'])->name('recipes.create');
  Route::post('/recepten', [RecipeController::class, 'store'])->name('recipes.store');
  Route::get('/recepten/{recipe}/bewerken', [RecipeController::class, 'edit'])->name('recipes.edit');
  Route::patch('/recepten/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
  Route::delete('/recepten/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

  Route::get('/recepten/importeren', [ImportController::class, 'index'])->name('import.index');
  Route::get('/recepten/importeren/controleren', [ImportController::class, 'create'])->name('import.create');
  Route::post('/recepten/importeren', [ImportController::class, 'store'])->name('import.store');
});

Route::get('/recepten/{slug}', [RecipeController::class, 'show'])
  ->name('recipes.show');

Route::get('/zoeken', [SearchController::class, 'index'])->name('search');
Route::get('/privacy', PrivacyController::class)->name('privacy');

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

// END Inertia routes

// START Blade routes

Route::get('/blade', [BladeRecipeController::class, 'index'])->name('blade.home');
Route::get('/blade/{slug}', [BladeRecipeController::class, 'show'])->name('blade.show');

// END Blade routes

// START Livewire routes

Route::get('/livewire', Index::class)->name('livewire.home');
Route::get('/livewire/{slug}', Show::class)->name('livewire.show');

// END Livewire routes

// START Inertia Minimal routes

Route::middleware([\App\Http\Middleware\Inertia\HandleInertiaMinimalRequests::class])
  ->withoutMiddleware([\App\Http\Middleware\Inertia\HandleInertiaRequests::class])
  ->group(function () {
    Route::get('/inertia-minimal', [InertiaMinimalRecipeController::class, 'index'])->name('inertia-minimal.home');
    Route::get('/inertia-minimal/{slug}', [InertiaMinimalRecipeController::class, 'show'])->name('inertia-minimal.show');
  });
// END Inertia Minimal routes
