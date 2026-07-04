<?php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($project->featured_image);
?>
<article class="content-card project-card h-100">
    <div class="content-card-media content-card-media-project">
        <?php if($imageUrl): ?>
            <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($project->title); ?>" loading="lazy" decoding="async">
        <?php else: ?>
            <div class="image-placeholder image-placeholder-project"><span>Project</span></div>
        <?php endif; ?>
        <span class="progress-badge"><?php echo e($project->progress_percentage ?? 0); ?>%</span>
    </div>
    <div class="content-card-body">
        <span class="content-card-meta"><?php echo e($project->category?->name ?: 'Project'); ?></span>
        <h3 class="content-card-title"><a href="<?php echo e(route('projects.show', $project)); ?>" class="stretched-link text-decoration-none text-reset"><?php echo e($project->title); ?></a></h3>
        <p class="content-card-description"><?php echo e($project->short_description ?: 'Construction project delivered with careful planning, quality control, and safe execution.'); ?></p>
        <span class="content-card-action">Read More →</span>
    </div>
</article>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/content/project-card.blade.php ENDPATH**/ ?>