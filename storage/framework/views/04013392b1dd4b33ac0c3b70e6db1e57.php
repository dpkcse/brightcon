<?php
    use App\Support\FrontendImage;

    $companyName = $siteSettings?->company_name ?: config('app.name');
    $defaultTitle = trim($companyName.' | '.($siteSettings?->tagline ?: 'Construction & Engineering'));
    $title = trim($__env->yieldContent('title', $seo['title'] ?? $defaultTitle));
    $description = trim($__env->yieldContent('meta_description', $seo['description'] ?? ($siteSettings?->tagline ?: 'Construction and engineering services delivered with safety, quality, and professionalism.')));
    $canonical = $__env->yieldContent('canonical', $seo['canonical'] ?? url()->current());
    $ogTitle = trim($__env->yieldContent('og_title', $seo['og_title'] ?? $title));
    $ogDescription = trim($__env->yieldContent('og_description', $seo['og_description'] ?? $description));
    $robots = trim($__env->yieldContent('robots', $seo['robots'] ?? 'index, follow'));
    $fallbackImage = FrontendImage::url($siteSettings?->logo) ?: FrontendImage::url($siteSettings?->favicon);
    $ogImage = $__env->yieldContent('og_image', $seo['og_image'] ?? $fallbackImage);
?>
<title><?php echo e($title); ?></title>
<meta name="description" content="<?php echo e($description); ?>">
<link rel="canonical" href="<?php echo e($canonical); ?>">
<meta name="robots" content="<?php echo e($robots); ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo e($companyName); ?>">
<meta property="og:title" content="<?php echo e($ogTitle); ?>">
<meta property="og:description" content="<?php echo e($ogDescription); ?>">
<meta property="og:url" content="<?php echo e($canonical); ?>">
<?php if(filled($ogImage)): ?>
<meta property="og:image" content="<?php echo e(url($ogImage)); ?>">
<?php endif; ?>
<meta name="twitter:card" content="<?php echo e(filled($ogImage) ? 'summary_large_image' : 'summary'); ?>">
<meta name="twitter:title" content="<?php echo e($ogTitle); ?>">
<meta name="twitter:description" content="<?php echo e($ogDescription); ?>">
<?php if(filled($ogImage)): ?>
<meta name="twitter:image" content="<?php echo e(url($ogImage)); ?>">
<?php endif; ?>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/seo.blade.php ENDPATH**/ ?>