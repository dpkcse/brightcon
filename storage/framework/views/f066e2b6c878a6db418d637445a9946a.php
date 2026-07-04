
<?php $__env->startSection('title',$item->exists?'Edit Service':'Create Service'); ?>
<?php $__env->startSection('page-heading',$item->exists?'Edit Service':'Create Service'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" enctype="multipart/form-data" action="<?php echo e($item->exists ? route('admin.services.update',$item) : route('admin.services.store')); ?>" class="card border-0 shadow-sm"><div class="card-body row g-3"><?php echo csrf_field(); ?> <?php if($item->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
<?php $__currentLoopData = ['title'=>'Title','slug'=>'Slug','icon_class'=>'Icon Class','sort_order'=>'Sort Order','seo_title'=>'SEO Title']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="col-md-4"><label class="form-label"><?php echo e($label); ?></label><input class="form-control <?php $__errorArgs = [$field];
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
unset($__errorArgs, $__bag); ?> <?php if($item->image): ?><img src="<?php echo e(Storage::url($item->image)); ?>" class="img-thumbnail mt-2" style="max-height:140px" alt="Current image"><?php endif; ?></div><div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" <?php if(old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Active</option><option value="0" <?php if(! old('status',$item->status ?? true)): echo 'selected'; endif; ?>>Inactive</option></select></div><div class="col-12"><label class="form-label">Short Description</label><textarea class="form-control <?php $__errorArgs = ['short_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="short_description" rows="3"><?php echo e(old('short_description',$item->short_description)); ?></textarea><?php $__errorArgs = ['short_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><div class="col-12"><label class="form-label">Full Description</label><textarea class="form-control <?php $__errorArgs = ['full_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="full_description" rows="7"><?php echo e(old('full_description',$item->full_description)); ?></textarea><?php $__errorArgs = ['full_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div><div class="col-12"><label class="form-label">SEO Description</label><textarea class="form-control <?php $__errorArgs = ['seo_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="seo_description" rows="3"><?php echo e(old('seo_description',$item->seo_description)); ?></textarea><?php $__errorArgs = ['seo_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div></div><div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="<?php echo e(route('admin.services.index')); ?>">Back</a><button class="btn btn-primary-brand">Save</button></div></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/pages/services/form.blade.php ENDPATH**/ ?>