<?php

namespace App\Licensing\Providers;

use App\Contracts\Licensing\LicenseProvider;
use App\Contracts\Licensing\ProviderCapabilities;
use App\Enums\LicenseStatus;
use App\Licensing\Data\ActivationRequest;
use App\Licensing\Data\LicenseDecision;
use DateTimeImmutable;
use JsonException;

final class OfflineSignedLicenseProvider implements LicenseProvider
{
    public function id(): string
    {
        return 'offline';
    }

    public function capabilities(): ProviderCapabilities
    {
        return new ProviderCapabilities(true, false, false, true);
    }

    public function activate(ActivationRequest $request): LicenseDecision
    {
        $parts = explode('.', trim($request->credential));
        $publicKey = $this->decode((string) config('licensing.offline.public_key'));
        if (count($parts) !== 2 || $publicKey === false || openssl_pkey_get_public($publicKey) === false) {
            return $this->invalid('The offline license or verification key is malformed.');
        }

        [$encodedPayload, $encodedSignature] = $parts;
        $payload = $this->decode($encodedPayload);
        $signature = $this->decode($encodedSignature);
        if ($payload === false || $signature === false || openssl_verify($payload, $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
            return $this->invalid('The offline license signature is invalid.');
        }

        try {
            $claims = json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->invalid('The offline license payload is invalid.');
        }

        if (! is_array($claims) || ! hash_equals($request->product, (string) ($claims['product'] ?? ''))) {
            return $this->invalid('The offline license is for another product.');
        }

        $hosts = $claims['hosts'] ?? [];
        if ($hosts !== [] && (! is_array($hosts) || array_filter($hosts, 'is_string') !== $hosts || ! in_array($this->normalizeHost($request->host), array_map([$this, 'normalizeHost'], $hosts), true))) {
            return $this->invalid('The offline license is not valid for this host.');
        }

        $expiresAt = isset($claims['expires_at']) ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, (string) $claims['expires_at']) : null;
        if (isset($claims['expires_at']) && $expiresAt === false) {
            return $this->invalid('The offline license expiry is invalid.');
        }

        $status = $expiresAt !== null && $expiresAt <= new DateTimeImmutable ? LicenseStatus::Expired : LicenseStatus::Active;

        return new LicenseDecision($status, (string) ($claims['license_id'] ?? ''), $expiresAt ?: null, ['entitlements' => $claims['entitlements'] ?? []]);
    }

    private function invalid(string $reason): LicenseDecision
    {
        return new LicenseDecision(LicenseStatus::Invalid, reason: $reason);
    }

    private function decode(string $value): string|false
    {
        $value = strtr($value, '-_', '+/');
        $value .= str_repeat('=', (4 - strlen($value) % 4) % 4);

        return base64_decode($value, true);
    }

    private function normalizeHost(string $host): string
    {
        return strtolower(rtrim(trim($host), '.'));
    }
}
