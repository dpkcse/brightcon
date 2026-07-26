<?php

namespace App\Http\Middleware;

use App\Licensing\LicenseManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireValidLicense
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('licensing.enforce', false)) {
            return $next($request);
        }

        if (! app(LicenseManager::class)->permitsUse()) {
            abort(Response::HTTP_SERVICE_UNAVAILABLE, 'A valid product license is required.');
        }

        return $next($request);
    }
}
