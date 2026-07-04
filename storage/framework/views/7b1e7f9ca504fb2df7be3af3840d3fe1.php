<?php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($service->image);
?>
<article class="content-card service-card h-100">
    <div class="content-card-media content-card-media-service">
        <?php if($imageUrl): ?>
            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($service->title); ?>" loading="lazy" decoding="async">
        <?php else: ?>
            <div class="image-placeholder image-placeholder-service"><span>Service</span></div>
        <?php endif; ?>
        <span class="service-icon"><?php if($service->icon_class): ?><i class="<?php echo e($service->icon_class); ?>"></i><?php else: ?>⚙<?php endif; ?></span>
    </div>
    <div class="content-card-body service-card-body">
        <h3 class="content-card-title"><a href="<?php echo e(route('services.show', $service)); ?>" class="stretched-link text-decoration-none text-reset"><?php echo e($service->title); ?></a></h3>
        <p class="content-card-description"><?php echo e($service->short_description ?: 'Professional construction and engineering service delivered by experienced specialists.'); ?></p>
        <span class="content-card-action">Explore Service →</span>
    </div>
</article>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/content/service-card.blade.php ENDPATH**/ ?>