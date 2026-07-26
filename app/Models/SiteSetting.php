<?php

namespace App\Models;

use App\Support\GoogleMapsUrl;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'company_name',
        'tagline',
        'logo',
        'favicon',
        'email',
        'phone',
        'address',
        'profile_pdf',
        'default_language',
        'timezone',
        'copyright_text',
        'developer_name',
        'developer_link',
        'show_contact_map',
        'google_map_embed_url',
        'map_location_name',
        'map_address',
        'map_latitude',
        'map_longitude',
        'map_zoom',
        'product_name', 'product_version', 'company_short_name', 'company_description',
        'dark_logo_path', 'light_logo_path', 'powered_by_text', 'show_powered_by', 'date_format',
        'website_status', 'maintenance_message', 'secondary_address', 'secondary_phone',
        'whatsapp_number', 'secondary_email', 'business_hours', 'contact_recipient_email',
        'contact_form_enabled', 'contact_phone_enabled', 'contact_subject_enabled',
        'contact_success_message', 'contact_failure_message', 'contact_email_subject_prefix',
        'default_seo_title', 'default_meta_description', 'default_meta_keywords', 'canonical_base_url',
        'open_graph_image_path', 'twitter_card_image_path', 'robots_directive',
        'organization_schema_enabled', 'google_analytics_id', 'google_tag_manager_id',
        'search_console_verification', 'installation_completed_at', 'installed_version',
    ];

    protected function casts(): array
    {
        return [
            'show_contact_map' => 'boolean',
            'map_latitude' => 'decimal:7',
            'map_longitude' => 'decimal:7',
            'map_zoom' => 'integer',
            'show_powered_by' => 'boolean',
            'contact_form_enabled' => 'boolean',
            'contact_phone_enabled' => 'boolean',
            'contact_subject_enabled' => 'boolean',
            'organization_schema_enabled' => 'boolean',
            'installation_completed_at' => 'datetime',
        ];
    }

    public function trustedGoogleMapEmbedUrl(): ?string
    {
        return GoogleMapsUrl::isTrustedEmbedUrl($this->google_map_embed_url)
            ? $this->google_map_embed_url
            : null;
    }

    public function googleMapsDirectionsUrl(): ?string
    {
        $destination = filled($this->map_latitude) && filled($this->map_longitude)
            ? $this->map_latitude.','.$this->map_longitude
            : ($this->map_address ?: $this->address);

        return filled($destination)
            ? 'https://www.google.com/maps/dir/?api=1&destination='.rawurlencode($destination)
            : null;
    }
}
