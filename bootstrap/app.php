<?php

use App\Http\Middleware\EnsureIsCreator;
use App\Http\Middleware\ResolveWorkspace;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'workspace' => ResolveWorkspace::class,
            'creator' => EnsureIsCreator::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetAppLocale::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
            'api/webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => class_basename($e),
                    'message' => $e->getMessage(),
                ], 400);
            }

            return null;
        });
    })->create();
