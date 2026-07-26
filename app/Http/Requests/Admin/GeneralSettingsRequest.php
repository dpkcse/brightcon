<?php

namespace App\Http\Requests\Admin;

use App\Support\GoogleMapsUrl;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_contact_map' => $this->boolean('show_contact_map'),
            'google_map_embed_url' => GoogleMapsUrl::extractEmbedUrl($this->input('google_map_embed_url')),
        ]);
    }

    public function rules(): array
    {
        return [
            'company_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,webp', 'max:1024'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'profile_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'default_language' => ['nullable', 'string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'developer_name' => ['nullable', 'string', 'max:255'],
            'developer_link' => ['nullable', 'url', 'max:255'],
            'show_contact_map' => ['required', 'boolean'],
            'google_map_embed_url' => [
                'nullable',
                'url',
                'max:2048',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! GoogleMapsUrl::isTrustedEmbedUrl($value)) {
                        $fail('The Google Maps embed URL must be an HTTPS URL hosted by Google Maps.');
                    }
                },
            ],
            'map_location_name' => ['nullable', 'string', 'max:255'],
            'map_address' => ['nullable', 'string', 'max:500'],
            'map_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'map_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_zoom' => ['nullable', 'integer', 'between:1,20'],
        ];
    }
}
