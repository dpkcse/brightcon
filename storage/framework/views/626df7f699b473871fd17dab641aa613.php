<?php
    $copyright = $siteSettings?->copyright_text ?: '© '.date('Y').' '.($siteSettings?->company_name ?: config('app.name')).'. All Rights Reserved.';
    $developerLink = $siteSettings?->developer_link;
    $validDeveloperLink = $developerLink && filter_var($developerLink, FILTER_VALIDATE_URL);
?>
<div class="copyright-bar py-3">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center text-md-start">
        <span><?php echo e(str_replace('{year}', date('Y'), $copyright)); ?></span>
        <?php if($siteSettings?->developer_name): ?>
            <span>Site Developed By <?php if($validDeveloperLink): ?><a href="<?php echo e($developerLink); ?>" target="_blank" rel="noopener"><?php echo e($siteSettings->developer_name); ?></a><?php else: ?><?php echo e($siteSettings->developer_name); ?><?php endif; ?>.</span>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/copyright.blade.php ENDPATH**/ ?>