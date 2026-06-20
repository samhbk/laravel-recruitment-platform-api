<?php

use App\Domain\Exceptions\DomainException;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\OptionalJwtAuthentication;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'jwt.optional' => OptionalJwtAuthentication::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (DomainException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
        });
    })->create();
