<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Edit Student</h1>
    <p>Class: <?php echo e($classSchedule->subject?->name ?? 'Unknown Subject'); ?> — <?php echo e($classSchedule->section?->name ?? 'Unknown Section'); ?> | <?php echo e($classSchedule->ay); ?> <?php echo e($classSchedule->term); ?></p>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-user-edit"></i> Edit Student Details
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('class-schedule-students.update', [$classSchedule->id, $enrollment->id])); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="sid" class="form-label">Student Number <span class="text-danger">*</span></label>
                    <input type="text" name="sid" id="sid" class="form-control <?php $__errorArgs = ['sid'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('sid', $student->sid)); ?>" required>
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
                <div class="col-md-8">
                    <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="lname" id="lname" class="form-control <?php $__errorArgs = ['lname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('lname', $student->lname)); ?>" required>
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
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="fname" id="fname" class="form-control <?php $__errorArgs = ['fname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fname', $student->fname)); ?>" required>
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
                <div class="col-md-6">
                    <label for="mname" class="form-label">Middle Name</label>
                    <input type="text" name="mname" id="mname" class="form-control <?php $__errorArgs = ['mname'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('mname', $student->mname)); ?>">
                    <?php $__errorArgs = ['mname'];
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
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
                <input type="email" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $student->email)); ?>">
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

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="apply_all_subjects" id="apply_all_subjects" value="1" <?php echo e(old('apply_all_subjects') ? 'checked' : ''); ?>>
                <label class="form-check-label" for="apply_all_subjects">
                    Apply to all subjects in this section/year level/SY/term
                </label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="apply_specific_subjects" id="apply_specific_subjects" value="1" <?php echo e(old('apply_specific_subjects') ? 'checked' : ''); ?>>
                <label class="form-check-label" for="apply_specific_subjects">
                    Apply to specific subjects only
                </label>
            </div>

            <div class="border rounded p-3 mb-4" id="subject_picker_edit" style="display:none;">
                <small class="text-muted d-block mb-2">Select one or more subjects:</small>
                <?php $__empty_1 = true; $__currentLoopData = $sectionSubjectSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                $selectedValues = old('selected_schedule_ids', $selectedScheduleIds ?? []);
                $selectedStrings = array_map('strval', (array) $selectedValues);
                ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_schedule_ids[]" id="edit_schedule_<?php echo e($schedule->id); ?>" value="<?php echo e($schedule->id); ?>" <?php echo e(in_array((string) $schedule->id, $selectedStrings, true) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="edit_schedule_<?php echo e($schedule->id); ?>">
                        <?php echo e($schedule->subject?->code ?? 'N/A'); ?> - <?php echo e($schedule->subject?->name ?? 'Unknown Subject'); ?>

                    </label>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <small class="text-muted">No subjects found for this section/year level/SY/term.</small>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="<?php echo e(route('class-schedule-students.index', $classSchedule->id)); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const allCheckbox = document.getElementById('apply_all_subjects');
        const specificCheckbox = document.getElementById('apply_specific_subjects');
        const picker = document.getElementById('subject_picker_edit');
        if (!allCheckbox || !specificCheckbox || !picker) return;

        const subjectCheckboxes = Array.from(picker.querySelectorAll('input[name="selected_schedule_ids[]"]'));

        const toggle = () => {
            if (allCheckbox.checked) {
                picker.style.display = 'block';
                subjectCheckboxes.forEach(cb => {
                    cb.checked = true;
                    cb.disabled = true;
                });
                return;
            }

            subjectCheckboxes.forEach(cb => {
                cb.disabled = false;
            });

            picker.style.display = specificCheckbox.checked ? 'block' : 'none';
        };

        allCheckbox.addEventListener('change', function() {
            if (allCheckbox.checked) {
                specificCheckbox.checked = false;
            }
            toggle();
        });

        specificCheckbox.addEventListener('change', function() {
            if (specificCheckbox.checked) {
                allCheckbox.checked = false;
                subjectCheckboxes.forEach(cb => {
                    cb.disabled = false;
                    cb.checked = false;
                });
            }
            toggle();
        });
        toggle();

        const form = allCheckbox.closest('form');
        if (form) {
            const submitButton = form.querySelector('button[type="submit"]');

            const updateSubmitState = () => {
                const hasSelection = subjectCheckboxes.some(cb => cb.checked);
                if (submitButton) {
                    submitButton.disabled = specificCheckbox.checked && !hasSelection;
                }
            };

            subjectCheckboxes.forEach(cb => cb.addEventListener('change', updateSubmitState));

            form.addEventListener('submit', function(event) {
                if (specificCheckbox.checked) {
                    const hasSelection = subjectCheckboxes.some(cb => cb.checked);
                    if (!hasSelection) {
                        event.preventDefault();
                        event.stopImmediatePropagation();
                        alert('Please select at least one subject before saving.');
                    }
                }
            });

            updateSubmitState();
        }
    });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/class_schedules/students/edit.blade.php ENDPATH**/ ?>