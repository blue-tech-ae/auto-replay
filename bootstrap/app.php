<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsurePageBelongsToUser;
use App\Http\Middleware\VerifyFacebookSignature;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'fb.verify_signature' => VerifyFacebookSignature::class,
            'page.owner' => EnsurePageBelongsToUser::class,
        ]);

        $middleware->appendToGroup('api', [
            EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
