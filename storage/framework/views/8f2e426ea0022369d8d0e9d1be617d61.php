<?php
    $fallbackMenu = collect([
        ['label' => 'Home', 'url' => url('/'), 'target' => '_self'], ['label' => 'About', 'url' => url('/about'), 'target' => '_self'],
        ['label' => 'Services', 'url' => url('/services'), 'target' => '_self'], ['label' => 'Projects', 'url' => url('/projects'), 'target' => '_self'],
        ['label' => 'Competency', 'url' => url('/competency'), 'target' => '_self'], ['label' => 'Equipment List', 'url' => url('/equipment-list'), 'target' => '_self'],
        ['label' => 'Gallery', 'url' => url('/gallery'), 'target' => '_self'], ['label' => 'Contact', 'url' => url('/contact'), 'target' => '_self'],
    ]);
    $items = $menuItems->isNotEmpty() ? $menuItems : $fallbackMenu;
    $normalize = fn ($path) => trim(parse_url($path, PHP_URL_PATH) ?: '/', '/');
?>
<nav class="navbar navbar-expand-lg frontend-nav sticky-top" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand d-lg-none" href="<?php echo e(route('home')); ?>"><?php echo e($siteSettings?->company_name ?: config('app.name')); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav gap-lg-1">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $url = is_array($item) ? $item['url'] : $item->url;
                        $label = is_array($item) ? $item['label'] : $item->label;
                        $target = in_array((is_array($item) ? $item['target'] : $item->target), ['_self', '_blank'], true) ? (is_array($item) ? $item['target'] : $item->target) : '_self';
                        $href = str_starts_with($url, 'http') || str_starts_with($url, '#') ? $url : url($url);
                        $active = $normalize(request()->path()) === $normalize($href) || (request()->is('/') && $normalize($href) === '');
                    ?>
                    <li class="nav-item"><a class="nav-link <?php echo e($active ? 'active' : ''); ?>" href="<?php echo e($href); ?>" target="<?php echo e($target); ?>" <?php if($target === '_blank'): ?> rel="noopener" <?php endif; ?>><?php echo e($label); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/navigation.blade.php ENDPATH**/ ?>