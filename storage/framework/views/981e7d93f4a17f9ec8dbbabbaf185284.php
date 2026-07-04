<?php use App\Support\FrontendImage; ?>
<section class="home-hero" aria-label="Homepage hero slider">
    <?php if($sliders->isNotEmpty()): ?>
        <div id="homeHeroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $imageUrl = $slide->image ? FrontendImage::url($slide->image) : null; ?>
                    <div class="carousel-item <?php echo e($loop->first ? 'active' : ''); ?>">
                        <div class="hero-slide" <?php if($imageUrl): ?> style="background-image: linear-gradient(90deg, rgba(8,11,16,.88), rgba(8,11,16,.58)), url('<?php echo e($imageUrl); ?>');" <?php endif; ?>>
                            <div class="container">
                                <div class="hero-content">
                                    <?php if($slide->sub_heading): ?><span class="hero-eyebrow"><?php echo e($slide->sub_heading); ?></span><?php endif; ?>
                                    <?php if($loop->first): ?><h1><?php echo e($slide->heading ?: 'Building Better Spaces With Engineering Excellence'); ?></h1><?php else: ?><h2><?php echo e($slide->heading ?: 'Building Better Spaces With Engineering Excellence'); ?></h2><?php endif; ?>
                                    <?php if($slide->description): ?><p><?php echo e($slide->description); ?></p><?php endif; ?>
                                    <?php if($slide->button_text && $slide->button_link): ?><a href="<?php echo e(url($slide->button_link)); ?>" class="btn btn-primary-brand btn-lg"><?php echo e($slide->button_text); ?></a><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php if($sliders->count() > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="hero-slide hero-fallback">
            <div class="container">
                <div class="hero-content">
                    <span class="hero-eyebrow">Construction & Engineering</span>
                    <h1>Building Better Spaces With Engineering Excellence</h1>
                    <p>Delivering safe, reliable, and professional construction solutions for modern businesses and communities.</p>
                    <a href="<?php echo e(url('/contact')); ?>" class="btn btn-primary-brand btn-lg">Start a Project</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/hero-slider.blade.php ENDPATH**/ ?>