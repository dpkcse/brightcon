<?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>
<?php if($errors->any()): ?><div class="alert alert-danger"><strong>Please fix the errors below.</strong></div><?php endif; ?>
<?php /**PATH D:\APPLICATION\brightcon\resources\views/admin/partials/alerts.blade.php ENDPATH**/ ?>