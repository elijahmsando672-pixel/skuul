<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
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

$testBase = dirname(__DIR__).DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'testing';
$testCache = $testBase.DIRECTORY_SEPARATOR.'cache';
if (($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? null) === 'testing' || getenv('APP_ENV') === 'testing') {
    foreach ([$testBase,$testCache,$testCache.DIRECTORY_SEPARATOR.'views',$testCache.DIRECTORY_SEPARATOR.'data',$testBase.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'sessions',$testBase.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'views',$testBase.DIRECTORY_SEPARATOR.'logs'] as $dir) {
        if (! is_dir($dir)) { @mkdir($dir, 0777, true); }
    }
    $app->addAbsoluteCachePathPrefix($testBase);
    foreach ([
        'APP_SERVICES_CACHE' => $testCache.DIRECTORY_SEPARATOR.'services.php',
        'APP_PACKAGES_CACHE' => $testCache.DIRECTORY_SEPARATOR.'packages.php',
        'APP_CONFIG_CACHE' => $testCache.DIRECTORY_SEPARATOR.'config.php',
        'APP_ROUTES_CACHE' => $testCache.DIRECTORY_SEPARATOR.'routes.php',
        'APP_EVENTS_CACHE' => $testCache.DIRECTORY_SEPARATOR.'events.php',
    ] as $k => $v) { $_ENV[$k] = $v; $_SERVER[$k] = $v; putenv($k.'='.$v); }
    $app->useStoragePath($testBase);
}

return $app;