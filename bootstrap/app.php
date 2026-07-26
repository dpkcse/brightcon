<?php

use App\Http\Middleware\EnforceCmsMaintenance;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Providers\FrontendViewServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withProviders([
        FrontendViewServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->alias(['admin' => EnsureUserIsAdmin::class, 'cms.maintenance' => EnforceCmsMaintenance::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
