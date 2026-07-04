
<?php $__env->startSection('title',$item->exists?'Edit Gallery Image':'Create Gallery Image'); ?>
<?php $__env->startSection('page-heading',$item->exists?'Edit Gallery Image':'Create Gallery Image'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" enctype="multipart/form-data" action="<?php echo e($item->exists ? route('admin.gallery-images.update',$item) : route('admin.gallery-images.store')); ?>" class="card border-0 shadow-sm"><div class="card-body row g-3"><?php echo csrf_field(); ?> <?php if($item->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<?php $__currentLoopData = ['title'=>'Title','category'=>'Category','sort_order'=>'Sort Order']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-4"><label class="form-label"><?php echo e($label); ?></label><input class="form-control <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($field); ?>" value="<?php echo e(old($field,$item->$field)); ?>"><?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-6"><label class="form-label">Image <?php echo e($item->exists ? '' : '*'); ?></label><input type="file" class="form-control <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="image" <?php if(! $item->exists): echo 'required'; endif; ?>><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php if($item->image): ?><img src="<?php echo e(Storage::url($item->image)); ?>" class="img-thumbnail mt-2" style="max-height:140px" alt="Current image"><?php endif; ?></div><div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" <?php if(old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Active</option><option value="0" <?php if(! old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Inactive</option></select></div></div><div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="<?php echo e(route('admin.gallery-images.index')); ?>">Back</a><button class="btn btn-primary-brand">Save</button></div></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/pages/gallery-images/form.blade.php ENDPATH**/ ?>