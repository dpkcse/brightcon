<?php use App\Support\FrontendImage; ?>
<?php if($featureItems->isNotEmpty()): ?>
<section class="feature-strip-wrap">
    <div class="container">
        <div class="feature-strip row g-0">
            <?php $__currentLoopData = $featureItems->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="col-md-4 feature-card">
                    <div class="feature-icon">
                        <?php if($feature->icon_class): ?><i class="<?php echo e($feature->icon_class); ?>"></i>
                        <?php elseif($feature->image): ?><img src="<?php echo e(FrontendImage::url($feature->image)); ?>" alt="<?php echo e($feature->title ?: 'Feature icon'); ?>" loading="lazy" decoding="async">
                        <?php else: ?><span>✓</span><?php endif; ?>
                    </div>
                    <div><h2 class="h5"><?php echo e($feature->title ?: 'Reliable Delivery'); ?></h2><?php if($feature->short_text): ?><p><?php echo e($feature->short_text); ?></p><?php endif; ?></div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/feature-strip.blade.php ENDPATH**/ ?>