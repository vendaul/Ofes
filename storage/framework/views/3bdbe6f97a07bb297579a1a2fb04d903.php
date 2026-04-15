<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add Student to <?php echo e($classSchedule->subject?->name ?? 'Unknown Subject'); ?></h1>
    <p>Class: <?php echo e($classSchedule->section?->name ?? 'Unknown Section'); ?> | <?php echo e($classSchedule->ay); ?> <?php echo e($classSchedule->term); ?></p>
</div>

<?php $activeTab = old('mode', 'existing') === 'new' ? 'new' : 'existing'; ?>

<div class="card">
    <div class="card-header text-white bg-primary">
        <ul class="nav nav-tabs card-header-tabs" id="addStudentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e($activeTab === 'existing' ? 'active' : ''); ?>" id="tab-existing" data-bs-toggle="tab" data-bs-target="#pane-existing" type="button" role="tab">
                    <i class="fas fa-search"></i> Pick Existing Student
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo e($activeTab === 'new' ? 'active' : ''); ?>" id="tab-new" data-bs-toggle="tab" data-bs-target="#pane-new" type="button" role="tab">
                    <i class="fas fa-user-plus"></i> Create New Student
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content" id="addStudentTabsContent">

        
        <div class="tab-pane fade <?php echo e($activeTab === 'existing' ? 'show active' : ''); ?>" id="pane-existing" role="tabpanel">
            <?php if(count($availableStudents) > 0): ?>
            <form action="<?php echo e(route('class-schedule-students.store', $classSchedule->id)); ?>" method="POST" class="mt-2">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="mode" value="existing">

                <div class="mb-3">
                    <label for="user_student_id" class="form-label">Select Student <span class="text-danger">*</span></label>
                    <select name="user_student_id" id="user_student_id" class="form-select <?php $__errorArgs = ['user_student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <option value="">-- Select a student --</option>
                        <?php $__currentLoopData = $availableStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>" <?php echo e(old('user_student_id') == $s->id ? 'selected' : ''); ?>>
                            <?php echo e($s->sid); ?> — <?php echo e($s->fname); ?> <?php echo e($s->lname); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['user_student_id'];
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

                <input type="hidden" name="class_type" value="Regular">
                <input type="hidden" name="class_status" value="P">
                <input type="hidden" name="remark" value="ENROLLED">

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="enroll_all_subjects" id="enroll_all_subjects_existing" value="1" <?php echo e(old('enroll_all_subjects') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="enroll_all_subjects_existing">
                        Enroll in all subjects in this section/year level/SY/term
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="enroll_specific_subjects" id="enroll_specific_subjects_existing" value="1" <?php echo e(old('enroll_specific_subjects') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="enroll_specific_subjects_existing">
                        Enroll in specific subjects only
                    </label>
                </div>

                <div class="border rounded p-3 mb-3 subject-picker" id="subject_picker_existing" style="display:none;">
                    <small class="text-muted d-block mb-2">Select one or more subjects:</small>
                    <?php $__empty_1 = true; $__currentLoopData = $sectionSubjectSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="selected_schedule_ids[]" id="existing_schedule_<?php echo e($schedule->id); ?>" value="<?php echo e($schedule->id); ?>" <?php echo e(in_array((string) $schedule->id, array_map('strval', old('selected_schedule_ids', [])), true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="existing_schedule_<?php echo e($schedule->id); ?>">
                            <?php echo e($schedule->subject?->code ?? 'N/A'); ?> - <?php echo e($schedule->subject?->name ?? 'Unknown Subject'); ?>

                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <small class="text-muted">No other subjects found for this section/year level/SY/term.</small>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Student</button>
                    <a href="<?php echo e(route('class-schedule-students.index', $classSchedule->id)); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
            <?php else: ?>
            <div class="alert alert-info mt-2 mb-0">
                <i class="fas fa-info-circle"></i> All available students are already enrolled in this class schedule.
            </div>
            <div class="mt-3">
                <a href="<?php echo e(route('class-schedule-students.index', $classSchedule->id)); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            <?php endif; ?>
        </div>

        
        <div class="tab-pane fade <?php echo e($activeTab === 'new' ? 'show active' : ''); ?>" id="pane-new" role="tabpanel">
            <form action="<?php echo e(route('class-schedule-students.store', $classSchedule->id)); ?>" method="POST" class="mt-2">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="mode" value="new">

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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('sid')); ?>" required>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('lname')); ?>" required>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fname')); ?>" required>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('mname')); ?>">
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

                <div class="mb-3">
                    <label for="email_new" class="form-label">Email <span class="text-muted">(optional)</span></label>
                    <input type="email" name="email" id="email_new" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>">
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

                <input type="hidden" name="class_type" value="Regular">

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="enroll_all_subjects" id="enroll_all_subjects_new" value="1" <?php echo e(old('enroll_all_subjects') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="enroll_all_subjects_new">
                        Enroll in all subjects in this section/year level/SY/term
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="enroll_specific_subjects" id="enroll_specific_subjects_new" value="1" <?php echo e(old('enroll_specific_subjects') ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="enroll_specific_subjects_new">
                        Enroll in specific subjects only
                    </label>
                </div>

                <div class="border rounded p-3 mb-3 subject-picker" id="subject_picker_new" style="display:none;">
                    <small class="text-muted d-block mb-2">Select one or more subjects:</small>
                    <?php $__empty_1 = true; $__currentLoopData = $sectionSubjectSchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="selected_schedule_ids[]" id="new_schedule_<?php echo e($schedule->id); ?>" value="<?php echo e($schedule->id); ?>" <?php echo e(in_array((string) $schedule->id, array_map('strval', old('selected_schedule_ids', [])), true) ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="new_schedule_<?php echo e($schedule->id); ?>">
                            <?php echo e($schedule->subject?->code ?? 'N/A'); ?> - <?php echo e($schedule->subject?->name ?? 'Unknown Subject'); ?>

                        </label>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <small class="text-muted">No other subjects found for this section/year level/SY/term.</small>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Create &amp; Enroll</button>
                    <a href="<?php echo e(route('class-schedule-students.index', $classSchedule->id)); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function wireSubjectPicker(allId, specificId, pickerId) {
            const allCheckbox = document.getElementById(allId);
            const specificCheckbox = document.getElementById(specificId);
            const picker = document.getElementById(pickerId);
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
        }

        wireSubjectPicker('enroll_all_subjects_existing', 'enroll_specific_subjects_existing', 'subject_picker_existing');
        wireSubjectPicker('enroll_all_subjects_new', 'enroll_specific_subjects_new', 'subject_picker_new');
    });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/class_schedules/students/create.blade.php ENDPATH**/ ?>