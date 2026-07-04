<?php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($project->featured_image);
?>
<article class="work-card h-100">
    <div class="card-media">
        <?php if($imageUrl): ?><img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($project->title); ?>" loading="lazy" decoding="async"><?php else: ?><div class="image-placeholder">Project</div><?php endif; ?>
        <span class="progress-badge"><?php echo e($project->progress_percentage ?? 0); ?>%</span>
    </div>
    <div class="card-body">
        <span class="card-meta"><?php echo e($project->category?->name ?: 'Project'); ?></span>
        <h3 class="h5"><a href="<?php echo e(route('projects.show', $project)); ?>" class="stretched-link text-decoration-none text-reset"><?php echo e($project->title); ?></a></h3>
        <p><?php echo e($project->short_description ?: 'Construction project delivered with careful planning, quality control, and safe execution.'); ?></p>
        <span class="card-link">Read More →</span>
    </div>
</article>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/content/project-card.blade.php ENDPATH**/ ?>