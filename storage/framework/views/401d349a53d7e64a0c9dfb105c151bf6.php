
<?php ($companyName = $siteSettings?->company_name ?: config('app.name')); ?>
<?php $__env->startSection('title', 'Services | '.$companyName); ?>
<?php $__env->startSection('meta_description', $section?->content ?: 'Explore our construction and engineering services.'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('frontend.partials.page-header', ['title' => 'Our Services', 'description' => $section?->subtitle ?: 'Integrated civil, structural, infrastructure, and power-sector construction capabilities.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<section class="container-xl section-spacing">
    <div class="section-heading text-center mx-auto mb-5">
        <span class="section-kicker">What We Do</span>
        <h2><?php echo e($section?->title ?: 'Construction & Engineering Services'); ?></h2>
        <p><?php echo e($section?->content ?: 'We provide reliable services from planning and procurement through site execution and handover.'); ?></p>
    </div>
    <?php if($services->count()): ?>
        <div class="row g-4"><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-6 col-xl-4 d-flex"><?php echo $__env->make('frontend.partials.content.service-card', ['service' => $service], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
        <div class="mt-5"><?php echo e($services->links()); ?></div>
    <?php else: ?>
        <?php echo $__env->make('frontend.partials.content.empty-state', ['title' => 'No services published yet', 'message' => 'Active services added from the CMS will appear here.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/pages/services/index.blade.php ENDPATH**/ ?>