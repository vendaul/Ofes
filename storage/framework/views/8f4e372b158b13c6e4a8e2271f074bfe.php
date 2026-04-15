<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="fas fa-map-marked-alt"></i> Areas & Colleges</h1>
    <p>Manage campus areas and their colleges/departments.</p>
</div>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">

    
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center text-white bg-primary">
                <span><i class="fas fa-map-pin me-1"></i> Areas</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-0" id="area-search" placeholder="Search areas...">
                    </div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAreaModal">
                        <i class="fas fa-plus"></i> Add Area
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="areas-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr data-search-row>
                                <td><span class="badge bg-secondary"><?php echo e($area->area_code); ?></span></td>
                                <td><?php echo e($area->area_name); ?></td>
                                <td class="text-muted small"><?php echo e($area->area_address ?? '—'); ?></td>
                                <td class="text-center">
                                    <div class="action-buttons justify-content-center">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editAreaModal<?php echo e($area->id); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo e(route('areas.destroy', $area)); ?>" method="POST" onsubmit="return confirm('Delete this area?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            
                            <div class="modal fade" id="editAreaModal<?php echo e($area->id); ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="<?php echo e(route('areas.update', $area)); ?>" method="POST" class="modal-content">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Area</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Area Code</label>
                                                <input type="text" name="area_code" class="form-control" value="<?php echo e($area->area_code); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Area Name</label>
                                                <input type="text" name="area_name" class="form-control" value="<?php echo e($area->area_name); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" name="area_address" class="form-control" value="<?php echo e($area->area_address); ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No areas found.</td>
                            </tr>
                            <?php endif; ?>
                            <tr id="areas-empty-search" style="display: none;">
                                <td colspan="4" class="text-center text-muted py-3">No matching areas found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center text-white bg-primary">
                <span><i class="fas fa-university me-1"></i> Colleges / Departments</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="max-width: 240px;">
                        <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-0" id="college-search" placeholder="Search colleges...">
                    </div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCollegeModal">
                        <i class="fas fa-plus"></i> Add College
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="colleges-table">
                        <thead>
                            <tr>
                                <th>Area</th>
                                <th>Name</th>
                                <th>Prefix</th>
                                <th>Head Officer</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $colleges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $college): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php ($isActiveCollege = (string) ($selectedCollege ?? '') === (string) $college->id); ?>
                            <tr data-search-row>
                                <td><span class="badge bg-secondary"><?php echo e($college->area_code); ?></span></td>
                                <td><?php echo e($college->name); ?></td>
                                <td><?php echo e($college->prefix ?? '—'); ?></td>
                                <td class="small text-muted"><?php echo e($college->head_officer ?? '—'); ?></td>
                                <td class="text-center">
                                    <?php if($isActiveCollege): ?>
                                    <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                    <span class="badge bg-light text-dark border">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-buttons justify-content-center">
                                        <form action="<?php echo e(route('colleges.set-active', $college)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button class="btn btn-sm <?php echo e($isActiveCollege ? 'btn-success' : 'btn-outline-success'); ?>" type="submit">
                                                <i class="fas fa-check-circle"></i> <?php echo e($isActiveCollege ? 'Active' : 'Set Active'); ?>

                                            </button>
                                        </form>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCollegeModal<?php echo e($college->id); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo e(route('colleges.destroy', $college)); ?>" method="POST" onsubmit="return confirm('Delete this college?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-sm btn-outline-danger" type="submit">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            
                            <div class="modal fade" id="editCollegeModal<?php echo e($college->id); ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="<?php echo e(route('colleges.update', $college)); ?>" method="POST" class="modal-content">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit College</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Area</label>
                                                <select name="area_code" class="form-select" required>
                                                    <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($a->area_code); ?>" <?php echo e($college->area_code === $a->area_code ? 'selected' : ''); ?>>
                                                        <?php echo e($a->area_code); ?> — <?php echo e($a->area_name); ?>

                                                    </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">College / Department Name</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo e($college->name); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Prefix</label>
                                                <input type="text" name="prefix" class="form-control" maxlength="5" value="<?php echo e($college->prefix); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Head Officer</label>
                                                <input type="text" name="head_officer" class="form-control" value="<?php echo e($college->head_officer); ?>">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No colleges found.</td>
                            </tr>
                            <?php endif; ?>
                            <tr id="colleges-empty-search" style="display: none;">
                                <td colspan="6" class="text-center text-muted py-3">No matching colleges found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addAreaModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo e(route('areas.store')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-map-pin me-1"></i> Add Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Area Code</label>
                    <input type="text" name="area_code" class="form-control" placeholder="e.g. IFSU 101" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Area Name</label>
                    <input type="text" name="area_name" class="form-control" placeholder="e.g. Main" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="area_address" class="form-control" placeholder="e.g. Lamut, Ifugao">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Area</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="addCollegeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo e(route('colleges.store')); ?>" method="POST" class="modal-content">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-university me-1"></i> Add College / Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Area</label>
                    <select name="area_code" class="form-select" required>
                        <option value="">— Select Area —</option>
                        <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a->area_code); ?>"><?php echo e($a->area_code); ?> — <?php echo e($a->area_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">College / Department Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Prefix</label>
                    <input type="text" name="prefix" class="form-control" maxlength="5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Head Officer</label>
                    <input type="text" name="head_officer" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Add College</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function attachTableSearch(inputId, tableId, emptyRowId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            const emptyRow = document.getElementById(emptyRowId);

            if (!input || !table || !emptyRow) {
                return;
            }

            const rows = Array.from(table.querySelectorAll('tbody tr[data-search-row]'));

            input.addEventListener('input', function() {
                const term = input.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(function(row) {
                    const matches = row.innerText.toLowerCase().includes(term);
                    row.style.display = matches ? '' : 'none';
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            });
        }

        attachTableSearch('area-search', 'areas-table', 'areas-empty-search');
        attachTableSearch('college-search', 'colleges-table', 'colleges-empty-search');
    });

</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/admin/area_college.blade.php ENDPATH**/ ?>