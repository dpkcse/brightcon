<?php

namespace App\Http\Middleware;

use App\Services\Installation\InstallationStateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $state = app(InstallationStateService::class);
        if ($state->isInstalled()) {
            if ($request->routeIs('install.complete') && $request->session()->has('installer.complete')) {
                return $next($request);
            }
            if ($request->routeIs('install.process.redirect') && $request->session()->has('installer.complete')) {
                return redirect()->route('install.complete');
            }

            return redirect()->route('admin.login');
        }
        if (! $state->canRunInstaller()) {
            return response()->view('installer.recovery', ['state' => $state->state()], 503);
        }

        return $next($request);
    }
}
