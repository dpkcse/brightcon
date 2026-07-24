<?php
    $theme = $themeSettings ?? null;
    $cssValue = fn ($value, $fallback) => e($value ?: $fallback);
    $customCss = $theme?->custom_css ? preg_replace(['/\<\/?script\b[^>]*>/i', '/<\/?style\b[^>]*>/i'], '', $theme->custom_css) : null;
?>
<style>
:root {
  --primary-color: <?php echo e($cssValue($theme?->primary_color, '#d80d4c')); ?>;
  --secondary-color: <?php echo e($cssValue($theme?->secondary_color, '#ffffff')); ?>;
  --footer-background-color: <?php echo e($cssValue($theme?->footer_background_color, '#111827')); ?>;
  --body-text-color: <?php echo e($cssValue($theme?->body_text_color, '#374151')); ?>;
  --heading-text-color: <?php echo e($cssValue($theme?->heading_text_color, '#111827')); ?>;
  --body-font-family: <?php echo e($cssValue($theme?->body_font_family, 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif')); ?>;
  --heading-font-family: <?php echo e($cssValue($theme?->heading_font_family, 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif')); ?>;
  --base-font-size: <?php echo e($cssValue($theme?->base_font_size, '16px')); ?>;
  --h1-font-size: <?php echo e($cssValue($theme?->h1_font_size, '2.5rem')); ?>;
  --h2-font-size: <?php echo e($cssValue($theme?->h2_font_size, '2rem')); ?>;
  --h3-font-size: <?php echo e($cssValue($theme?->h3_font_size, '1.5rem')); ?>;
  --button-radius: <?php echo e($cssValue($theme?->button_radius, '.375rem')); ?>;
  --section-spacing: <?php echo e($cssValue($theme?->section_spacing, '4rem')); ?>;
}
<?php if($customCss): ?>
<?php echo $customCss; ?>

<?php endif; ?>
</style>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/theme-css.blade.php ENDPATH**/ ?>