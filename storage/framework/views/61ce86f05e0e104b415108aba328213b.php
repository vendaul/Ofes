<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-tasks"></i> My Workload</h1>
    <p>View your teaching assignments and evaluation completion status</p>
</div>

<!-- Filter Form - Always visible -->
<div class="card mb-4">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-filter"></i> Filter Assignments
    </div>
    <div class="card-body">
        <?php if(isset($availableFilters) && $availableFilters->count() > 0): ?>
        <form method="GET" action="<?php echo e(route('instructor.workload')); ?>" class="row g-3">
            <div class="col-md-8">
                <label for="filter" class="form-label">Term and Academic Year</label>
                <select name="filter" id="filter" class="form-select">
                    <option value="">All Terms and Academic Years</option>
                    <?php $__currentLoopData = $availableFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($filter); ?>" <?php echo e((isset($selectedFilter) && $selectedFilter == $filter) ? 'selected' : ''); ?>>
                        <?php echo e($filter); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="<?php echo e(route('instructor.workload')); ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No assignment term/year combinations available for filtering yet.
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($schedules->count() > 0): ?>
<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> My Teaching Assignments
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-code"></i> Course Code</th>
                    <th><i class="fas fa-book"></i> Course Name</th>
                    <th><i class="fas fa-layer-group"></i> Section</th>
                    <th><i class="fas fa-calendar"></i> Academic Year</th>
                    <th><i class="fas fa-calendar-alt"></i> Term</th>
                    <th><i class="fas fa-star"></i> Eval Avg (0-100)</th>
                    <th><i class="fas fa-link"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <span class="badge bg-primary text-white"><?php echo e($schedule->subject ? $schedule->subject->code : 'N/A'); ?></span>
                    </td>
                    <td>
                        <strong><?php echo e($schedule->subject ? $schedule->subject->name : 'Unknown Subject'); ?></strong>
                    </td>
                    <td>
                        <span class="badge bg-success"><?php echo e($schedule->section ? $schedule->section->name : 'Unknown Section'); ?></span>
                    </td>
                    <td>
                        <span class="badge bg-primary"><?php echo e($schedule->ay); ?></span>
                    </td>
                    <td>
                        <span class="badge bg-warning text-dark"><?php echo e($schedule->term); ?></span>
                    </td>
                    <td>
                        <?php
                        $eval = $schedule->evaluation_result;
                        $avg = $eval ? number_format(min(max($eval->overall_average * 20, 0), 100), 2) : 'N/A';
                        ?>
                        <span class="badge bg-info text-white"><?php echo e($avg); ?></span>
                    </td>
                    <td>
                        <?php if($schedule->evaluation_result): ?>
                        <a href="<?php echo e(route('instructor.results', $schedule->id)); ?>" class="btn btn-sm btn-success">View Results</a>
                        <?php else: ?>
                        <span class="text-muted">No result yet</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if(!isset($schedules) || $schedules->count() === 0): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> You have no teaching assignments or class schedules at this time.
</div>
<?php endif; ?>

<div class="row mt-4">
    <div class="col-md-12">
        <a href="<?php echo e(route('instructor.dashboard')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.faculty', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/instructor/workload.blade.php ENDPATH**/ ?>