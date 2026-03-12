<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // If you are using Laravel 10+, Passport::routes() is removed.
        // You need to define the routes in your routes/api.php file.
        // Remove the following line if you are on Laravel 10+:
        // Passport::routes();

    }
}
