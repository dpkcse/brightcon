<?php

namespace App\Licensing;

use App\Contracts\Licensing\LicenseProvider;
use App\Licensing\Exceptions\LicenseProviderException;
use Illuminate\Contracts\Container\Container;

final class ProviderRegistry
{
    public function __construct(private Container $container) {}

    public function provider(string $id): LicenseProvider
    {
        $definition = config("licensing.providers.{$id}");

        if (! is_array($definition)) {
            throw new LicenseProviderException("Unknown license provider [{$id}].");
        }

        if (! ($definition['operational'] ?? false) || empty($definition['adapter'])) {
            throw new LicenseProviderException("License provider [{$id}] is declared but is not operational.");
        }

        $adapter = $this->container->make($definition['adapter']);
        if (! $adapter instanceof LicenseProvider || $adapter->id() !== $id) {
            throw new LicenseProviderException("Invalid adapter configured for license provider [{$id}].");
        }

        return $adapter;
    }

    public function definitions(): array
    {
        return config('licensing.providers', []);
    }
}
