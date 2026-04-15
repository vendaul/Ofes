<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add Student</h1>
    <p>Create a student record</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form"></i> Create New Student
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('students.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="area_code" value="<?php echo e(old('area_code', $selectedArea ?? session('selected_area'))); ?>">
                    <input type="hidden" name="college_code" value="<?php echo e(old('college_code', $selectedCollege ?? session('selected_college'))); ?>">
                    <?php if(!empty($preselectedSectionId)): ?>
                    <input type="hidden" name="section_id" value="<?php echo e($preselectedSectionId); ?>">
                    <?php endif; ?>

                    <div class="form-group mb-3">
                        <label for="student_number" class="form-label">Student Number</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['sid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="student_number" name="sid" placeholder="Enter student number" required>
                        <?php $__errorArgs = ['sid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['fname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="first_name" name="fname" placeholder="Enter first name" required>
                        <?php $__errorArgs = ['fname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['lname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="last_name" name="lname" placeholder="Enter last name" required>
                        <?php $__errorArgs = ['lname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email" placeholder="Enter student email" value="<?php echo e(old('email')); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Student
                    </button>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="card">
        <div class="card-header text-white bg-primary">
            <i class="fas fa-lightbulb"></i> Tips
        </div>
        <div class="card-body">
            <ul class="list-unstyled">
                <li><i class="fas fa-check text-success"></i> <strong>Student ID</strong> must be unique</li>
                <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>First Name</strong> and <strong>Last Name</strong> are required</li>
                <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>Email</strong> is optional but recommended</li>
                <li class="mt-2"><i class="fas fa-check text-success"></i> Section assignment is managed in class schedules</li>
            </ul>
        </div>
    </div>
</div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/students/create.blade.php ENDPATH**/ ?>