<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ThemeSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $hex = ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'];
        return [
            'primary_color' => $hex,
            'secondary_color' => $hex,
            'footer_background_color' => $hex,
            'body_text_color' => $hex,
            'heading_text_color' => $hex,
            'body_font_family' => ['nullable', 'string', 'max:100'],
            'heading_font_family' => ['nullable', 'string', 'max:100'],
            'base_font_size' => ['nullable', 'string', 'max:20'],
            'h1_font_size' => ['nullable', 'string', 'max:20'],
            'h2_font_size' => ['nullable', 'string', 'max:20'],
            'h3_font_size' => ['nullable', 'string', 'max:20'],
            'button_radius' => ['nullable', 'string', 'max:20'],
            'section_spacing' => ['nullable', 'string', 'max:20'],
            'custom_css' => ['nullable', 'string'],
        ];
    }
}
