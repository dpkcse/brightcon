<?php use App\Support\FrontendImage; ?>
<section class="home-services section-spacing">
    <div class="container-xl">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker"><?php echo e($section?->title ?: 'What We Do'); ?></span>
            <h2><?php echo e($section?->subtitle ?: 'Construction services built around your goals.'); ?></h2>
            <?php if($section?->content): ?><p><?php echo e($section->content); ?></p><?php endif; ?>
        </div>
        <?php if($services->isNotEmpty()): ?>
            <div class="row g-4 mt-3 align-items-stretch">
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-xl-3 d-flex">
                        <?php echo $__env->make('frontend.partials.content.service-card', ['service' => $service], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="empty-state mt-4">No active services available.</div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/services-section.blade.php ENDPATH**/ ?>