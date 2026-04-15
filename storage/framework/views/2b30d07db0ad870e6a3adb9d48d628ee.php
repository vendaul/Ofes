<?php $__env->startSection('content'); ?>
<div class="page-header">
    <h1><i class="fas fa-calendar-alt"></i> My Class Schedules</h1>
    <p>View your class schedules filtered by term and academic year.</p>
</div>

<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-filter"></i> Filter Schedules
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('student.schedules')); ?>" class="row g-3">
            <div class="col-md-8">
                <label for="filter" class="form-label">Term and Academic Year</label>
                <select name="filter" id="filter" class="form-select">
                    <option value="">All Terms and Years</option>
                    <?php $__currentLoopData = $scheduleFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($filter); ?>" <?php echo e($selectedFilter == $filter ? 'selected' : ''); ?>><?php echo e($filter); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> Class Schedules
    </div>
    <div class="card-body">
        <?php if($schedules->isEmpty()): ?>
        <div class="alert alert-info">
            No schedules found for the selected filters.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Term</th>
                        <th>AY</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($schedule->subject ? $schedule->subject->name : 'N/A'); ?></td>
                        <td><?php echo e($schedule->section ? $schedule->section->name : 'N/A'); ?></td>
                        <td><?php echo e($schedule->term ?? 'N/A'); ?></td>
                        <td><?php echo e($schedule->ay ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/students/schedules.blade.php ENDPATH**/ ?>