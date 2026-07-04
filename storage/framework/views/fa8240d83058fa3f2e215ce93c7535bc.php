<?php
    use App\Support\FrontendImage;
    $backgroundImage = $section?->background_image ? FrontendImage::url($section->background_image) : null;
    $backgroundColor = $section?->background_color ?: 'var(--primary-color)';
?>
<section class="gallery-cta" style="--cta-bg: <?php echo e($backgroundColor); ?>; <?php if($backgroundImage): ?> background-image: linear-gradient(rgba(8,11,16,.70), rgba(8,11,16,.70)), url('<?php echo e($backgroundImage); ?>'); <?php endif; ?>">
    <div class="container text-center">
        <span class="section-kicker light"><?php echo e($section?->title ?: 'Our Gallery'); ?></span>
        <h2><?php echo e($section?->subtitle ?: 'In our work we have pride, quality is what we provide.'); ?></h2>
        <?php if($section?->content): ?><p><?php echo e($section->content); ?></p><?php endif; ?>
        <?php if($section?->button_text && $section?->button_link): ?><a href="<?php echo e(url($section->button_link)); ?>" class="btn btn-light btn-lg"><?php echo e($section->button_text); ?></a><?php endif; ?>
    </div>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/gallery-cta.blade.php ENDPATH**/ ?>