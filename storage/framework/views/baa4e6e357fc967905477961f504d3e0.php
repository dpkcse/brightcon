<?php use App\Support\FrontendImage; ?>
<section class="home-projects section-spacing bg-soft">
    <div class="container-xl">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker"><?php echo e($section?->title ?: 'Project Highlights'); ?></span>
            <h2><?php echo e($section?->subtitle ?: 'Featured work delivered with precision.'); ?></h2>
            <?php if($section?->content): ?><p><?php echo e($section->content); ?></p><?php endif; ?>
        </div>
        <?php if($projects->isNotEmpty()): ?>
            <div class="row g-4 mt-3 align-items-stretch">
                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4 d-flex">
                        <?php echo $__env->make('frontend.partials.content.project-card', ['project' => $project], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="empty-state mt-4">No featured projects available.</div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/home/project-highlights.blade.php ENDPATH**/ ?>