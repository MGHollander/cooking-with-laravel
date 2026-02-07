<?php

use App\Http\Controllers\Recipe\ImportController;
use App\Http\Controllers\Recipe\RecipeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\UserController;
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

Route::get('/', [RecipeController::class, 'index'])->name('home');
Route::get('/recepten', function () {
    return redirect()->route('home');
});
Route::get('/recipes', function () {
    return redirect()->route('home');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/recepten/toevoegen', [RecipeController::class, 'create'])->name('recipes.create.nl');
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create.en');
    Route::post('/recepten', [RecipeController::class, 'store'])->name('recipes.store.nl');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store.en');
    Route::get('/recepten/{recipe}/bewerken', [RecipeController::class, 'edit'])->name('recipes.edit.nl');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit.en');
    Route::patch('/recepten/{recipe}', [RecipeController::class, 'update'])->name('recipes.update.nl');
    Route::patch('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update.en');
    Route::delete('/recepten/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy.nl');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy.en');

    Route::get('/recepten/importeren', [ImportController::class, 'index'])->name('import.index.nl');
    Route::get('/recipes/import', [ImportController::class, 'index'])->name('import.index.en');
    Route::get('/recepten/importeren/controleren', [ImportController::class, 'create'])->name('import.create.nl');
    Route::get('/recipes/import/review', [ImportController::class, 'create'])->name('import.create.en');
    Route::post('/recepten/importeren/import-recipe', [ImportController::class, 'importRecipe'])->name('import.import-recipe.nl');
    Route::post('/recipes/import/recipe', [ImportController::class, 'importRecipe'])->name('import.import-recipe.en');
    Route::post('/recepten/importeren', [ImportController::class, 'store'])->name('import.store.nl');
    Route::post('/recipes/import', [ImportController::class, 'store'])->name('import.store.en');
    Route::get('/recepten/importeren/proxy-image', [ImportController::class, 'proxyImage'])->name('import.proxy-image.nl');
    Route::get('/recipes/import/proxy-image', [ImportController::class, 'proxyImage'])->name('import.proxy-image.en');
});

Route::get('/recepten/{public_id}/{slug?}', [RecipeController::class, 'show'])
    ->where('public_id', '[a-z0-9]+')
    ->where('slug', '[a-z0-9-]+')
    ->name('recipes.show.nl');
Route::get('/recipes/{public_id}/{slug?}', [RecipeController::class, 'show'])
    ->where('public_id', '[a-z0-9]+')
    ->where('slug', '[a-z0-9-]+')
    ->name('recipes.show.en');

Route::get('/recepten/{slug}', [RecipeController::class, 'showLegacy'])
    ->where('slug', '[a-z0-9-]+')
    ->name('recipes.show.legacy.nl');
Route::get('/recipes/{slug}', [RecipeController::class, 'showLegacy'])
    ->where('slug', '[a-z0-9-]+')
    ->name('recipes.show.legacy.en');

Route::get('/over-mij', function () {
    return view('kocina.about-me');
})->name('about-me.nl');
Route::get('/about-me', function () {
    return view('kocina.about-me');
})->name('about-me.en');

Route::get('/privacy', function () {
    return view('kocina.privacy');
})->name('privacy.nl');
Route::get('/privacy-policy', function () {
    return view('kocina.privacy');
})->name('privacy.en');

Route::get('/zoeken', [SearchController::class, 'index'])->name('search.nl');
Route::get('/search', [SearchController::class, 'index'])->name('search.en');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/gebruikers/wachtwoord-wijzigen', [ChangePasswordController::class, 'edit'])->name('users.password.edit.nl');
    Route::get('/users/change-password', [ChangePasswordController::class, 'edit'])->name('users.password.edit.en');

    Route::post('/gebruikers/wachtwoord-wijzigen', [ChangePasswordController::class, 'update'])->name('users.password.update.nl');
    Route::post('/users/change-password', [ChangePasswordController::class, 'update'])->name('users.password.update.en');

    Route::get('/gebruikers', [UserController::class, 'index'])->name('users.index.nl');
    Route::get('/users', [UserController::class, 'index'])->name('users.index.en');

    Route::get('/gebruikers/toevoegen', [UserController::class, 'create'])->name('users.create.nl');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create.en');

    Route::get('/gebruikers/{user}', [UserController::class, 'show'])->name('users.show.nl');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show.en');

    Route::post('/gebruikers', [UserController::class, 'store'])->name('users.store.nl');
    Route::post('/users', [UserController::class, 'store'])->name('users.store.en');

    Route::get('/gebruikers/{user}/bewerken', [UserController::class, 'edit'])->name('users.edit.nl');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit.en');

    Route::patch('/gebruikers/{user}', [UserController::class, 'update'])->name('users.update.nl');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update.en');

    Route::delete('/gebruikers/{user}', [UserController::class, 'destroy'])->name('users.destroy.nl');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy.en');
});

Route::post('/locale', [App\Http\Controllers\LocaleController::class, 'update'])->name('locale.update');

require __DIR__.'/auth.php';
