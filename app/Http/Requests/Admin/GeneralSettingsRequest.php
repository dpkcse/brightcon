<?php

namespace App\Http\Requests\Admin;

use App\Support\GoogleMapsUrl;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'show_contact_map' => $this->boolean('show_contact_map'),
            'show_powered_by' => $this->boolean('show_powered_by'),
            'contact_form_enabled' => $this->boolean('contact_form_enabled'),
            'contact_phone_enabled' => $this->boolean('contact_phone_enabled'),
            'contact_subject_enabled' => $this->boolean('contact_subject_enabled'),
            'organization_schema_enabled' => $this->boolean('organization_schema_enabled'),
            'google_map_embed_url' => GoogleMapsUrl::extractEmbedUrl($this->input('google_map_embed_url')),
            'website_status' => $this->input('website_status', 'active'),
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
            'default_language' => ['nullable', 'regex:/^[a-z]{2}(?:-[A-Z]{2})?$/'],
            'timezone' => ['nullable', 'timezone:all'],
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
            'company_short_name' => ['nullable', 'string', 'max:100'], 'company_description' => ['nullable', 'string', 'max:3000'],
            'dark_logo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], 'light_logo_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'powered_by_text' => ['nullable', 'string', 'max:255'], 'show_powered_by' => ['required', 'boolean'],
            'date_format' => ['nullable', 'in:M d, Y,d M Y,Y-m-d,d/m/Y,m/d/Y'], 'website_status' => ['required', 'in:active,maintenance'],
            'maintenance_message' => ['nullable', 'string', 'max:1000', 'not_regex:/[<>]/'],
            'secondary_address' => ['nullable', 'string', 'max:1000'], 'secondary_phone' => ['nullable', 'regex:/^[0-9+().\-\s]{3,50}$/'],
            'whatsapp_number' => ['nullable', 'regex:/^[0-9+().\-\s]{3,50}$/'], 'secondary_email' => ['nullable', 'email', 'max:255'],
            'business_hours' => ['nullable', 'string', 'max:1000', 'not_regex:/[<>]/'], 'contact_recipient_email' => ['nullable', 'email', 'max:255'],
            'contact_form_enabled' => ['required', 'boolean'], 'contact_phone_enabled' => ['required', 'boolean'], 'contact_subject_enabled' => ['required', 'boolean'],
            'contact_success_message' => ['nullable', 'string', 'max:500', 'not_regex:/[<>]/'], 'contact_failure_message' => ['nullable', 'string', 'max:500', 'not_regex:/[<>]/'],
            'contact_email_subject_prefix' => ['nullable', 'string', 'max:100', 'not_regex:/[<>]/'],
            'default_seo_title' => ['nullable', 'string', 'max:255', 'not_regex:/[<>]/'], 'default_meta_description' => ['nullable', 'string', 'max:500', 'not_regex:/[<>]/'],
            'default_meta_keywords' => ['nullable', 'string', 'max:500', 'not_regex:/[<>]/'],
            'canonical_base_url' => ['nullable', 'url:http,https', 'max:2048', function ($a, $v, $fail) {
                if (app()->environment('production') && ! str_starts_with($v, 'https://')) {
                    $fail('The canonical base URL must use HTTPS in production.');
                }
            }],
            'open_graph_image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'], 'twitter_card_image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'robots_directive' => ['nullable', 'in:index, follow,noindex, follow,index, nofollow,noindex, nofollow'],
            'organization_schema_enabled' => ['required', 'boolean'], 'google_analytics_id' => ['nullable', 'regex:/^(?:G-[A-Z0-9]{6,20}|UA-\d{4,12}-\d+)$/'],
            'google_tag_manager_id' => ['nullable', 'regex:/^GTM-[A-Z0-9]{4,12}$/'], 'search_console_verification' => ['nullable', 'regex:/^[A-Za-z0-9_=-]{10,255}$/'],
        ];
    }
}
