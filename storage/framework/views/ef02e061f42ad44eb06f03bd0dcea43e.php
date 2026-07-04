
<?php $__env->startSection('title','Theme Settings'); ?>
<?php $__env->startSection('page-heading','Theme Settings'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('admin.settings.theme.update')); ?>" class="card border-0 shadow-sm"><div class="card-body row g-3"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<?php $__currentLoopData = ['primary_color','secondary_color','footer_background_color','body_text_color','heading_text_color']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-4"><label class="form-label"><?php echo e(Str::headline($field)); ?></label><input type="color" class="form-control form-control-color d-inline-block me-2" value="<?php echo e(old($field,$setting->$field ?: '#000000')); ?>" oninput="this.nextElementSibling.value=this.value"><input class="form-control d-inline-block w-auto <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($field); ?>" value="<?php echo e(old($field,$setting->$field)); ?>"><?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__currentLoopData = ['body_font_family','heading_font_family','base_font_size','h1_font_size','h2_font_size','h3_font_size','button_radius','section_spacing']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-3"><label class="form-label"><?php echo e(Str::headline($field)); ?></label><input class="form-control" name="<?php echo e($field); ?>" value="<?php echo e(old($field,$setting->$field)); ?>"></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="col-12"><label class="form-label">Custom CSS</label><textarea class="form-control" name="custom_css" rows="8"><?php echo e(old('custom_css',$setting->custom_css)); ?></textarea><div class="form-help">CSS only. Script tags are stripped in the frontend partial.</div></div>
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary-brand">Save Theme</button></div></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/pages/settings/theme.blade.php ENDPATH**/ ?>