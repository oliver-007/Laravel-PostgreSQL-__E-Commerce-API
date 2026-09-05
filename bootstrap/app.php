<?php

use App\Exceptions\OutOfStockException; // add this import
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Register custom middleware aliases
        $middleware->alias([
            'auth' => Authenticate::class,
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Add this block
        $exceptions->render(function (OutOfStockException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        });
    })->create();