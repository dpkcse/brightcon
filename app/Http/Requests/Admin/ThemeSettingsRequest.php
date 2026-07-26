<?php

namespace App\Http\Requests\Admin;

use App\Support\CustomCssPolicy;
use Illuminate\Foundation\Http\FormRequest;

class ThemeSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    protected function prepareForValidation(): void
    {
        $data = ['custom_css_enabled' => $this->boolean('custom_css_enabled')];
        foreach (['primary_color', 'secondary_color', 'footer_background_color', 'body_text_color', 'heading_text_color', 'accent_color', 'header_background_color', 'header_text_color', 'button_background_color', 'button_text_color', 'link_color', 'link_hover_color'] as $field) {
            if ($this->filled($field)) {
                $data[$field] = strtoupper($this->input($field));
            }
        }
        $this->merge($data);
    }

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
            'base_font_size' => ['nullable', 'regex:/^\d+(?:\.\d+)?(?:px|rem)$/'],
            'h1_font_size' => ['nullable', 'regex:/^\d+(?:\.\d+)?(?:px|rem)$/'],
            'h2_font_size' => ['nullable', 'regex:/^\d+(?:\.\d+)?(?:px|rem)$/'],
            'h3_font_size' => ['nullable', 'regex:/^\d+(?:\.\d+)?(?:px|rem)$/'],
            'button_radius' => ['nullable', 'regex:/^(?:0|\d+(?:\.\d+)?(?:px|rem))$/'],
            'section_spacing' => ['nullable', 'regex:/^\d+(?:\.\d+)?(?:px|rem)$/'],
            'custom_css' => ['nullable', 'string'],
            'accent_color' => $hex, 'header_background_color' => $hex, 'header_text_color' => $hex,
            'button_background_color' => $hex, 'button_text_color' => $hex, 'link_color' => $hex, 'link_hover_color' => $hex,
            'card_border_radius' => ['nullable', 'regex:/^(?:0|\d+(?:\.\d+)?(?:px|rem))$/'], 'input_border_radius' => ['nullable', 'regex:/^(?:0|\d+(?:\.\d+)?(?:px|rem))$/'],
            'header_logo_variant' => ['nullable', 'in:default,dark,light'], 'custom_css_enabled' => ['required', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! CustomCssPolicy::isSafe($this->input('custom_css'))) {
                $validator->errors()->add('custom_css', 'Custom CSS contains a prohibited or unsafe pattern.');
            }
        });
    }
}
