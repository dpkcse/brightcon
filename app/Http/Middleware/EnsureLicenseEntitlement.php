<?php

namespace App\Http\Middleware;

use App\Services\Licensing\LicensePolicyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureLicenseEntitlement
{
    public function handle(Request $request, Closure $next, string $action): Response
    {
        $decision = app(LicensePolicyService::class)->decisionFor($action);
        if ($decision->allowed) {
            return $next($request);
        }

        $message = 'A valid product entitlement is required for this action.';

        return $request->expectsJson()
            ? response()->json(['message' => $message, 'action' => $action], Response::HTTP_FORBIDDEN)
            : abort(Response::HTTP_FORBIDDEN, $message);
    }
}
