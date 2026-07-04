
<?php $__env->startSection('title', $item->exists ? 'Edit Hero Slider' : 'Create Hero Slider'); ?>
<?php $__env->startSection('page-heading', $item->exists ? 'Edit Hero Slider' : 'Create Hero Slider'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($item->exists ? route('admin.sliders.update', $item) : route('admin.sliders.store')); ?>" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3"><?php echo csrf_field(); ?> <?php if($item->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<?php $__currentLoopData = ['heading'=>'Heading','sub_heading'=>'Sub Heading','button_text'=>'Button Text','button_link'=>'Button Link','sort_order'=>'Sort Order']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-6"><label class="form-label"><?php echo e($label); ?></label><input class="form-control <?php $__errorArgs = [$field];
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
<div class="col-12"><label class="form-label">Description</label><textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="description" rows="4"><?php echo e(old('description',$item->description)); ?></textarea><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
<div class="col-md-6"><label class="form-label">Image</label><input type="file" class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="image"><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php if($item->image): ?><img src="<?php echo e(Storage::url($item->image)); ?>" class="img-thumbnail mt-2" style="max-height:120px" alt="Current image"><?php endif; ?></div>
<div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" <?php if(old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Active</option><option value="0" <?php if(! old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Inactive</option></select></div>
</div><div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="<?php echo e(route('admin.sliders.index')); ?>">Back</a><button class="btn btn-primary-brand">Save</button></div></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/pages/sliders/form.blade.php ENDPATH**/ ?>