
<?php $__env->startSection('title','Edit Homepage Section'); ?>
<?php $__env->startSection('page-heading','Edit '.$label); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('admin.homepage-sections.update', $item)); ?>" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<div class="col-md-6"><label class="form-label">Section Key</label><input class="form-control" value="<?php echo e($item->section_key); ?>" disabled></div>
<?php $__currentLoopData = ['title'=>'Title','subtitle'=>'Subtitle','button_text'=>'Button Text','button_link'=>'Button Link','background_color'=>'Background Color','sort_order'=>'Sort Order']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$labelText): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-6"><label class="form-label"><?php echo e($labelText); ?></label><input class="form-control <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $item->$field)); ?>"><?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="col-12"><label class="form-label">Content</label><textarea class="form-control <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="content" rows="6"><?php echo e(old('content',$item->content)); ?></textarea><?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
<?php $__currentLoopData = ['image'=>'Image','background_image'=>'Background Image']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$labelText): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-6"><label class="form-label"><?php echo e($labelText); ?></label><input type="file" class="form-control <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($field); ?>"><?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php if($item->$field): ?><img src="<?php echo e(Storage::url($item->$field)); ?>" class="img-thumbnail mt-2" style="max-height:120px" alt="Current <?php echo e($labelText); ?>"><?php endif; ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" <?php if(old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Active</option><option value="0" <?php if(! old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Inactive</option></select></div>
</div><div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="<?php echo e(route('admin.homepage-sections.index')); ?>">Back</a><button class="btn btn-primary-brand">Save</button></div></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/pages/homepage-sections/form.blade.php ENDPATH**/ ?>