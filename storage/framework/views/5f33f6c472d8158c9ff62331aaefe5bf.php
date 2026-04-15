<?php $__env->startSection('content'); ?>

<style>
    .subjects-sticky-wrap {
        max-height: 68vh;
        overflow: auto;
    }

    .subjects-sticky-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: #f8f9fa;
    }

</style>

<div class="container-fluid">

    <div class="page-header mb-4 d-flex justify-content-between align-items-start">
        <div>
            <h1><i class="fas fa-graduation-cap"></i> Academic Management</h1>
            <p class="text-muted">Manage subjects, sections, and students in the system</p>
        </div>
        <form id="academicFilterForm" method="GET" class="d-flex gap-2 align-items-end">
            <input type="hidden" name="tab" id="activeTabInput" value="<?php echo e($activeTab); ?>">
            <div class="form-group mb-0">
                <label for="area_code" class="form-label small mb-1">Area</label>
                <select id="area_code" name="area_code" class="form-select" onchange="submitAcademicFilter(this.form)">
                    <option value="">Select Area</option>
                    <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>" <?php echo e($code == $selectedArea ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group mb-0">
                <label for="college_id" class="form-label small mb-1">College</label>
                <select id="college_id" name="college_id" class="form-select" onchange="submitAcademicFilter(this.form)">
                    <option value="">Select College</option>
                    <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e($id == $selectedCollege ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="button" class="btn btn-outline-primary align-self-end" onclick="submitAcademicFilter(document.getElementById('academicFilterForm'))">Apply</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="subjectSectionTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'curriculum' ? 'active' : ''); ?>" id="curriculum-tab" data-bs-toggle="tab" data-bs-target="#curriculum" type="button">
                        <i class="fas fa-sitemap"></i> Program & Curriculum
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'subjects' ? 'active' : ''); ?>" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button">
                        <i class="fas fa-book"></i> Subjects
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade <?php echo e($activeTab === 'curriculum' ? 'show active' : ''); ?>" id="curriculum">
                    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 text-dark">Program and Curriculum</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#createProgramPanel" aria-expanded="false" aria-controls="createProgramPanel">
                                <i class="fas fa-plus"></i> Create Program
                            </button>
                            <button class="btn btn-success" type="button" data-bs-toggle="collapse" data-bs-target="#createCurriculumPanel" aria-expanded="false" aria-controls="createCurriculumPanel">
                                <i class="fas fa-plus"></i> Create Curriculum
                            </button>
                        </div>
                    </div>

                    <div class="collapse mb-3" id="createProgramPanel">
                        <div class="card border-primary shadow-sm">
                            <div class="card-header bg-primary text-white fw-semibold">Create Program</div>
                            <div class="card-body">
                                <form method="POST" action="<?php echo e(route('subjects.programs.store')); ?>" class="row g-2 align-items-end">
                                    <?php echo csrf_field(); ?>
                                    <div class="col-md-2">
                                        <label for="program_area" class="form-label">Area</label>
                                        <select id="program_area" name="area_code" class="form-select" required>
                                            <option value="">Select Area</option>
                                            <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($code); ?>" <?php echo e((string) $selectedArea === (string) $code ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="program_college" class="form-label">College</label>
                                        <select id="program_college" name="college_id" class="form-select" required>
                                            <option value="">Select College</option>
                                            <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($id); ?>" <?php echo e((string) $selectedCollege === (string) $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="program_code" class="form-label">Program Code</label>
                                        <input type="text" id="program_code" name="course_program" class="form-control" placeholder="e.g. BSIT" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="program_name" class="form-label">Program Name</label>
                                        <input type="text" id="program_name" name="name" class="form-control" placeholder="e.g. Bachelor of Science in IT" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="collapse mb-3" id="createCurriculumPanel">
                        <div class="card border-success shadow-sm">
                            <div class="card-header bg-success text-white fw-semibold">Create Curriculum</div>
                            <div class="card-body">
                                <form method="POST" action="<?php echo e(route('subjects.curriculums.store')); ?>" class="row g-2 align-items-end">
                                    <?php echo csrf_field(); ?>
                                    <div class="col-md-3">
                                        <label for="curr_area" class="form-label">Area</label>
                                        <select id="curr_area" name="area_code" class="form-select">
                                            <option value="">Select Area</option>
                                            <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($code); ?>" <?php echo e((string) $selectedArea === (string) $code ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="curr_college" class="form-label">College</label>
                                        <select id="curr_college" name="college_id" class="form-select">
                                            <option value="">Select College</option>
                                            <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($id); ?>" <?php echo e((string) $selectedCollege === (string) $id ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="curr_program" class="form-label">Program</label>
                                        <select id="curr_program" name="course_id" class="form-select" required>
                                            <option value="">Select Program</option>
                                            <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($program->id); ?>"><?php echo e($program->course_program ?: $program->code); ?> - <?php echo e($program->name ?: 'N/A'); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="curr_code" class="form-label">Curriculum Code</label>
                                        <input type="text" id="curr_code" name="code" class="form-control" placeholder="e.g. BSIT-2024" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-success w-100">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-list"></i> Program / Course View
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Program Code</th>
                                            <th>Program Name</th>
                                            <th>Area</th>
                                            <th>College</th>
                                            <th style="width: 220px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><span class="fw-semibold"><?php echo e($program->course_program ?: $program->code ?: '-'); ?></span></td>
                                            <td><?php echo e($program->name ?: '-'); ?></td>
                                            <td><?php echo e($areaOptions[$program->area_code] ?? ($program->area_code ?: '-')); ?></td>
                                            <td><?php echo e($collegeOptions[$program->college_id] ?? ($program->college_id ?: '-')); ?></td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <button type="button" class="btn btn-sm btn-warning program-edit-btn" data-bs-toggle="modal" data-bs-target="#editProgramModal" data-update-url="<?php echo e(route('subjects.programs.update', ['id' => $program->id], false)); ?>" data-program-id="<?php echo e($program->id); ?>" data-program-area-code="<?php echo e($program->area_code); ?>" data-program-college-id="<?php echo e($program->college_id); ?>" data-program-code="<?php echo e($program->course_program ?: $program->code); ?>" data-program-name="<?php echo e($program->name); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <form method="POST" action="<?php echo e(route('subjects.programs.destroy', $program->id)); ?>" onsubmit="return confirm('Delete this program?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No program/course records found.</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark text-white fw-semibold">
                            <i class="fas fa-sitemap"></i> Curriculum View
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Course Program and Name</th>
                                            <th style="width: 180px;">Curriculum Code</th>
                                            <th style="width: 240px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $curriculumsByProgramId; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $programId => $programCurriculums): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                        $programCurriculums = collect($programCurriculums)->values();
                                        $rowspan = $programCurriculums->count();
                                        $program = $programLookup->get((int) $programId);
                                        ?>

                                        <?php $__currentLoopData = $programCurriculums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $curriculum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <?php if($index === 0): ?>
                                            <td rowspan="<?php echo e($rowspan); ?>" class="align-top bg-light">
                                                <?php if($program): ?>
                                                <div class="fw-semibold"><?php echo e($program->course_program ?: $program->code); ?></div>
                                                <div class="text-muted small"><?php echo e($program->name ?: '-'); ?></div>
                                                <?php else: ?>
                                                <div class="fw-semibold">Program ID: <?php echo e($programId ?: 'N/A'); ?></div>
                                                <div class="text-muted small">No matching program row in db_courses</div>
                                                <?php endif; ?>
                                            </td>
                                            <?php endif; ?>

                                            <td>
                                                <span class="fw-semibold"><?php echo e($curriculum->code); ?></span>
                                                <?php if((int)($activeCurriculumId ?? 0) === (int)$curriculum->id): ?>
                                                <span class="badge bg-success ms-2">Active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <form method="POST" action="<?php echo e(route('subjects.curriculums.set-active', $curriculum->id)); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm <?php echo e((int)($activeCurriculumId ?? 0) === (int)$curriculum->id ? 'btn-success' : 'btn-outline-success'); ?>">
                                                            <i class="fas fa-check-circle"></i> Set Active
                                                        </button>
                                                    </form>
                                                    <button class="btn btn-sm btn-warning curriculum-edit-btn" type="button" data-bs-toggle="modal" data-bs-target="#editCurriculumModal" data-update-url="<?php echo e(route('subjects.curriculums.update', ['id' => $curriculum->id], false)); ?>" data-curriculum-id="<?php echo e($curriculum->id); ?>" data-area-code="<?php echo e($curriculum->area_code); ?>" data-college-id="<?php echo e($curriculum->college_id); ?>" data-course-id="<?php echo e($curriculum->course_id); ?>" data-curriculum-code="<?php echo e($curriculum->code); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <form method="POST" action="<?php echo e(route('subjects.curriculums.destroy', $curriculum->id)); ?>" onsubmit="return confirm('Delete this curriculum?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                No curriculum records found.
                                                <a href="#createCurriculumPanel" data-bs-toggle="collapse" class="ms-1">Create one now</a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade <?php echo e($activeTab === 'subjects' ? 'show active' : ''); ?>" id="subjects">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2 align-items-center">
                        <a href="<?php echo e(route('subjects.create', ['tab' => 'subjects', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>" id="addSubjectBtn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Subject
                        </a>
                        <button type="button" id="importSubjectBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importSubjectModal">
                            <i class="fas fa-file-import"></i> Import Subject
                        </button>
                    </div>
                    <form method="GET" action="<?php echo e(route('subjects.index')); ?>" class="input-group w-50">
                        <input type="hidden" name="tab" value="subjects">
                        <input type="hidden" name="area_code" value="<?php echo e($selectedArea); ?>">
                        <input type="hidden" name="college_id" value="<?php echo e($selectedCollege); ?>">
                        <input id="subjectSearch" type="text" name="subject_search" class="form-control" placeholder="Search all subjects by name or code..." value="<?php echo e($searchTerm); ?>">
                        <button id="subjectSearchBtn" type="button" class="btn btn-outline-secondary"><i class="fas fa-search"></i> Search</button>
                        <?php if($searchTerm): ?>
                        <a href="<?php echo e(route('subjects.index', ['tab' => 'subjects', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>" class="btn btn-outline-danger" title="Clear search"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </form>
                </div>
                <?php if($searchTerm): ?>
                <div class="alert alert-info py-2 mb-2">
                    <i class="fas fa-search"></i> Showing all subjects matching <strong>"<?php echo e($searchTerm); ?>"</strong> across all areas and colleges.
                    <a href="<?php echo e(route('subjects.index', ['tab' => 'subjects', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>" class="ms-2 alert-link">Clear search</a>
                </div>
                <?php endif; ?>

                <?php if($subjects->count() > 0): ?>
                <form method="POST" action="<?php echo e(route('subjects.destroyMany')); ?>" id="bulkDeleteSubjectsForm" class="mb-2">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <input type="hidden" name="tab" value="subjects">
                    <button type="submit" class="btn btn-danger btn-sm" id="bulkDeleteSubjectsBtn" disabled>
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                </form>
                <div class="table-responsive subjects-sticky-wrap">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 44px;" class="text-center">
                                    <input type="checkbox" id="subjectsSelectAll" title="Select all">
                                </th>
                                <th>Program</th>
                                <th>Curriculum</th>
                                <th>Year</th>
                                <th>Term</th>
                                <th>Subject ID</th>
                                <th>Code</th>
                                <th>Subject Name</th>
                                <th style="width:160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="subject-select-item" name="subject_ids[]" value="<?php echo e($subject->id); ?>" form="bulkDeleteSubjectsForm">
                                </td>
                                <td><?php echo e($subject->firstCurriculumSubject?->curriculum?->course?->code ?? $subject->curricula->first()?->course?->code ?? '-'); ?></td>
                                <td><?php echo e($subject->firstCurriculumSubject?->curriculum?->code ?? $subject->curricula->first()?->code ?? '-'); ?></td>
                                <td><?php echo e($subject->subject_year ?: '-'); ?></td>
                                <td><?php echo e($subject->subject_term ?: '-'); ?></td>
                                <td><span class="badge bg-primary text-white"><?php echo e($subject->id); ?></span></td>
                                <td><code><?php echo e($subject->code); ?></code></td>
                                <td><strong><?php echo e($subject->name); ?></strong></td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="<?php echo e(route('subjects.edit', $subject->id)); ?>?tab=subjects&area_code=<?php echo e($selectedArea); ?>&college_id=<?php echo e($selectedCollege); ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('subjects.destroy', $subject->id)); ?>?tab=subjects" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this subject?')">
                                                <i class="fas fa-trash"></i>
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
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    No subjects found.
                    <?php if($selectedArea && $selectedCollege): ?>
                    <a href="<?php echo e(route('subjects.create', ['tab' => 'subjects', 'area_code' => $selectedArea, 'college_id' => $selectedCollege])); ?>">Create one now</a>
                    <?php else: ?>
                    <span class="text-muted">Select area and college first to create one.</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

</div>

<div class="modal fade" id="importSubjectModal" tabindex="-1" aria-labelledby="importSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('subjects.import')); ?>" enctype="multipart/form-data" id="importSubjectForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="tab" value="subjects">
                <div class="modal-header">
                    <h5 class="modal-title" id="importSubjectModalLabel"><i class="fas fa-file-import"></i> Import Subjects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">Upload an Excel or CSV file. These headers are supported:</p>
                    <code>Course Code, Descriptive Title</code>
                    <div class="small text-muted mt-1">Also supported: <code>code, name, subject_year, subject_term, program_code, curriculum_code, subject_units</code></div>

                    <div class="row g-2 mt-3">
                        <div class="col-md-6">
                            <label for="import_area_code" class="form-label">Area</label>
                            <select id="import_area_code" name="area_code" class="form-select" required>
                                <option value="">Select Area</option>
                                <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($code); ?>" <?php echo e((string) $importDefaultAreaCode === (string) $code ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="import_college_id" class="form-label">College</label>
                            <select id="import_college_id" name="college_id" class="form-select" required>
                                <option value="">Select College</option>
                                <?php $__currentLoopData = $allColleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $college): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($college->id); ?>" data-area-code="<?php echo e($college->area_code); ?>" <?php echo e((string) $importDefaultCollegeId === (string) $college->id ? 'selected' : ''); ?>><?php echo e($college->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label for="import_program_id" class="form-label">Default Program (optional)</label>
                            <select id="import_program_id" name="import_program_id" class="form-select">
                                <option value="">Select Program</option>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($program->id); ?>" <?php echo e((int)($importDefaultProgramId ?? 0) === (int)$program->id ? 'selected' : ''); ?>><?php echo e($program->course_program ?: $program->code); ?> - <?php echo e($program->name ?: 'N/A'); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="import_curriculum_id" class="form-label">Default Curriculum (optional)</label>
                            <select id="import_curriculum_id" name="import_curriculum_id" class="form-select">
                                <option value="">Select Curriculum</option>
                                <?php $__currentLoopData = $curriculums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curriculum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($curriculum->id); ?>" data-course-id="<?php echo e($curriculum->course_id); ?>" <?php echo e((int)($importDefaultCurriculumId ?? 0) === (int)$curriculum->id ? 'selected' : ''); ?>><?php echo e($curriculum->code); ?> - <?php echo e($curriculum->desc ?: 'N/A'); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label for="import_subject_year" class="form-label">Default Year Level (optional)</label>
                            <select id="import_subject_year" name="import_subject_year" class="form-select">
                                <option value="">None</option>
                                <option value="First">First</option>
                                <option value="Second">Second</option>
                                <option value="Third">Third</option>
                                <option value="Fourth">Fourth</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="import_subject_term" class="form-label">Default Semester / Term (optional)</label>
                            <select id="import_subject_term" name="import_subject_term" class="form-select">
                                <option value="">None</option>
                                <option value="First">First</option>
                                <option value="Second">Second</option>
                                <option value="Third">Third</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="import_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                        <input type="file" id="import_file" name="import_file" class="form-control" accept=".xlsx,.xls,.csv,text/csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCurriculumModal" tabindex="-1" aria-labelledby="editCurriculumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="" id="editCurriculumForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editCurriculumModalLabel">
                        <i class="fas fa-edit"></i> Edit Curriculum
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_curriculum_area" class="form-label">Area</label>
                            <select id="edit_curriculum_area" name="area_code" class="form-select">
                                <option value="">Select Area</option>
                                <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($code); ?>"><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_curriculum_college" class="form-label">College</label>
                            <select id="edit_curriculum_college" name="college_id" class="form-select">
                                <option value="">Select College</option>
                                <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_curriculum_program" class="form-label">Program</label>
                            <select id="edit_curriculum_program" name="course_id" class="form-select" required>
                                <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optProgram): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($optProgram->id); ?>"><?php echo e($optProgram->course_program ?: $optProgram->code); ?> - <?php echo e($optProgram->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_curriculum_code" class="form-label">Curriculum Code</label>
                            <input type="text" id="edit_curriculum_code" name="code" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="" id="editProgramForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="editProgramModalLabel">
                        <i class="fas fa-edit"></i> Edit Program
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_program_area" class="form-label">Area</label>
                            <select id="edit_program_area" name="area_code" class="form-select" required>
                                <option value="">Select Area</option>
                                <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($code); ?>"><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_program_college" class="form-label">College</label>
                            <select id="edit_program_college" name="college_id" class="form-select" required>
                                <option value="">Select College</option>
                                <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_program_code" class="form-label">Program Code</label>
                            <input type="text" id="edit_program_code" name="course_program" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label for="edit_program_name" class="form-label">Program Name</label>
                            <input type="text" id="edit_program_name" name="name" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSearchInput = document.getElementById('subjectSearch');
        const subjectSearchBtn = document.getElementById('subjectSearchBtn');
        const activeTabInput = document.getElementById('activeTabInput');
        const academicFilterForm = document.getElementById('academicFilterForm');
        const addSubjectBtn = document.getElementById('addSubjectBtn');
        const importSubjectBtn = document.getElementById('importSubjectBtn');
        const importAreaSelect = document.getElementById('import_area_code');
        const importCollegeSelect = document.getElementById('import_college_id');
        const importSubjectForm = document.getElementById('importSubjectForm');
        const importProgramSelect = document.getElementById('import_program_id');
        const importCurriculumSelect = document.getElementById('import_curriculum_id');
        const bulkDeleteSubjectsForm = document.getElementById('bulkDeleteSubjectsForm');
        const bulkDeleteSubjectsBtn = document.getElementById('bulkDeleteSubjectsBtn');
        const subjectsSelectAll = document.getElementById('subjectsSelectAll');
        const subjectSelectItems = Array.from(document.querySelectorAll('.subject-select-item'));
        const areaSelect = document.getElementById('area_code');
        const collegeSelect = document.getElementById('college_id');

        const importCurriculumOptions = importCurriculumSelect ? Array.from(importCurriculumSelect.options).map(function(option) {
            return {
                value: option.value
                , text: option.textContent
                , courseId: option.dataset.courseId || ''
            };
        }) : [];

        function syncImportCurriculumOptions() {
            if (!importProgramSelect || !importCurriculumSelect) {
                return;
            }

            const selectedProgramId = importProgramSelect.value || '';
            const previousCurriculum = importCurriculumSelect.value;

            importCurriculumSelect.innerHTML = '';

            importCurriculumOptions.forEach(function(entry) {
                if (entry.value === '') {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = entry.text;
                    importCurriculumSelect.appendChild(option);
                    return;
                }

                if (!selectedProgramId || entry.courseId === selectedProgramId) {
                    const option = document.createElement('option');
                    option.value = entry.value;
                    option.textContent = entry.text;
                    option.dataset.courseId = entry.courseId;
                    importCurriculumSelect.appendChild(option);
                }
            });

            if (previousCurriculum) {
                const stillExists = Array.from(importCurriculumSelect.options).some(function(option) {
                    return option.value === previousCurriculum;
                });

                if (stillExists) {
                    importCurriculumSelect.value = previousCurriculum;
                }
            }
        }

        function syncImportCollegeOptions() {
            if (!importAreaSelect || !importCollegeSelect) {
                return;
            }

            const selectedArea = importAreaSelect.value || '';

            Array.from(importCollegeSelect.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const collegeArea = option.dataset.areaCode || '';
                option.hidden = selectedArea !== '' && collegeArea !== selectedArea;
            });

            const selectedOption = importCollegeSelect.options[importCollegeSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.areaCode && selectedOption.dataset.areaCode !== selectedArea) {
                importCollegeSelect.value = '';
            }
        }

        function filterRows(containerSelector, inputValue) {
            const filter = inputValue.trim().toLowerCase();
            document.querySelectorAll(`${containerSelector} table tbody tr`).forEach(function(row) {
                const rowText = Array.from(row.querySelectorAll('td')).map(function(td) {
                    return td.textContent.trim().toLowerCase();
                }).join(' ');
                const matches = filter === '' || rowText.indexOf(filter) !== -1;
                row.style.display = matches ? '' : 'none';
            });
        }

        function debounce(fn, delay) {
            let timer;
            return function() {
                const args = arguments;
                clearTimeout(timer);
                timer = setTimeout(function() {
                    fn.apply(null, args);
                }, delay);
            };
        }

        function getCurrentTabState() {
            const activeButton = document.querySelector('#subjectSectionTabs .nav-link.active');
            if (activeButton) {
                return activeButton.getAttribute('data-bs-target').replace('#', '');
            }

            const activePane = document.querySelector('.tab-content .tab-pane.show.active');
            if (activePane) {
                return activePane.id;
            }

            return '<?php echo e($activeTab); ?>';
        }

        function setTabState(tabName) {
            if (activeTabInput) {
                activeTabInput.value = tabName;
            }

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);
            url.hash = tabName;
            window.history.replaceState({}, '', url.toString());
        }

        window.submitAcademicFilter = function(form) {
            const tabName = getCurrentTabState();
            setTabState(tabName);
            form.submit();
        };

        if (academicFilterForm) {
            academicFilterForm.addEventListener('submit', function() {
                setTabState(getCurrentTabState());
            });
        }

        if (addSubjectBtn) {
            addSubjectBtn.addEventListener('click', function(event) {
                const selectedAreaValue = areaSelect ? areaSelect.value : '';
                const selectedCollegeValue = collegeSelect ? collegeSelect.value : '';

                if (!selectedAreaValue || !selectedCollegeValue) {
                    event.preventDefault();
                    alert('Please select Area and College first before creating a new subject.');
                    return;
                }

                const createUrl = new URL(addSubjectBtn.href, window.location.origin);
                createUrl.searchParams.set('tab', 'subjects');
                createUrl.searchParams.set('area_code', selectedAreaValue);
                createUrl.searchParams.set('college_id', selectedCollegeValue);
                addSubjectBtn.href = createUrl.toString();
            });
        }

        if (importSubjectBtn) {
            importSubjectBtn.addEventListener('click', function(event) {
                const selectedAreaValue = importAreaSelect ? importAreaSelect.value : '';
                const selectedCollegeValue = importCollegeSelect ? importCollegeSelect.value : '';

                if (!selectedAreaValue || !selectedCollegeValue) {
                    event.preventDefault();
                    alert('Please set Area and College in the import form before importing subjects.');
                }
            });
        }

        if (importSubjectForm) {
            importSubjectForm.addEventListener('submit', function(event) {
                const selectedAreaValue = importAreaSelect ? importAreaSelect.value : '';
                const selectedCollegeValue = importCollegeSelect ? importCollegeSelect.value : '';

                if (!selectedAreaValue || !selectedCollegeValue) {
                    event.preventDefault();
                    alert('Please set Area and College in the import form before importing subjects.');
                }
            });
        }

        if (importAreaSelect) {
            importAreaSelect.addEventListener('change', syncImportCollegeOptions);
            syncImportCollegeOptions();
        }

        if (importProgramSelect) {
            importProgramSelect.addEventListener('change', syncImportCurriculumOptions);
            syncImportCurriculumOptions();
        }

        function updateBulkDeleteState() {
            if (!bulkDeleteSubjectsBtn) {
                return;
            }

            const selectedCount = subjectSelectItems.filter(function(item) {
                return item.checked;
            }).length;

            bulkDeleteSubjectsBtn.disabled = selectedCount === 0;
        }

        if (subjectsSelectAll) {
            subjectsSelectAll.addEventListener('change', function() {
                subjectSelectItems.forEach(function(item) {
                    item.checked = subjectsSelectAll.checked;
                });
                updateBulkDeleteState();
            });
        }

        subjectSelectItems.forEach(function(item) {
            item.addEventListener('change', function() {
                if (subjectsSelectAll) {
                    subjectsSelectAll.checked = subjectSelectItems.length > 0 && subjectSelectItems.every(function(opt) {
                        return opt.checked;
                    });
                }
                updateBulkDeleteState();
            });
        });

        if (bulkDeleteSubjectsForm) {
            bulkDeleteSubjectsForm.addEventListener('submit', function(event) {
                const selectedCount = subjectSelectItems.filter(function(item) {
                    return item.checked;
                }).length;

                if (selectedCount === 0) {
                    event.preventDefault();
                    alert('Please select at least one subject to delete.');
                    return;
                }

                if (!confirm('Delete ' + selectedCount + ' selected subject(s)?')) {
                    event.preventDefault();
                }
            });
        }

        updateBulkDeleteState();

        const editCurriculumForm = document.getElementById('editCurriculumForm');
        const editCurriculumModal = document.getElementById('editCurriculumModal');
        const editArea = document.getElementById('edit_curriculum_area');
        const editCollege = document.getElementById('edit_curriculum_college');
        const editProgram = document.getElementById('edit_curriculum_program');
        const editCode = document.getElementById('edit_curriculum_code');
        const editProgramForm = document.getElementById('editProgramForm');
        const editProgramModal = document.getElementById('editProgramModal');
        const editProgramArea = document.getElementById('edit_program_area');
        const editProgramCollege = document.getElementById('edit_program_college');
        const editProgramCode = document.getElementById('edit_program_code');
        const editProgramName = document.getElementById('edit_program_name');

        if (editCurriculumModal) {
            editCurriculumModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                if (!trigger || !editCurriculumForm) {
                    return;
                }

                const updateUrl = trigger.getAttribute('data-update-url') || '';
                const curriculumId = trigger.getAttribute('data-curriculum-id') || '';
                editCurriculumForm.action = updateUrl || (curriculumId ? `/subjects/curriculums/${curriculumId}` : '');

                if (editArea) {
                    editArea.value = trigger.getAttribute('data-area-code') || '';
                }
                if (editCollege) {
                    editCollege.value = trigger.getAttribute('data-college-id') || '';
                }
                if (editProgram) {
                    editProgram.value = trigger.getAttribute('data-course-id') || '';
                }
                if (editCode) {
                    editCode.value = trigger.getAttribute('data-curriculum-code') || '';
                }
            });
        }

        if (editCurriculumForm) {
            editCurriculumForm.addEventListener('submit', function(event) {
                if (!editCurriculumForm.action) {
                    event.preventDefault();
                    alert('Unable to submit edit form. Please close and reopen the edit popup.');
                }
            });
        }

        if (editProgramModal) {
            editProgramModal.addEventListener('show.bs.modal', function(event) {
                const trigger = event.relatedTarget;
                if (!trigger || !editProgramForm) {
                    return;
                }

                const updateUrl = trigger.getAttribute('data-update-url') || '';
                const programId = trigger.getAttribute('data-program-id') || '';
                editProgramForm.action = updateUrl || (programId ? `/subjects/programs/${programId}` : '');

                if (editProgramArea) {
                    editProgramArea.value = trigger.getAttribute('data-program-area-code') || '';
                }
                if (editProgramCollege) {
                    editProgramCollege.value = trigger.getAttribute('data-program-college-id') || '';
                }
                if (editProgramCode) {
                    editProgramCode.value = trigger.getAttribute('data-program-code') || '';
                }
                if (editProgramName) {
                    editProgramName.value = trigger.getAttribute('data-program-name') || '';
                }
            });
        }

        if (editProgramForm) {
            editProgramForm.addEventListener('submit', function(event) {
                if (!editProgramForm.action) {
                    event.preventDefault();
                    alert('Unable to submit edit form. Please close and reopen the edit popup.');
                }
            });
        }

        if (subjectSearchBtn && subjectSearchInput) {
            const subjectDebounced = debounce(function() {
                filterRows('#subjects', subjectSearchInput.value);
            }, 150);

            subjectSearchBtn.addEventListener('click', function() {
                filterRows('#subjects', subjectSearchInput.value);
            });

            subjectSearchInput.addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    filterRows('#subjects', subjectSearchInput.value);
                } else {
                    subjectDebounced();
                }
            });

            if (subjectSearchInput.value && getCurrentTabState() === 'subjects') {
                filterRows('#subjects', subjectSearchInput.value);
            }
        }

        const allowedTabs = ['curriculum', 'subjects'];
        const hash = window.location.hash.replace('#', '');
        const queryTab = new URLSearchParams(window.location.search).get('tab');
        const initialTab = allowedTabs.includes(queryTab) ?
            queryTab :
            (allowedTabs.includes(hash) ? hash : getCurrentTabState());

        const initialTabTrigger = document.getElementById(initialTab + '-tab');
        if (initialTabTrigger && window.bootstrap && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(initialTabTrigger).show();
            setTabState(initialTab);
        } else {
            setTabState(getCurrentTabState());
        }

        document.querySelectorAll('#subjectSectionTabs button[data-bs-toggle="tab"]').forEach(function(tabButton) {
            tabButton.addEventListener('shown.bs.tab', function(event) {
                const target = event.target.getAttribute('data-bs-target') || '';
                const tabName = target.replace('#', '');
                const currentQueryTab = new URLSearchParams(window.location.search).get('tab');
                if (currentQueryTab === tabName) {
                    setTabState(tabName);
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set('tab', tabName);
                url.hash = tabName;
                window.location.assign(url.toString());
            });
        });
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes/resources/views/subjects/index.blade.php ENDPATH**/ ?>