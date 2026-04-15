<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-users"></i> Students in <?php echo e($classSchedule->subject?->name ?? 'Unknown Subject'); ?></h1>
    <p>Class: <?php echo e($classSchedule->section?->name ?? 'Unknown Section'); ?> | <?php echo e($classSchedule->ay); ?> <?php echo e($classSchedule->term); ?></p>
</div>

<div class="mb-4">
    <a href="<?php echo e(route('class-schedule-students.create', $classSchedule->id)); ?>" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add Student
    </a>
    <a href="javascript:history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if($students->count() > 0): ?>
<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> Enrolled Students (<?php echo e($students->count()); ?>)
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-id-card"></i> Student Number</th>
                    <th><i class="fas fa-user"></i> Name</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-layer-group"></i> Status</th>
                    <th style="width: 160px;"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <span class="badge bg-primary"><?php echo e($cs->student?->sid ?? 'Unknown'); ?></span>
                    </td>
                    <td>
                        <?php echo e($cs->student?->fname ?? ''); ?> <?php echo e($cs->student?->lname ?? ''); ?>

                    </td>
                    <td>
                        <?php echo e($cs->student?->email ?? 'No email'); ?>

                    </td>
                    <td>
                        <?php
                        $studentType = strtolower((string) ($cs->student?->student_type ?? ''));
                        ?>
                        <?php if($studentType === 'regular'): ?>
                        <span class="badge bg-info">Regular</span>
                        <?php elseif($studentType === 'irregular'): ?>
                        <span class="badge bg-warning text-dark">Irregular</span>
                        <?php else: ?>
                        <span class="badge bg-secondary"><?php echo e($cs->student?->student_type ?: 'N/A'); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo e(route('class-schedule-students.edit', [$classSchedule->id, $cs->id])); ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="<?php echo e(route('class-schedule-students.destroy', [$classSchedule->id, $cs->id])); ?>" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No students enrolled in this class schedule yet.
    <a href="<?php echo e(route('class-schedule-students.create', $classSchedule->id)); ?>">Add the first student</a>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/class_schedules/students/index.blade.php ENDPATH**/ ?>