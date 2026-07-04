<?php use App\Support\FrontendImage; ?>
<section class="home-services section-spacing">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker"><?php echo e($section?->title ?: 'What We Do'); ?></span>
            <h2><?php echo e($section?->subtitle ?: 'Construction services built around your goals.'); ?></h2>
            <?php if($section?->content): ?><p><?php echo e($section->content); ?></p><?php endif; ?>
        </div>
        <?php if($services->isNotEmpty()): ?>
            <div class="row g-4 mt-2">
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-xl-3">
                        <article class="service-card h-100">
                            <div class="card-media">
                                <?php if($service->image): ?><img src="<?php echo e(FrontendImage::url($service->image)); ?>" alt="<?php echo e($service->title); ?>" loading="lazy" decoding="async"><?php else: ?><div class="image-placeholder"><span>Service</span></div><?php endif; ?>
                                <div class="service-icon"><?php if($service->icon_class): ?><i class="<?php echo e($service->icon_class); ?>"></i><?php else: ?><span>▰</span><?php endif; ?></div>
                            </div>
                            <div class="card-body"><h3 class="h5"><?php echo e($service->title); ?></h3><?php if($service->short_description): ?><p><?php echo e($service->short_description); ?></p><?php endif; ?> <a href="<?php echo e(url('/services/'.$service->slug)); ?>" class="stretched-link card-link">Explore Service</a></div>
                        </article>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="empty-state mt-4">No active services available.</div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/services-section.blade.php ENDPATH**/ ?>