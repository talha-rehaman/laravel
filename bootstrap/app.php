<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\Passport;
use App\Providers\AuthServiceProvider; // ✅ Import your provider

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withProviders([
        AuthServiceProvider::class, // ✅ Register your provider here
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
