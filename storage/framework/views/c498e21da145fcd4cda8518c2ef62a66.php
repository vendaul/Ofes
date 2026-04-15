<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-eye"></i> Workload for
        <?php echo e(optional($instructor)->fname ?? 'Unknown'); ?>

        <?php echo e(optional($instructor)->lname ?? ''); ?>

    </h1>
    <p>Teaching assignments associated with this instructor</p>
</div>

<div class="card">
    <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="fas fa-list"></i> Class Workload</span>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?php echo e(route('assignments.create', ['instructor_id' => $instructor->id, 'college_id' => $instructor->college, 'area_code' => $instructor->areacode])); ?>" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Add Assignment
            </a>
            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#importWorkloadModal">
                <i class="fas fa-file-import"></i> Import Workload
            </button>
            
            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#importClassListModal">
                <i class="fas fa-file-excel"></i> Import Class List
            </button>
            <a href="<?php echo e(route('instructors.index')); ?>" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Instructors
            </a>
        </div>
    </div>
    <?php if($schedules->count() > 0): ?>
    <div class="px-3 pt-3 pb-0">
        <div class="alert alert-info alert-dismissible fade show mb-2 py-2">
            <i class="fas fa-info-circle"></i> <strong>Tip:</strong> Click on a <span class="badge bg-success">Section</span> badge to view and manage the list of enrolled students.
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-code"></i> Course Code</th>
                    <th><i class="fas fa-book"></i> Course</th>
                    <th><i class="fas fa-layer-group"></i> Year/Section</th>
                    <th><i class="fas fa-calendar"></i> Academic Year</th>
                    <th><i class="fas fa-calendar-alt"></i> Term</th>
                    <th style="width: 150px;"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <span class="badge bg-primary text-white"><?php echo e($a->subject ? $a->subject->code : 'N/A'); ?></span>
                    </td>
                    <td>
                        <?php echo e($a->subject ? $a->subject->name : 'Unknown Subject'); ?>

                    </td>
                    <td>
                        <a href="<?php echo e(route('class-schedule-students.index', $a->id)); ?>" class="text-decoration-none">
                            <span class="badge bg-success"><?php echo e($a->section ? $a->section->name : 'Unknown Section'); ?></span>
                        </a>
                    </td>
                    <td>
                        <span class="badge bg-primary"><?php echo e($a->ay); ?></span>
                    </td>
                    <td>
                        <span class="badge bg-warning text-dark"><?php echo e($a->term); ?></span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('class-schedules.edit', $a->id)); ?>" class="btn btn-sm btn-warning me-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="<?php echo e(route('class-schedules.destroy', $a->id)); ?>" style="display: inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class schedule?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle"></i> No assignments found for this instructor.
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Import Workload Modal -->
<div class="modal fade" id="importWorkloadModal" tabindex="-1" aria-labelledby="importWorkloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('instructors.workload.import', $instructor->id)); ?>" method="POST" enctype="multipart/form-data" id="importWorkloadForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importWorkloadModalLabel">
                        <i class="fas fa-file-import"></i> Import Workload
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border mb-3 py-2">
                        <div class="small mb-1">Supported columns:</div>
                        <code>Course, Year &amp; Section, Course Code, COURSE TITLE</code>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="import_school_year" class="form-label">School Year</label>
                            <input type="text" id="import_school_year" name="school_year" class="form-control" value="<?php echo e(old('school_year', $activePeriod->year ?? '')); ?>" placeholder="e.g. 2025-2026" required>
                        </div>
                        <div class="col-md-6">
                            <label for="import_semester" class="form-label">Semester / Term</label>
                            <input type="text" id="import_semester" name="semester" class="form-control" value="<?php echo e(old('semester', $activePeriod->term ?? '')); ?>" placeholder="e.g. First" required>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label for="import_area_code" class="form-label">Area</label>
                            <select id="import_area_code" name="area_code" class="form-select" required>
                                <option value="">Select Area</option>
                                <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($code); ?>" <?php echo e((string) old('area_code', $defaultAreaCode) === (string) $code ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="import_college_id" class="form-label">College</label>
                            <select id="import_college_id" name="college_id" class="form-select" required>
                                <option value="">Select College</option>
                                <?php $__currentLoopData = $allColleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $college): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($college->id); ?>" data-area-code="<?php echo e($college->area_code); ?>" <?php echo e((string) old('college_id', $defaultCollegeId) === (string) $college->id ? 'selected' : ''); ?>><?php echo e($college->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label for="import_program_id" class="form-label">Default Program (optional)</label>
                            <select id="import_program_id" name="program_id" class="form-select">
                                <option value="">Select Program</option>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($program->id); ?>" <?php echo e((string) old('program_id', $defaultProgramId ?? '') === (string) $program->id ? 'selected' : ''); ?>><?php echo e($program->course_program ?: $program->code); ?> - <?php echo e($program->name ?: 'N/A'); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="import_curriculum_id" class="form-label">Default Curriculum (optional)</label>
                            <select id="import_curriculum_id" name="curriculum_id" class="form-select">
                                <option value="">Select Curriculum</option>
                                <?php $__currentLoopData = $curriculums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($curriculum->id); ?>" data-course-id="<?php echo e($curriculum->course_id); ?>" <?php echo e((string) old('curriculum_id', $defaultCurriculumId ?? '') === (string) $curriculum->id ? 'selected' : ''); ?>><?php echo e($curriculum->code); ?> - <?php echo e($curriculum->desc ?: 'N/A'); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-12">
                            <label for="import_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                            <input type="file" id="import_file" name="import_file" class="form-control" accept=".xlsx,.xls,.csv,text/csv" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-file-import"></i> Import Workload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Class List Modal -->
<div class="modal fade" id="importClassListModal" tabindex="-1" aria-labelledby="importClassListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importClassListModalLabel">
                    <i class="fas fa-file-excel"></i> Import Class List
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('students.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="instructor_id" value="<?php echo e($instructor->id); ?>">
                <div class="modal-body">
                    <div class="alert alert-light border mb-3 py-2">
                        <div class="small mb-1">Expected columns:</div>
                        <code>Course, Year and Section, ID, Name</code>
                        <div class="form-text mt-1">Name format can be <code>LASTNAME, FIRSTNAME MIDDLE</code>.</div>
                    </div>

                    <div class="mb-3">
                        <label for="class_list_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" id="class_list_file" name="csv_file" accept=".xlsx,.xls,.csv,text/csv" required>
                    </div>

                    <div class="mb-0">
                        <label for="class_list_section_name" class="form-label">Default Section (optional)</label>
                        <input type="text" class="form-control" id="class_list_section_name" name="section_name" placeholder="Used only when Year and Section cell is blank" list="sections-list">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-file-import"></i> Import Class List
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Students Modal -->
<div class="modal fade" id="importStudentsModal" tabindex="-1" aria-labelledby="importStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStudentsModalLabel">
                    <i class="fas fa-upload"></i> Import Students
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('students.import')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".xlsx,.xls,.csv,text/csv" required>
                        <div class="form-text">
                            Supported columns: ID Number + Fullname, or student_id/sid + fname + lname. Email and password are optional.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="section_name" class="form-label">Assign to Section</label>
                        <input type="text" class="form-control" id="section_name" name="section_name" placeholder="Type or select section..." required list="sections-list">
                        <datalist id="sections-list">
                            <?php $__currentLoopData = ($instructorSections ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($section->name); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                        <div class="form-text">Only sections currently assigned to this instructor are listed.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const importArea = document.getElementById('import_area_code');
        const importCollege = document.getElementById('import_college_id');
        const importProgram = document.getElementById('import_program_id');
        const importCurriculum = document.getElementById('import_curriculum_id');

        const curriculumOptions = importCurriculum ? Array.from(importCurriculum.options).map(function(option) {
            return {
                value: option.value
                , text: option.textContent
                , courseId: option.dataset.courseId || ''
            };
        }) : [];

        function syncImportCollegeOptions() {
            if (!importArea || !importCollege) return;

            const selectedArea = importArea.value || '';
            Array.from(importCollege.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }
                const optionArea = option.dataset.areaCode || '';
                option.hidden = selectedArea !== '' && optionArea !== selectedArea;
            });

            const selected = importCollege.options[importCollege.selectedIndex];
            if (selected && selected.dataset.areaCode && selected.dataset.areaCode !== selectedArea) {
                importCollege.value = '';
            }
        }

        function syncImportCurriculumOptions() {
            if (!importProgram || !importCurriculum) return;

            const selectedProgram = importProgram.value || '';
            const previous = importCurriculum.value;
            importCurriculum.innerHTML = '';

            curriculumOptions.forEach(function(entry) {
                if (entry.value === '') {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = entry.text;
                    importCurriculum.appendChild(opt);
                    return;
                }
                if (!selectedProgram || entry.courseId === selectedProgram) {
                    const opt = document.createElement('option');
                    opt.value = entry.value;
                    opt.textContent = entry.text;
                    opt.dataset.courseId = entry.courseId;
                    importCurriculum.appendChild(opt);
                }
            });

            const exists = Array.from(importCurriculum.options).some(function(option) {
                return option.value === previous;
            });
            if (exists) {
                importCurriculum.value = previous;
            }
        }

        if (importArea) {
            importArea.addEventListener('change', syncImportCollegeOptions);
            syncImportCollegeOptions();
        }
        if (importProgram) {
            importProgram.addEventListener('change', syncImportCurriculumOptions);
            syncImportCurriculumOptions();
        }
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/instructors/workload.blade.php ENDPATH**/ ?>