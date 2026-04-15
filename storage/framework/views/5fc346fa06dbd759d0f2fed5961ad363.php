<?php $__env->startSection('content'); ?>

<style>
    .instructors-table-wrap {
        max-height: 70vh;
        overflow: auto;
    }

    .instructors-table-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        background: #f8f9fa;
        box-shadow: inset 0 -1px 0 #dee2e6;
    }

</style>

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-chalkboard-user"></i> Instructors Management</h1>
        <p>Manage all instructors in the system</p>
    </div>
    <form method="GET" class="d-flex gap-2 align-items-end">
        <div class="form-group mb-0">
            <label for="area_code" class="form-label small mb-1">Area</label>
            <select id="area_code" name="area_code" class="form-select" onchange="this.form.submit()">
                <option value="">Select Area</option>
                <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($code); ?>" <?php echo e($code == $selectedArea ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="form-group mb-0">
            <label for="college_id" class="form-label small mb-1">College</label>
            <select id="college_id" name="college_id" class="form-select" onchange="this.form.submit()">
                <option value="">Select College</option>
                <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($id); ?>" <?php echo e($id == $selectedCollege ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="btn btn-outline-primary align-self-end">Apply</button>
    </form>
</div>

<div class="card">
    <div class="card-header text-white bg-primary d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><i class="fas fa-list"></i> Instructors List</span>
        <div class="d-flex flex-wrap gap-2">
            <button id="addInstructorBtn" type="button" class="btn btn-light btn-sm">
                <i class="fas fa-plus"></i> Add New Instructor
            </button>
            <button id="importInstructorWorkloadBtn" type="button" class="btn btn-success btn-sm">
                <i class="fas fa-file-import"></i> Import Instructors + Workload
            </button>
            <a id="assignSubjectBtn" href="<?php echo e(route('assignments.create')); ?>" class="btn btn-outline-light btn-sm">
                <i class="fas fa-tasks"></i> Assign Subject
            </a>
        </div>
    </div>
    <div class="card-body border-bottom">
        <div class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-4">
                <label for="instructorTableSearch" class="form-label mb-1">Search Instructors</label>
                <input type="text" id="instructorTableSearch" class="form-control form-control-sm" placeholder="Type to search employee ID, name, rank, designation, or college">
            </div>
        </div>
    </div>
    <?php if($instructors->count() > 0): ?>
    <div class="table-responsive instructors-table-wrap">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-id-card"></i> Employee ID</th>
                    <th><i class="fas fa-user"></i> Name</th>
                    <th><i class="fas fa-user-tag"></i> Rank</th>
                    <th><i class="fas fa-user-tag"></i> Designation</th>
                    <th><i class="fas fa-user-tag"></i> College</th>
                    <th style="width: 150px;"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <span class="badge bg-primary text-white"><?php echo e($instructor->empid); ?></span>
                    </td>
                    <td>
                        <strong><?php echo e($instructor->fname); ?> <?php echo e($instructor->lname); ?></strong>
                    </td>
                    <td>
                        <span class="badge bg-info text-white"><?php echo e($instructor->academic_rank ?? 'N/A'); ?></span>
                    </td>
                    <td><?php echo e($instructor->position ?? 'N/A'); ?></td>
                    <td><?php echo e($instructor->collegeRelation ? $instructor->collegeRelation->name : 'N/A'); ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="<?php echo e(route('instructors.workload', $instructor->id)); ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?php echo e(route('instructors.edit', $instructor->id)); ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="<?php echo e(route('instructors.destroy', $instructor->id)); ?>" method="POST" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this instructor?')">
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
            <i class="fas fa-info-circle"></i> No instructors found. <a href="<?php echo e(route('instructors.create')); ?>">Create one now</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="importInstructorWorkloadModal" tabindex="-1" aria-labelledby="importInstructorWorkloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('instructors.importWithWorkload')); ?>" enctype="multipart/form-data" id="importInstructorWorkloadForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="importInstructorWorkloadModalLabel">
                        <i class="fas fa-file-import"></i> Import Instructors with Workload
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-light border py-2 mb-3">
                        <div class="small mb-1">Supported headers:</div>
                        <code>Instructor ID, Name, Course, Year &amp; Section, Course Code, COURSE TITLE</code>
                        <div class="small mt-1 text-muted">Optional: <code>Academic Rank</code>, <code>Position</code></div>
                    </div>

                    <input type="hidden" name="area_code" id="import_area_code" value="<?php echo e(old('area_code', $selectedArea ?? '')); ?>">
                    <input type="hidden" name="college_id" id="import_college_id" value="<?php echo e(old('college_id', $selectedCollege ?? '')); ?>">

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Area</label>
                            <input type="text" class="form-control" value="<?php echo e($areaOptions[$selectedArea] ?? ($selectedArea ?? '')); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">College</label>
                            <input type="text" class="form-control" value="<?php echo e($selectedCollegeName ?? ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label for="import_school_year" class="form-label">School Year</label>
                            <input type="text" id="import_school_year" name="school_year" class="form-control" value="<?php echo e(old('school_year', $activePeriod->year ?? '')); ?>" placeholder="e.g. 2025-2026" required>
                        </div>
                        <div class="col-md-6">
                            <label for="import_semester" class="form-label">Semester / Term</label>
                            <input type="text" id="import_semester" name="semester" class="form-control" value="<?php echo e(old('semester', $activePeriod->term ?? '')); ?>" placeholder="e.g. First" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label for="import_file" class="form-label">File (.xlsx, .xls, .csv)</label>
                        <input type="file" id="import_file" name="import_file" class="form-control" accept=".xlsx,.xls,.csv,text/csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var areaSelect = document.getElementById('area_code');
        var collegeSelect = document.getElementById('college_id');
        var importAreaCode = document.getElementById('import_area_code');
        var importCollegeId = document.getElementById('import_college_id');
        var addInstructorBtn = document.getElementById('addInstructorBtn');
        var importInstructorWorkloadBtn = document.getElementById('importInstructorWorkloadBtn');
        var importInstructorWorkloadForm = document.getElementById('importInstructorWorkloadForm');
        var instructorTableSearch = document.getElementById('instructorTableSearch');
        var assignBtn = document.getElementById('assignSubjectBtn');

        function syncImportScopeFields() {
            if (importAreaCode && areaSelect) {
                importAreaCode.value = areaSelect.value || '';
            }
            if (importCollegeId && collegeSelect) {
                importCollegeId.value = collegeSelect.value || '';
            }
        }

        function updateAssignSubjectLink() {
            if (!areaSelect || !collegeSelect || !assignBtn) return;

            var area = areaSelect.value;
            var college = collegeSelect.value;
            var url = new URL(assignBtn.getAttribute('href'), window.location.origin);

            if (area) {
                url.searchParams.set('area_code', area);
            } else {
                url.searchParams.delete('area_code');
            }

            if (college) {
                url.searchParams.set('college_id', college);
            } else {
                url.searchParams.delete('college_id');
            }

            assignBtn.setAttribute('href', url.toString());
        }

        if (addInstructorBtn) {
            addInstructorBtn.addEventListener('click', function() {
                if (!areaSelect || !collegeSelect) return;

                var area = areaSelect.value;
                var college = collegeSelect.value;

                if (!area || !college) {
                    alert('Please set area and college first.');
                    return;
                }

                window.location.href = '<?php echo e(route("instructors.create")); ?>?area_code=' + encodeURIComponent(area) + '&college_id=' + encodeURIComponent(college);
            });
        }

        if (importInstructorWorkloadBtn) {
            importInstructorWorkloadBtn.addEventListener('click', function() {
                if (!areaSelect || !collegeSelect) return;

                var area = areaSelect.value;
                var college = collegeSelect.value;

                if (!area || !college) {
                    alert('Please set area and college first.');
                    return;
                }

                syncImportScopeFields();

                if (window.bootstrap && bootstrap.Modal) {
                    var modalEl = document.getElementById('importInstructorWorkloadModal');
                    if (modalEl) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                }
            });
        }

        if (importInstructorWorkloadForm) {
            importInstructorWorkloadForm.addEventListener('submit', function(event) {
                if (!areaSelect || !collegeSelect) return;

                if (!areaSelect.value || !collegeSelect.value) {
                    event.preventDefault();
                    alert('Please set area and college first.');
                    return;
                }

                syncImportScopeFields();
            });
        }

        if (areaSelect) {
            areaSelect.addEventListener('change', syncImportScopeFields);
            areaSelect.addEventListener('change', updateAssignSubjectLink);
        }

        if (collegeSelect) {
            collegeSelect.addEventListener('change', syncImportScopeFields);
            collegeSelect.addEventListener('change', updateAssignSubjectLink);
        }

        if (instructorTableSearch) {
            instructorTableSearch.addEventListener('input', function() {
                var query = (instructorTableSearch.value || '').toLowerCase().trim();
                var rows = document.querySelectorAll('.instructors-table-wrap tbody tr');

                rows.forEach(function(row) {
                    var text = (row.textContent || '').toLowerCase();
                    row.style.display = !query || text.indexOf(query) !== -1 ? '' : 'none';
                });
            });
        }

        syncImportScopeFields();
        updateAssignSubjectLink();
    });

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/instructors/index.blade.php ENDPATH**/ ?>