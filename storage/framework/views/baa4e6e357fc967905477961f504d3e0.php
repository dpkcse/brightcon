<?php use App\Support\FrontendImage; ?>
<section class="home-projects section-spacing bg-soft">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker"><?php echo e($section?->title ?: 'Project Highlights'); ?></span>
            <h2><?php echo e($section?->subtitle ?: 'Featured work delivered with precision.'); ?></h2>
            <?php if($section?->content): ?><p><?php echo e($section->content); ?></p><?php endif; ?>
        </div>
        <?php if($projects->isNotEmpty()): ?>
            <div class="row g-4 mt-2">
                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4">
                        <article class="work-card h-100">
                            <div class="card-media">
                                <?php if($project->featured_image): ?><img src="<?php echo e(FrontendImage::url($project->featured_image)); ?>" alt="<?php echo e($project->title); ?>" loading="lazy" decoding="async"><?php else: ?><div class="image-placeholder"><span>Project</span></div><?php endif; ?>
                                <span class="progress-badge"><?php echo e((int) $project->progress_percentage); ?>%</span>
                            </div>
                            <div class="card-body"><h3 class="h5"><?php echo e($project->title); ?></h3><?php if($project->category): ?><span class="card-meta"><?php echo e($project->category->name); ?></span><?php endif; ?> <?php if($project->short_description): ?><p><?php echo e($project->short_description); ?></p><?php endif; ?> <a href="<?php echo e(url('/projects/'.$project->slug)); ?>" class="stretched-link card-link">Read More</a></div>
                        </article>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="empty-state mt-4">No featured projects available.</div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/project-highlights.blade.php ENDPATH**/ ?>