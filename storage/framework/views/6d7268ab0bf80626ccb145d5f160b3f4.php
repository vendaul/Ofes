<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-layer-group"></i> Add New Section</h1>
    <p>Create a new section</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form "></i> Section Information
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('sections.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="tab" value="<?php echo e($activeTab ?? request('tab', 'sections')); ?>">

                    <div class="form-group mb-3">
                        <label for="area_code" class="form-label">Area</label>
                        <select class="form-control <?php $__errorArgs = ['area_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="area_code" name="area_code" required>
                            <option value="">Select Area</option>
                            <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $areaCode => $areaName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($areaCode); ?>" <?php echo e(old('area_code', $selectedArea ?? session('selected_area')) == $areaCode ? 'selected' : ''); ?>>
                                <?php echo e($areaName); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['area_code'];
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
                        <label for="college_id" class="form-label">College</label>
                        <select class="form-control <?php $__errorArgs = ['college_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="college_id" name="college_id" required>
                            <option value="">Select College</option>
                            <?php $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $college): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($college->id); ?>" data-area-code="<?php echo e($college->area_code); ?>" <?php echo e((string) old('college_id', $selectedCollege ?? session('selected_college')) === (string) $college->id ? 'selected' : ''); ?>>
                                <?php echo e($college->name); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['college_id'];
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
                        <label for="name" class="form-label">Section Name</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" placeholder="e.g., Section A, BS-CS-2A" required>
                        <?php $__errorArgs = ['name'];
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

                    <div class="form-group mb-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-control <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="First" <?php echo e(old('year') === 'First' ? 'selected' : ''); ?>>First</option>
                            <option value="Second" <?php echo e(old('year') === 'Second' ? 'selected' : ''); ?>>Second</option>
                            <option value="Third" <?php echo e(old('year') === 'Third' ? 'selected' : ''); ?>>Third</option>
                            <option value="Fourth" <?php echo e(old('year') === 'Fourth' ? 'selected' : ''); ?>>Fourth</option>
                        </select>
                        <?php $__errorArgs = ['year'];
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

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Section
                        </button>
                        <a href="<?php echo e(route('sections.index')); ?>" class="btn btn-secondary">
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
                    <li><i class="fas fa-check text-success"></i> <strong>Section Name</strong> should be descriptive</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>Year Level</strong> is typically 1-4</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Example: "BS-CS-2A" for 2nd year Computer Science</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('area_code');
        const collegeSelect = document.getElementById('college_id');

        function syncCollegeOptions() {
            const selectedArea = areaSelect.value;

            Array.from(collegeSelect.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                option.hidden = selectedArea !== '' && option.dataset.areaCode !== selectedArea;
            });

            const selectedOption = collegeSelect.options[collegeSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.areaCode && selectedOption.dataset.areaCode !== selectedArea) {
                collegeSelect.value = '';
            }
        }

        areaSelect.addEventListener('change', syncCollegeOptions);
        syncCollegeOptions();
    });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/sections/create.blade.php ENDPATH**/ ?>