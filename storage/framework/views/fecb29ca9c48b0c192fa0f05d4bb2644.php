

<?php
    $companyName = $siteSettings?->company_name ?: config('app.name');
    $homeTitle = 'Home | '.$companyName;
    $homeDescription = $siteSettings?->tagline ?: 'Construction and engineering services delivered with safety, quality, and professionalism.';
?>

<?php $__env->startSection('title', $homeTitle); ?>
<?php $__env->startSection('meta_description', $homeDescription); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('frontend.partials.home.hero-slider', ['sliders' => $sliders], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('frontend.partials.home.feature-strip', ['featureItems' => $featureItems], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('frontend.partials.home.about-section', ['section' => $aboutSection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('frontend.partials.home.project-highlights', ['section' => $projectHighlightsSection, 'projects' => $featuredProjects], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('frontend.partials.home.gallery-cta', ['section' => $galleryCtaSection], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('frontend.partials.home.services-section', ['section' => $servicesSection, 'services' => $services], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/pages/home.blade.php ENDPATH**/ ?>