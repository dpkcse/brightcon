<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class InstallerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('installer.execution', function (Request $request): Limit {
            $attempts = max(1, (int) config('installer.execution_attempts', 5));
            $minutes = max(1, (int) config('installer.execution_decay_minutes', 10));
            $sessionIdentifier = $request->session()->get('installer.execution_id');
            if (! is_string($sessionIdentifier) || $sessionIdentifier === '') {
                $sessionIdentifier = (string) Str::uuid();
                $request->session()->put('installer.execution_id', $sessionIdentifier);
            }
            $key = hash('sha256', $request->ip().'|'.$sessionIdentifier);

            return Limit::perMinutes($minutes, $attempts)->by($key)
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 0);
                    $this->removeSecretsFromSession($request);

                    if ($request->expectsJson()) {
                        return response()->json(['message' => 'Too many installation attempts. Please wait and try again.', 'retry_after' => $retryAfter], 429, $headers);
                    }

                    return response()->view('installer.throttled', [
                        'retryAfter' => $retryAfter,
                        'returnRoute' => $request->session()->has('installer.safe') ? 'install.review' : 'install.welcome',
                    ], 429, $headers);
                });
        });
    }

    private function removeSecretsFromSession(Request $request): void
    {
        $oldInput = (array) $request->session()->get('_old_input', []);
        unset($oldInput['db_password'], $oldInput['admin_password'], $oldInput['admin_password_confirmation']);
        $request->session()->put('_old_input', $oldInput);
        $request->session()->forget(['db_password', 'admin_password', 'admin_password_confirmation']);
    }
}
