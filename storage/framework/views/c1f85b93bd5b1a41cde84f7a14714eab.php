<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-clipboard-list"></i> My Evaluations</h1>
        <p>View and complete pending evaluations for your instructors.</p>
    </div>
    <div class="card border-info" style="min-width:210px;">
        <div class="card-body py-2 px-3">
            <h6 class="card-title mb-1">Active Period</h6>
            <p class="card-text mb-0"><?php echo e($activePeriod ? $activePeriod->year . ' / ' . $activePeriod->term : 'N/A'); ?></p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body">
                <h5 class="card-title">My Sections</h5>
                <p class="card-text mb-0"><?php echo e($courseCode ?? $student->course_code ?? 'N/A'); ?> - <?php echo e($yearLevel ?? $student->year_level ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('student.evaluations')); ?>" class="row g-2">
                    <div class="col-9">
                        <label for="instructor_id" class="form-label">Choose Instructor</label>
                        <select class="form-select" name="instructor_id" onchange="this.form.submit()">
                            <option value="">All Instructors</option>
                            <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($instructor->id); ?>" <?php echo e(isset($selectedInstructorId) && $selectedInstructorId == $instructor->id ? 'selected' : ''); ?>>
                                <?php echo e($instructor->fname); ?> <?php echo e($instructor->lname); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($evaluationStartDate) && !empty($evaluationEndDate)): ?>
<div class="mb-4 alert alert-info">
    <strong>Evaluation window:</strong>
    <?php echo e(\Illuminate\Support\Carbon::parse($evaluationStartDate)->format('Y-m-d')); ?> to <?php echo e(\Illuminate\Support\Carbon::parse($evaluationEndDate)->format('Y-m-d')); ?>.
    <?php if($evaluationFuture): ?>
    <span class="badge bg-warning">Not yet open</span>
    <?php elseif($evaluationOpen): ?>
    <span class="badge bg-success">Open</span>
    <?php elseif($evaluationExpired): ?>
    <span class="badge bg-danger">Closed</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if($assignments->isEmpty()): ?>
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-3"></i>
    <h5>No Instructors Found</h5>
    <p>There are currently no teaching assignments matching your section/course.</p>
</div>
<?php else: ?>
<div class="card mb-4">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-users"></i> My Instructors & Subjects
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Instructor</th>
                        <th>Section</th>
                        <th>Rating (1-100)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $studentRating100 = $assignment->student_evaluation_rating;
                    $rating100 = $studentRating100 !== null ? number_format($studentRating100, 2) : null;
                    ?>
                    <tr>
                        <td><?php echo e($assignment->subject ? $assignment->subject->name : 'Unknown Subject'); ?></td>
                        <td><?php echo e($assignment->instructor ? $assignment->instructor->fname . ' ' . $assignment->instructor->lname : 'N/A'); ?></td>
                        <td><?php echo e($assignment->section ? $assignment->section->name : 'N/A'); ?></td>
                        <td><?php echo e($rating100 ?? 'N/A'); ?></td>
                        <td>
                            <?php if($assignment->evaluated): ?>
                            <span class="badge bg-success">Evaluated</span>
                            <?php else: ?>
                            <span class="badge bg-warning text-dark">Pending Evaluation</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($assignment->evaluated): ?>
                            <button class="btn btn-sm btn-secondary" disabled>Reviewed</button>
                            <?php else: ?>
                            <?php if($evaluationFuture): ?>
                            <button class="btn btn-sm btn-warning" disabled>Not Open</button>
                            <?php elseif($evaluationExpired): ?>
                            <button class="btn btn-sm btn-danger" disabled>Evaluation Closed</button>
                            <?php elseif($evaluationOpen): ?>
                            <a href="<?php echo e(route('evaluate.show', $assignment->id)); ?>" class="btn btn-sm btn-success">Evaluate</a>
                            <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled>Unavailable</button>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/students/evaluations.blade.php ENDPATH**/ ?>