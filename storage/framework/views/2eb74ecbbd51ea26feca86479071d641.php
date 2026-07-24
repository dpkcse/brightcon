<?php
    $partnerMessages = $partnerMessages ?? collect();
?>

<?php if($partnerMessages->isNotEmpty()): ?>
<section id="partner-messages" class="partner-messages-section section-spacing" aria-labelledby="partner-messages-title">
    <div class="container-xl">
        <header class="section-heading text-center mx-auto mb-5">
            <span class="section-kicker">Leadership Voices</span>
            <h2 id="partner-messages-title">Messages from Our Partners</h2>
            <p>Leadership insights, shared values, and the vision behind our journey.</p>
        </header>

        <div class="partner-message-stage" data-partner-carousel tabindex="0" aria-label="Partner messages carousel">
            <?php $__currentLoopData = $partnerMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($initials = collect(preg_split('/\s+/', trim($message->name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('')); ?>
                <article id="partner-message-<?php echo e($message->id); ?>" class="partner-message-card <?php echo e($loop->first ? 'is-active' : ''); ?>" data-partner-slide aria-hidden="<?php echo e($loop->first ? 'false' : 'true'); ?>" <?php if(! $loop->first): ?> hidden <?php endif; ?>>
                    <div class="partner-message-media">
                        <div class="partner-message-accent" aria-hidden="true"></div>
                        <?php if($message->image_path): ?>
                            <img src="<?php echo e(\App\Support\FrontendImage::url($message->image_path)); ?>" alt="Portrait of <?php echo e($message->name); ?>" <?php echo e($loop->first ? 'fetchpriority=high' : 'loading=lazy'); ?> decoding="async">
                        <?php else: ?>
                            <div class="partner-message-placeholder" role="img" aria-label="Portrait placeholder for <?php echo e($message->name); ?>"><?php echo e($initials ?: '?'); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="partner-message-content">
                        <span class="partner-message-quote" aria-hidden="true">“</span>
                        <?php if($message->highlighted_text): ?><p class="partner-message-highlight"><?php echo e($message->highlighted_text); ?></p><?php endif; ?>
                        <p class="partner-message-copy"><?php echo e($message->full_message); ?></p>
                        <footer class="partner-message-person">
                            <div>
                                <?php if($message->organization_logo_path): ?><img src="<?php echo e(\App\Support\FrontendImage::url($message->organization_logo_path)); ?>" alt="<?php echo e($message->organization ? $message->organization.' logo' : 'Organization logo'); ?>" class="partner-message-logo" loading="lazy" decoding="async"><?php endif; ?>
                                <h3><?php echo e($message->name); ?></h3>
                                <p><?php echo e($message->designation); ?><?php if($message->organization): ?> <span aria-hidden="true">·</span> <?php echo e($message->organization); ?><?php endif; ?></p>
                            </div>
                            <?php if($message->linkedin_url): ?><a class="partner-message-linkedin" href="<?php echo e($message->linkedin_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="View <?php echo e($message->name); ?> on LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a><?php endif; ?>
                        </footer>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($partnerMessages->count() > 1): ?>
                <div class="partner-message-navigation" aria-label="Partner message navigation">
                    <button type="button" class="partner-message-control" data-partner-prev aria-label="Previous partner message"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                    <span class="partner-message-indicator" data-partner-indicator aria-live="polite">01 / <?php echo e(str_pad((string) $partnerMessages->count(), 2, '0', STR_PAD_LEFT)); ?></span>
                    <button type="button" class="partner-message-control" data-partner-next aria-label="Next partner message"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
                <div class="partner-message-selectors" role="list" aria-label="Select a partner message">
                    <?php $__currentLoopData = $partnerMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="partner-message-selector <?php echo e($loop->first ? 'is-active' : ''); ?>" data-partner-tab data-index="<?php echo e($loop->index); ?>" aria-controls="partner-message-<?php echo e($message->id); ?>" <?php if($loop->first): ?> aria-current="true" <?php endif; ?>>
                            <?php if($message->image_path): ?><img src="<?php echo e(\App\Support\FrontendImage::url($message->image_path)); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><span class="partner-message-selector-placeholder" aria-hidden="true"><?php echo e(collect(preg_split('/\s+/', trim($message->name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') ?: '?'); ?></span><?php endif; ?>
                            <span><strong><?php echo e($message->name); ?></strong><small><?php echo e($message->designation ?: $message->organization); ?></small></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/frontend/partials/about/partner-messages.blade.php ENDPATH**/ ?>