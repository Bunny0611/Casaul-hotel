<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_PROTO
        );

        $middleware->redirectGuestsTo(function (Request $request): string {
            if ($request->is('guest/*') || $request->is('reservation*')) {
                return route('home', ['auth' => 'signin']);
            }

            return route('login');
        });

        $middleware->alias([
            'role' => CheckRole::class,
        ]);
        $middleware->validateCsrfTokens(except: ['logout']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
