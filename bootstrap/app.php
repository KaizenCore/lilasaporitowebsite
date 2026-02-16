<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// FORCE file-based storage to prevent SQLite errors
// This overrides .env until proper database is configured
if (!isset($_ENV['SESSION_DRIVER'])) {
    $_ENV['SESSION_DRIVER'] = 'file';
    putenv('SESSION_DRIVER=file');
}
if (!isset($_ENV['CACHE_STORE'])) {
    $_ENV['CACHE_STORE'] = 'file';
    putenv('CACHE_STORE=file');
}
if (!isset($_ENV['QUEUE_CONNECTION'])) {
    $_ENV['QUEUE_CONNECTION'] = 'sync';
    putenv('QUEUE_CONNECTION=sync');
}

// If in production and no DB_CONNECTION set, default to pgsql
if (($_ENV['APP_ENV'] ?? 'production') === 'production' && !isset($_ENV['DB_CONNECTION'])) {
    $_ENV['DB_CONNECTION'] = 'pgsql';
    putenv('DB_CONNECTION=pgsql');
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);

        // Trust all proxies (Dokploy/Traefik) so Laravel generates HTTPS URLs
        $middleware->validateCsrfTokens(except: ['webhook/*']);
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
