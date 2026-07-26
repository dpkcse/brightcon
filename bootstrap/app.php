<?php

use App\Contracts\InstallationStateInterface;
use App\Http\Middleware\EnforceCmsMaintenance;
use App\Http\Middleware\EnsureApplicationInstalled;
use App\Http\Middleware\EnsureApplicationNotInstalled;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Providers\FrontendViewServiceProvider;
use App\Services\Installation\InstallationStateService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

// Laravel's encrypted CSRF/session cookies need a key before the web installer can
// create the permanent .env. Keep this generated bootstrap key server-side only;
// the installation pipeline persists it and removes the temporary file last.
if (! getenv('APP_KEY')) {
    $temporaryKeyPath = dirname(__DIR__).'/storage/app/.installer-key';
    $temporaryKey = is_file($temporaryKeyPath) ? trim((string) file_get_contents($temporaryKeyPath)) : '';
    if (! str_starts_with($temporaryKey, 'base64:')) {
        $temporaryKey = 'base64:'.base64_encode(random_bytes(32));
        if (is_dir(dirname($temporaryKeyPath)) && is_writable(dirname($temporaryKeyPath))) {
            file_put_contents($temporaryKeyPath, $temporaryKey, LOCK_EX);
            @chmod($temporaryKeyPath, 0600);
        }
    }
    putenv('APP_KEY='.$temporaryKey);
    $_ENV['APP_KEY'] = $_SERVER['APP_KEY'] = $temporaryKey;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')->group(base_path('routes/install.php'));
            Route::middleware('web')->group(base_path('routes/admin.php'));
        },
    )
    ->withProviders([
        FrontendViewServiceProvider::class,
    ])
    ->withBindings([InstallationStateInterface::class => InstallationStateService::class])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->alias(['admin' => EnsureUserIsAdmin::class, 'cms.maintenance' => EnforceCmsMaintenance::class, 'cms.installed' => EnsureApplicationInstalled::class, 'install.open' => EnsureApplicationNotInstalled::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
