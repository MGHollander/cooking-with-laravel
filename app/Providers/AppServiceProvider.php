<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Enable Laravel's strict mode for development
        // @see https://coderflex.com/blog/laravel-strict-mode-all-what-you-need-to-know#all-in-one
        Model::shouldBeStrict(!$this->app->isProduction());
    }
}
