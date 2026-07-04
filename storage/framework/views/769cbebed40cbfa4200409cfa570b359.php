<?php use App\Support\FrontendImage; ?>
<section class="home-about section-spacing">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="section-kicker"><?php echo e($section?->title ?: 'Who We Are'); ?></span>
                <h2><?php echo e($section?->subtitle ?: 'Experienced construction and engineering professionals.'); ?></h2>
                <div class="section-copy"><?php echo nl2br(e($section?->content ?: 'We combine practical field expertise, disciplined project management, and a quality-first mindset to deliver dependable construction outcomes.')); ?></div>
                <?php if($section?->button_text && $section?->button_link): ?><a href="<?php echo e(url($section->button_link)); ?>" class="btn btn-primary-brand mt-4"><?php echo e($section->button_text); ?></a><?php endif; ?>
            </div>
            <div class="col-lg-6">
                <?php if($section?->image): ?><img class="about-image" src="<?php echo e(FrontendImage::url($section->image)); ?>" alt="<?php echo e($section->subtitle ?: $section->title ?: 'About our company'); ?>" loading="lazy" decoding="async">
                <?php else: ?><div class="image-placeholder about-placeholder"><span>Construction Excellence</span></div><?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/about-section.blade.php ENDPATH**/ ?>