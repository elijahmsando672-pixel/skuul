<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')->group(base_path('routes/saas.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant' => \App\Http\Middleware\ResolveTenant::class,
            'platform' => \App\Http\Middleware\EnsurePlatformAdmin::class,
            'subscription' => \App\Http\Middleware\EnsureActiveSubscription::class,
        ]);

        $middleware->web(append: [\App\Http\Middleware\ResolveTenant::class]);
        $middleware->api(append: [\App\Http\Middleware\ResolveTenant::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();