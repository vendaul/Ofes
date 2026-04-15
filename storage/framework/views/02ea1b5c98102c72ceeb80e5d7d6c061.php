<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-user"></i> Student Details</h1>
    <p>View student information and details</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-id-card"></i> Student Information
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>#:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-primary text-white"><?php echo e($student->id); ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Student ID:</strong>
                    </div>
                    <div class="col-sm-9">
                        <code><?php echo e($student->sid); ?></code>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Full Name:</strong>
                    </div>
                    <div class="col-sm-9">
                        <strong><?php echo e($student->fname); ?> <?php echo e($student->lname); ?></strong>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Section:</strong>
                    </div>
                    <div class="col-sm-9">
                        <?php
                            // Get section from student's class schedule enrollments
                            $section = null;
                            if ($student->classScheduleEnrollments->isNotEmpty()) {
                                $schedule = $student->classScheduleEnrollments->first()->classSchedule;
                                if ($schedule && $schedule->section) {
                                    $section = $schedule->section;
                                }
                            }
                        ?>
                        <?php if($section): ?>
                        <span class="badge bg-success"><?php echo e($section->name); ?></span>
                        <?php elseif($student->course_code && $student->year_level): ?>
                        <span class="badge bg-success"><?php echo e($student->course_code); ?> - Year <?php echo e($student->year_level); ?></span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Not Assigned</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-sm-9">
                        <?php echo e($student->user ? $student->user->email : 'N/A'); ?>

                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Account Status:</strong>
                    </div>
                    <div class="col-sm-9">
                        <?php if($student->user): ?>
                        <span class="badge bg-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-warning">No Account</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="<?php echo e(route('students.edit', ['student' => $student->id, 'section_id' => request('section_id'), 'tab' => request('tab', 'students')])); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Student
                    </a>
                    <?php if(request()->filled('section_id')): ?>
                    <a href="<?php echo e(route('sections.students', ['id' => request('section_id')])); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <?php else: ?>
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo e(route('students.create', request()->filled('section_id') ? ['section_id' => request('section_id')] : [])); ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </a>
                    <a href="#" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> View Evaluations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/students/show.blade.php ENDPATH**/ ?>