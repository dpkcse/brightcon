

<?php $__env->startSection('title', 'Page Not Found | '.($siteSettings?->company_name ?: config('app.name'))); ?>
<?php $__env->startSection('meta_description', 'The page you requested could not be found.'); ?>
<?php $__env->startSection('robots', 'noindex, follow'); ?>

<?php $__env->startSection('content'); ?>
<section class="container section-spacing text-center">
    <div class="mx-auto" style="max-width: 680px;">
        <span class="section-kicker">404 Error</span>
        <h1 class="display-5 fw-bold mb-3">Page Not Found</h1>
        <p class="lead text-muted mb-4">The page may have moved, been removed, or the address may be incorrect.</p>
        <a href="<?php echo e(route('home')); ?>" class="btn btn-primary-brand btn-lg">Back to Home</a>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/errors/404.blade.php ENDPATH**/ ?>