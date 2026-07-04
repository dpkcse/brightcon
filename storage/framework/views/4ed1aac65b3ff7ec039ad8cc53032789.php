<?php use App\Support\FrontendImage; ?>
<footer class="frontend-footer py-5">
    <div class="container">
        <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="mb-3">
                    <?php if($siteSettings?->logo): ?><img src="<?php echo e(FrontendImage::url($siteSettings->logo)); ?>" alt="<?php echo e($siteSettings?->company_name); ?>" class="footer-logo"><?php else: ?><h5><?php echo e($siteSettings?->company_name ?: config('app.name')); ?></h5><?php endif; ?>
                </div>
                <?php if($siteSettings?->address): ?><p><?php echo e($siteSettings->address); ?></p><?php endif; ?>
                <?php if($siteSettings?->phone): ?><p class="mb-1"><a href="tel:<?php echo e($siteSettings->phone); ?>"><?php echo e($siteSettings->phone); ?></a></p><?php endif; ?>
                <?php if($siteSettings?->email): ?><p><a href="mailto:<?php echo e($siteSettings->email); ?>"><?php echo e($siteSettings->email); ?></a></p><?php endif; ?>
                <?php if($socialLinks->whereNotNull('url')->isNotEmpty()): ?>
                    <div class="footer-social d-flex flex-wrap gap-3 mt-3">
                        <?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(blank($social->url)) continue; ?>
                            <a href="<?php echo e($social->url); ?>" target="_blank" rel="noopener" aria-label="<?php echo e($social->platform); ?>"><?php if($social->icon_class): ?><i class="<?php echo e($social->icon_class); ?>"></i><?php else: ?><?php echo e($social->platform); ?><?php endif; ?></a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-3">
                <h5>Quick Links</h5>
                <ul class="footer-links list-unstyled mt-3">
                    <?php $__empty_1 = true; $__currentLoopData = $footerLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><a href="<?php echo e(str_starts_with($link->url, 'http') ? $link->url : url($link->url)); ?>" target="<?php echo e(in_array($link->target, ['_self', '_blank'], true) ? $link->target : '_self'); ?>" <?php if($link->target === '_blank'): ?> rel="noopener" <?php endif; ?>><?php echo e($link->label); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li><a href="<?php echo e(route('about')); ?>">About</a></li><li><a href="<?php echo e(route('services.index')); ?>">Services</a></li><li><a href="<?php echo e(route('contact.index')); ?>">Contact</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-lg-5">
                <h5>Send Us a Message</h5>
                <?php echo $__env->make('frontend.partials.contact-form', ['formClass' => 'footer-contact-form mt-3', 'rows' => 4, 'formId' => 'footer-contact'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/footer.blade.php ENDPATH**/ ?>