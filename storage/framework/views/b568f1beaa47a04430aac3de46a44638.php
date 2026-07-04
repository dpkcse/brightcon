<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Admin Dashboard'); ?> | BrightCon</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js']); ?>
</head>
<body>
<div class="admin-shell d-flex">
    <?php ($unreadMessages = Cache::remember('contact_messages_unread_count', 300, fn () => \App\Models\ContactMessage::query()->unread()->count())); ?>
    <?php ($groups = [
        'Dashboard' => [['Dashboard', route('admin.dashboard'), 'admin.dashboard']],
        'Settings' => [['General Settings', route('admin.settings.general.edit'), 'admin.settings.general.*'], ['Theme Settings', route('admin.settings.theme.edit'), 'admin.settings.theme.*']],
        'Homepage' => [['Homepage Sections', route('admin.homepage-sections.index'), 'admin.homepage-sections.*'], ['Hero Sliders', route('admin.sliders.index'), 'admin.sliders.*'], ['Feature Highlights', route('admin.features.index'), 'admin.features.*']],
        'Content' => [['Project Categories', route('admin.project-categories.index'), 'admin.project-categories.*'], ['Projects', route('admin.projects.index'), 'admin.projects.*'], ['Services', route('admin.services.index'), 'admin.services.*'], ['Gallery Images', route('admin.gallery-images.index'), 'admin.gallery-images.*'], ['Contact Messages', route('admin.contact-messages.index'), 'admin.contact-messages.*', $unreadMessages]],
        'Appearance' => [['Menu Items', route('admin.menu-items.index'), 'admin.menu-items.*'], ['Social Links', route('admin.social-links.index'), 'admin.social-links.*'], ['Footer Links', route('admin.footer-links.index'), 'admin.footer-links.*']],
    ]); ?>
    <aside class="admin-sidebar d-none d-lg-flex flex-column">
        <div class="brand p-4 fw-bold fs-4">BrightCon Admin</div>
        <nav class="nav flex-column gap-1 p-3">
            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $heading => $links): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sidebar-heading mt-3 mb-1"><?php echo e($heading); ?></div>
                <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php ([$label, $href, $pattern, $badge] = array_pad($link, 4, null)); ?>
                    <a class="nav-link d-flex justify-content-between align-items-center <?php echo e($pattern && request()->routeIs($pattern) ? 'active' : ''); ?> <?php echo e($href === '#' ? 'disabled opacity-75' : ''); ?>" href="<?php echo e($href); ?>"><span><?php echo e($label); ?></span><?php if($badge): ?><span class="badge text-bg-danger"><?php echo e($badge); ?></span><?php endif; ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </aside>
    <div class="admin-content flex-grow-1">
        <div class="admin-topbar px-4 py-3 d-flex justify-content-between align-items-center">
            <div><strong><?php echo $__env->yieldContent('page-heading', 'Dashboard'); ?></strong><div class="small text-muted">Back office content management</div></div>
            <form method="POST" action="<?php echo e(route('admin.logout')); ?>"><?php echo csrf_field(); ?><button class="btn btn-primary-brand btn-sm" type="submit">Logout</button></form>
        </div>
        <main class="p-4"><?php echo $__env->make('admin.partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php echo $__env->yieldContent('content'); ?></main>
    </div>
</div>
</body>
</html>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>