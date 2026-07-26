<?php

namespace App\Support;

final class RuntimeDemoMode
{
    public static function enabled(): bool
    {
        return (bool) config('cms.runtime_demo_mode', false);
    }

    public static function abortIfProtected(string $message = 'This action is disabled in runtime demo mode.'): void
    {
        if (self::enabled()) {
            abort(403, $message);
        }
    }
}
