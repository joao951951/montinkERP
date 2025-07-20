<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Cart\CartManager;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton('cart', function($app) {
            return new CartManager();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
