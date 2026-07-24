<div class="frontend-topbar py-2">
    <div class="container-xl d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div class="topbar-tagline text-center text-md-start"><?php echo e($siteSettings?->tagline ?: 'Engineering excellence built on trust.'); ?></div>
        <?php if($socialLinks->whereNotNull('url')->isNotEmpty()): ?>
            <div class="topbar-social d-flex flex-wrap justify-content-center gap-3">
                <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(blank($social->url)) continue; ?>
                    <a href="<?php echo e($social->url); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($social->platform); ?>">
                        <?php if($social->icon_class): ?><i class="<?php echo e($social->icon_class); ?>"></i><?php else: ?><?php echo e($social->platform); ?><?php endif; ?>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/top-mini-bar.blade.php ENDPATH**/ ?>