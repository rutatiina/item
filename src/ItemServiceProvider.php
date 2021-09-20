<?php

namespace Rutatiina\Item;

use Illuminate\Support\ServiceProvider;

class ItemServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        include __DIR__.'/routes/routes.php';
        include __DIR__.'/routes/api.php';

        $this->loadViewsFrom(__DIR__.'/resources/views', 'item');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->publishes([
            __DIR__.'/resources/public' => public_path('storage'),
        ], 'rutatiina/item');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->make('Rutatiina\Item\Http\Controllers\ItemController');
    }
}
