<?php use App\Support\FrontendImage; ?>
<header class="frontend-header bg-white py-3">
    <div class="container-xl d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        <a class="brand-wrap text-decoration-none" href="<?php echo e(route('home')); ?>">
            <?php if($siteSettings?->logo): ?>
                <img src="<?php echo e(FrontendImage::url($siteSettings->logo)); ?>" alt="<?php echo e($siteSettings?->company_name ?: config('app.name')); ?>" class="frontend-logo">
            <?php else: ?>
                <span class="brand-mark"><?php echo e($siteSettings?->company_name ?: config('app.name')); ?></span>
            <?php endif; ?>
        </a>
        <div class="header-contact d-flex flex-column flex-md-row align-items-center gap-3 gap-xl-4">
            <?php if($siteSettings?->email): ?>
                <a class="header-contact-item" href="mailto:<?php echo e($siteSettings->email); ?>"><span>Email</span><strong><?php echo e($siteSettings->email); ?></strong></a>
            <?php endif; ?>
            <?php if($siteSettings?->phone): ?>
                <a class="header-contact-item" href="tel:<?php echo e($siteSettings->phone); ?>"><span>Call Us</span><strong><?php echo e($siteSettings->phone); ?></strong></a>
            <?php endif; ?>
            <?php if($siteSettings?->profile_pdf): ?>
                <a class="btn btn-primary-brand" href="<?php echo e(FrontendImage::url($siteSettings->profile_pdf)); ?>" target="_blank" rel="noopener">Download Profile</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/header.blade.php ENDPATH**/ ?>