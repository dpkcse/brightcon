<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
    ];

    protected function casts(): array
    {
        return [
        ];
    }
}
