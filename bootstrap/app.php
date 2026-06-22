<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ── Named middleware aliases ─────────────────────────────
        $middleware->alias([
            'super_admin'      => \App\Http\Middleware\SuperAdminMiddleware::class,
            'admin'            => \App\Http\Middleware\AdminMiddleware::class,
            'role'             => \App\Http\Middleware\RoleMiddleware::class,
            'no.cache'         => \App\Http\Middleware\NoCacheMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        // ── Append security headers to every web response ────────
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
