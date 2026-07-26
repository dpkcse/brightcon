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
    ];

    protected function casts(): array
    {
        return [
            'show_contact_map' => 'boolean',
            'map_latitude' => 'decimal:7',
            'map_longitude' => 'decimal:7',
            'map_zoom' => 'integer',
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
