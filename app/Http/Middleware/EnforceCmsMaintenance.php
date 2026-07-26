<?php

namespace App\Http\Middleware;

use App\Contracts\SettingsRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceCmsMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(SettingsRepositoryInterface::class);
        if ($settings->string('website_status', 'active') === 'maintenance') {
            return response()->view('frontend.maintenance', ['message' => $settings->string('maintenance_message')], 503);
        }

        return $next($request);
    }
}
