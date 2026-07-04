@php
    $theme = $themeSettings ?? null;
    $cssValue = fn ($value, $fallback) => e($value ?: $fallback);
    $customCss = $theme?->custom_css ? preg_replace(['/\<\/?script\b[^>]*>/i', '/<\/?style\b[^>]*>/i'], '', $theme->custom_css) : null;
@endphp
<style>
:root {
  --primary-color: {{ $cssValue($theme?->primary_color, '#d80d4c') }};
  --secondary-color: {{ $cssValue($theme?->secondary_color, '#ffffff') }};
  --footer-background-color: {{ $cssValue($theme?->footer_background_color, '#111827') }};
  --body-text-color: {{ $cssValue($theme?->body_text_color, '#374151') }};
  --heading-text-color: {{ $cssValue($theme?->heading_text_color, '#111827') }};
  --body-font-family: {{ $cssValue($theme?->body_font_family, 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif') }};
  --heading-font-family: {{ $cssValue($theme?->heading_font_family, 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif') }};
  --base-font-size: {{ $cssValue($theme?->base_font_size, '16px') }};
  --h1-font-size: {{ $cssValue($theme?->h1_font_size, '2.5rem') }};
  --h2-font-size: {{ $cssValue($theme?->h2_font_size, '2rem') }};
  --h3-font-size: {{ $cssValue($theme?->h3_font_size, '1.5rem') }};
  --button-radius: {{ $cssValue($theme?->button_radius, '.375rem') }};
  --section-spacing: {{ $cssValue($theme?->section_spacing, '4rem') }};
}
@if($customCss)
{!! $customCss !!}
@endif
</style>
