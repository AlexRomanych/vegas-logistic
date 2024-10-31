<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
//use App\Http\Middleware\JwtMiddleware;
//use App\Http\Middleware\GuestsMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api_v1.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware) {
//        $middleware->append(JwtMiddleware::class);
//        $middleware->append(JwtMiddleware::class);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();



