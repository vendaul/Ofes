<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-tasks"></i> Add Teaching Assignment</h1>
    <p>Create a new instructor teaching assignment</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form"></i> Assignment Information
            </div>
            <div class="card-body">
                <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <strong>Errors found:</strong>
                    <ul class="mb-0 mt-2">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <form action="<?php echo e(route('assignments.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <input type="hidden" name="area_code" value="<?php echo e(old('area_code', $selectedAreaId ?? request('area_code') ?? '')); ?>">
                    <input type="hidden" name="college_id" value="<?php echo e(old('college_id', $selectedCollegeId ?? request('college_id') ?? '')); ?>">

                    <div class="form-group mb-3">
                        <label for="instructor_id" class="form-label">Instructor</label>
                        <select class="form-select <?php $__errorArgs = ['instructor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="instructor_id" name="instructor_id" required>
                            <option value="">-- Select an Instructor --</option>
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->id); ?>" <?php if(old('instructor_id', request('instructor_id'))==$i->id): ?> selected <?php endif; ?>>
                                <?php echo e($i->fname); ?> <?php echo e($i->lname); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['instructor_id'];
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
                        <label for="program_name" class="form-label">Program</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control <?php $__errorArgs = ['program_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="program_name" name="program_name" placeholder="Search or type program" autocomplete="off" list="programs-list" value="<?php echo e(old('program_name', optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->course_program ? optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->course_program . ' (' . optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->code . ') - ' . optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->name : optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->name ?? '')); ?>">
                        </div>
                        <datalist id="programs-list">
                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($program->course_program); ?> (<?php echo e($program->code); ?>) - <?php echo e($program->name); ?>" data-id="<?php echo e($program->id); ?>"></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                        <input type="hidden" id="program_id" name="program_id" value="<?php echo e(old('program_id', $selectedProgramId ?? '')); ?>">
                    <input type="hidden" id="college_id" name="college_id" value="<?php echo e(old('college_id', $selectedCollegeId ?? request('college_id'))); ?>">
                        <?php $__errorArgs = ['program_id'];
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
                        <label for="curriculum_id" class="form-label">Curriculum</label>
                        <select class="form-select <?php $__errorArgs = ['curriculum_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="curriculum_id" name="curriculum_id">
                            <option value="">-- Select Curriculum --</option>
                            <?php $__currentLoopData = $curriculums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($curriculum->id); ?>" <?php echo e(old('curriculum_id', $selectedCurriculumId ?? '') == $curriculum->id ? 'selected' : ''); ?>><?php echo e($curriculum->code ?? $curriculum->name ?? $curriculum->id); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['curriculum_id'];
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
                        <label for="year_level" class="form-label">Year Level</label>
                        <select class="form-select <?php $__errorArgs = ['year_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="year_level" name="year_level" required>
                            <option value="">-- Select Year Level --</option>
                            <?php $__currentLoopData = $yearLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($level); ?>" <?php if(old('year_level', $selectedYearLevel ?? '' )==$level): ?> selected <?php endif; ?>><?php echo e($level); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['year_level'];
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
                        <label for="subject_name" class="form-label">Subject</label>
                        <small class="form-text text-muted mb-1">Showing subjects for selected year level and active period.</small>
                        <input type="text" class="form-control <?php $__errorArgs = ['subject_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="subject_name" name="subject_name" placeholder="Type or select subject..." required list="subjects-list">
                        <datalist id="subjects-list">
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $primaryCurriculum = $s->curriculumSubjects->first()?->curriculum;
                                $programId = $primaryCurriculum?->course?->id ?? '';
                                $curriculumId = $primaryCurriculum?->id ?? '';
                            ?>
                            <option value="<?php echo e($s->code); ?> - <?php echo e($s->name); ?>" data-year="<?php echo e($s->subject_year ?? ''); ?>" data-program="<?php echo e($programId); ?>" data-curriculum="<?php echo e($curriculumId); ?>"></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                        <script id="subjects-json" type="application/json">
                            <?php echo json_encode($subjectOptions, 15, 512) ?>
                        </script>
                        <?php $__errorArgs = ['subject_name'];
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
                        <label for="section_name" class="form-label">Section</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['section_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="section_name" name="section_name" placeholder="Type or select section..." required list="sections-list">
                        <datalist id="sections-list">
                            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sec->name); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                        <?php $__errorArgs = ['section_name'];
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

                    <?php $periodLocked = !empty($activePeriod); ?>
                    <div class="form-group mb-3">
                        <label for="school_year" class="form-label">School Year
                            <?php if($periodLocked): ?>
                            <span class="badge bg-info">Locked to active period</span>
                            <?php endif; ?>
                        </label>
                        <select class="form-select <?php $__errorArgs = ['school_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="school_year" name="school_year" required <?php if($periodLocked): ?> disabled <?php endif; ?>>
                            <option value="">-- Select School Year --</option>
                            <?php $__currentLoopData = $schoolYears ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($year); ?>" <?php echo e(old('school_year', $activePeriod->year ?? '') == $year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($periodLocked): ?>
                        <input type="hidden" name="school_year" value="<?php echo e($activePeriod->year); ?>">
                        <?php endif; ?>
                        <?php $__errorArgs = ['school_year'];
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
                        <label for="semester" class="form-label">Semester
                            <?php if($periodLocked): ?>
                            <span class="badge bg-info">Locked to active period</span>
                            <?php endif; ?>
                        </label>
                        <select class="form-select <?php $__errorArgs = ['semester'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="semester" name="semester" required <?php if($periodLocked): ?> disabled <?php endif; ?>>
                            <option value="">-- Select Semester --</option>
                            <?php $__currentLoopData = $semesters ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($semester); ?>" <?php echo e(old('semester', $activePeriod->term ?? '') == $semester ? 'selected' : ''); ?>><?php echo e($semester); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if($periodLocked): ?>
                        <input type="hidden" name="semester" value="<?php echo e($activePeriod->term); ?>">
                        <?php endif; ?>
                        <?php $__errorArgs = ['semester'];
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
                            <i class="fas fa-save"></i> Create Assignment
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
                    <li><i class="fas fa-check text-success"></i> All fields are required</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Ensure instructor is available</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Use format: "2024-2025" for year</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Use: "First" or "Second" for semester</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearLevelSelect = document.getElementById('year_level');
        const subjectInput = document.getElementById('subject_name');
        const dataList = document.getElementById('subjects-list');
        const subjectsData = <?php echo json_encode($subjectOptions, 15, 512) ?>;

        const programNameInput = document.getElementById('program_name');
        const programIdInput = document.getElementById('program_id');
        const curriculumSelect = document.getElementById('curriculum_id');

        const programsData = <?php echo json_encode($programOptions, 15, 512) ?>;

        const allCurriculums = <?php echo json_encode($curriculumOptions, 15, 512) ?>;

        const curriculumIndex = allCurriculums.reduce((acc, c) => {
            acc[c.id] = c;
            return acc;
        }, {});

        function normalizeString(v) {
            return (v || '').toString().trim().toLowerCase();
        }

        function syncProgramIdFromName() {
            const typed = normalizeString(programNameInput.value);

            // exact label match first
            let selected = programsData.find(p => normalizeString(p.label) === typed);

            // fallback prefix match (typing "BSIT" should match "BSIT (...) - ...")
            if (!selected && typed.length > 0) {
                selected = programsData.find(p => normalizeString(p.label).startsWith(typed));
            }

            if (!selected && curriculumSelect && curriculumSelect.value) {
                const curriculum = curriculumIndex[curriculumSelect.value];
                if (curriculum && curriculum.course_id) {
                    selected = programsData.find(p => String(p.id) === String(curriculum.course_id));
                }
            }

            programIdInput.value = selected ? selected.id : '';

            if (selected) {
                programNameInput.value = selected.label;
            }
        }

        function updateCurriculumOptions() {
            if (!curriculumSelect) {
                return;
            }

            const selectedProgram = programIdInput.value;
            const selectedCurriculum = curriculumSelect.value;
            curriculumSelect.innerHTML = '<option value="">-- Select Curriculum --</option>';

            const filtered = selectedProgram
                ? allCurriculums.filter(c => String(c.course_id) === String(selectedProgram))
                : allCurriculums;

            filtered.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.label;
                curriculumSelect.appendChild(option);
            });

            if (selectedProgram && selectedCurriculum && !filtered.some(c => String(c.id) === String(selectedCurriculum))) {
                curriculumSelect.value = '';
            }
        }

        function renderSubjectOptions() {
            const selectedYear = yearLevelSelect.value;
            const selectedProgram = programIdInput ? programIdInput.value : '';
            const selectedCurriculum = curriculumSelect ? curriculumSelect.value : '';
            dataList.innerHTML = '';

            subjectsData.forEach(item => {
                const yearMatch = !selectedYear || String(item.year) === String(selectedYear);
                const programMatch = !selectedProgram || String(item.program) === String(selectedProgram);
                const curriculumMatch = !selectedCurriculum || String(item.curriculum) === String(selectedCurriculum);

                if (yearMatch && programMatch && curriculumMatch) {
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.dataset.year = item.year;
                    option.dataset.program = item.program;
                    option.dataset.curriculum = item.curriculum;
                    dataList.appendChild(option);
                }
            });

            if (subjectInput.value) {
                const match = subjectsData.some(item => item.value === subjectInput.value &&
                    (!selectedYear || item.year === selectedYear) &&
                    (!selectedProgram || item.program === selectedProgram) &&
                    (!selectedCurriculum || item.curriculum === selectedCurriculum)
                );
                if (!match) {
                    subjectInput.value = '';
                }
            }
        }

        yearLevelSelect.addEventListener('change', renderSubjectOptions);
        if (programNameInput) {
            programNameInput.addEventListener('change', function () {
                syncProgramIdFromName();
                updateCurriculumOptions();
                renderSubjectOptions();
            });
            programNameInput.addEventListener('input', function () {
                if (!programNameInput.value.trim()) {
                    programIdInput.value = '';
                    updateCurriculumOptions();
                    renderSubjectOptions();
                }
            });
        }
        if (curriculumSelect) curriculumSelect.addEventListener('change', renderSubjectOptions);

        // Make sure program/curriculum sync gets triggered once on load.
        if (programNameInput) {
            syncProgramIdFromName();
            updateCurriculumOptions();
        }
        renderSubjectOptions();
    });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/assignments/create.blade.php ENDPATH**/ ?>