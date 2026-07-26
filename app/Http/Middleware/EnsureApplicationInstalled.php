<?php

namespace App\Http\Middleware;

use App\Enums\InstallationState;
use App\Services\Installation\InstallationStateService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApplicationInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('installer.enforce', true)) {
            return $next($request);
        }
        $state = app(InstallationStateService::class)->state();
        if (in_array($state, [InstallationState::Installed, InstallationState::LegacyInstalled], true)) {
            return $next($request);
        }
        if ($state === InstallationState::Inconsistent) {
            return $request->expectsJson() ? response()->json(['message' => 'Installation state requires recovery.', 'state' => $state->value], 503) : response()->view('installer.recovery', compact('state'), 503);
        }

        return $request->expectsJson() ? response()->json(['message' => 'Application installation is required.', 'state' => $state->value], 409) : redirect()->route('install.welcome');
    }
}
