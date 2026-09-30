<?php

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
        // Apache on the host owns :80 and reverse-proxies to this container.
        // The container publishes only on 127.0.0.1 (see compose.yaml), so the
        // sole possible client is that proxy -- but the request arrives from the
        // Docker bridge gateway, not 127.0.0.1, so a loopback-pinned list would
        // never match. Trusting all proxies is safe given the loopback binding.
        $middleware->trustProxies(at: '*');

        // Lets the React SPA authenticate against routes/api.php with the
        // session cookie (Sanctum SPA authentication) instead of API tokens.
        $middleware->statefulApi();

        // The admin login route is `admin.login`, not the framework default
        // `login`, so guests would otherwise hit a missing-route error. JSON
        // requests get a 401 instead of this redirect.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
