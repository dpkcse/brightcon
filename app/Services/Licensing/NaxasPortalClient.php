<?php

namespace App\Services\Licensing;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

final class NaxasPortalClient
{
    public function create(array $payload): array
    {
        return $this->send('post', $this->url((string) config('licensing.naxas_portal.request_path')), $payload, [
            'request_id' => ['required', 'string', 'max:191'],
            'request_token' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending'],
            'expires_at' => ['required', 'date', 'after:now'],
            'portal_url' => ['required', 'url', 'max:2048'],
        ]);
    }

    public function status(string $requestId, string $requestToken): array
    {
        $path = str_replace('{request_id}', rawurlencode($requestId), (string) config('licensing.naxas_portal.status_path'));

        return $this->send('post', $this->url($path), ['request_token' => $requestToken], [
            'status' => ['required', 'in:pending,approved,rejected,expired,completed'],
            'signed_license' => ['required_if:status,approved', 'string', 'max:100000'],
            'failure_code' => ['nullable', 'string', 'max:64'],
            'safe_message' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function validatePortalUrl(string $url): void
    {
        $base = $this->trustedBaseUrl();
        if (parse_url($url, PHP_URL_SCHEME) !== parse_url($base, PHP_URL_SCHEME)
            || ! hash_equals((string) parse_url($base, PHP_URL_HOST), (string) parse_url($url, PHP_URL_HOST))
            || parse_url($url, PHP_URL_PORT) !== parse_url($base, PHP_URL_PORT)
            || parse_url($url, PHP_URL_USER) !== null || parse_url($url, PHP_URL_QUERY) !== null || parse_url($url, PHP_URL_FRAGMENT) !== null) {
            throw new RuntimeException('The license service returned an untrusted portal address.');
        }
    }

    private function send(string $method, string $url, array $payload, array $rules): array
    {
        try {
            $response = Http::acceptJson()->asJson()
                ->timeout(min(30, max(1, (int) config('licensing.naxas_portal.timeout_seconds', 10))))
                ->connectTimeout(min(15, max(1, (int) config('licensing.naxas_portal.connect_timeout_seconds', 5))))
                ->retry(min(2, max(0, (int) config('licensing.naxas_portal.retries', 1))), 200, throw: false)
                ->withOptions(['allow_redirects' => false])
                ->{$method}($url, $payload);
        } catch (ConnectionException) {
            throw new RuntimeException('The Naxas license service is temporarily unavailable. You can continue using the core CMS and retry activation later.');
        }

        if (! $response->successful() || ! is_array($response->json())) {
            throw new RuntimeException('The Naxas license service is temporarily unavailable. You can continue using the core CMS and retry activation later.');
        }
        $data = $response->json();
        if (Validator::make($data, $rules)->fails()) {
            throw new RuntimeException('The license service returned an invalid response. No activation data was changed.');
        }

        return $data;
    }

    private function url(string $path): string
    {
        return rtrim($this->trustedBaseUrl(), '/').'/'.ltrim($path, '/');
    }

    private function trustedBaseUrl(): string
    {
        $url = (string) config('licensing.naxas_portal.base_url');
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);
        $localHttp = app()->environment(['local', 'testing']) && (bool) config('licensing.naxas_portal.allow_http_local');
        if (! is_string($host) || $host === '' || ($scheme !== 'https' && ! ($scheme === 'http' && $localHttp))
            || parse_url($url, PHP_URL_USER) !== null || parse_url($url, PHP_URL_QUERY) !== null || parse_url($url, PHP_URL_FRAGMENT) !== null) {
            throw new RuntimeException('The configured Naxas license service endpoint is not trusted.');
        }

        return $url;
    }
}
