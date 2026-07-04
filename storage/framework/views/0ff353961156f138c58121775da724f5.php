<?php use App\Support\FrontendImage; ?>
<?php if($featureItems->isNotEmpty()): ?>
<section class="home-feature-strip feature-strip-wrap" aria-label="Company feature highlights">
    <div class="container-xl">
        <div class="home-feature-grid">
            <?php $__currentLoopData = $featureItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="home-feature-card">
                    <div class="home-feature-icon" aria-hidden="true">
                        <?php if($feature->icon_class): ?>
                            <i class="<?php echo e($feature->icon_class); ?>"></i>
                        <?php elseif($feature->image): ?>
                            <img src="<?php echo e(FrontendImage::url($feature->image)); ?>" alt="" loading="lazy" decoding="async">
                        <?php else: ?>
                            <span>✓</span>
                        <?php endif; ?>
                    </div>
                    <div class="home-feature-content">
                        <h2 class="home-feature-title"><?php echo e($feature->title ?: 'Reliable Delivery'); ?></h2>
                        <?php if($feature->short_text): ?>
                            <p class="home-feature-text"><?php echo e($feature->short_text); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/feature-strip.blade.php ENDPATH**/ ?>