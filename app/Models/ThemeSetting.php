<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = [
        'primary_color',
        'secondary_color',
        'footer_background_color',
        'body_text_color',
        'heading_text_color',
        'body_font_family',
        'heading_font_family',
        'base_font_size',
        'h1_font_size',
        'h2_font_size',
        'h3_font_size',
        'button_radius',
        'section_spacing',
        'custom_css',
        'accent_color', 'header_background_color', 'header_text_color',
        'button_background_color', 'button_text_color', 'link_color', 'link_hover_color',
        'card_border_radius', 'input_border_radius', 'header_logo_variant', 'custom_css_enabled',
    ];

    protected function casts(): array
    {
        return ['custom_css_enabled' => 'boolean'];
    }
}
