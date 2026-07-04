
<?php $__env->startSection('title','General Settings'); ?>
<?php $__env->startSection('page-heading','General Settings'); ?>
<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('admin.settings.general.update')); ?>" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<?php $__currentLoopData = ['company_name'=>'Company Name','tagline'=>'Tagline','email'=>'Email','phone'=>'Phone','default_language'=>'Default Language','timezone'=>'Timezone','copyright_text'=>'Copyright Text','developer_name'=>'Developer Name','developer_link'=>'Developer Link']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-6"><label class="form-label"><?php echo e($label); ?></label><input class="form-control <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($field); ?>" value="<?php echo e(old($field, $setting->$field)); ?>"><?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="3"><?php echo e(old('address',$setting->address)); ?></textarea></div>
<?php $__currentLoopData = ['logo'=>'Logo','favicon'=>'Favicon','profile_pdf'=>'Profile PDF']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="col-md-4"><label class="form-label"><?php echo e($label); ?></label><input type="file" class="form-control <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="<?php echo e($field); ?>"><?php if($setting->$field): ?><div class="form-help mt-1">Current: <a href="<?php echo e(asset('storage/'.$setting->$field)); ?>" target="_blank">View file</a></div><?php endif; ?> <?php $__errorArgs = [$field];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary-brand">Save Settings</button></div></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/pages/settings/general.blade.php ENDPATH**/ ?>