<?php $__env->startSection('content'); ?>

<style>
    .page-header {
        margin-bottom: 25px;
    }

    .page-header h1 {
        font-weight: 600;
    }

    .dashboard-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        font-weight: 500;
    }

    .section-item {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }

    .section-item:last-child {
        border-bottom: none;
    }

    .badge {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 20px;
    }

    .instructor-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #0d6efd;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .assignment-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: 0.2s;
    }

    .assignment-card:hover {
        transform: translateY(-5px);
    }

    .btn-success {
        border-radius: 20px;
        padding: 5px 15px;
    }

</style>

<div class="page-header">
    <h1><i class="fas fa-tachometer-alt"></i> Student Dashboard</h1>
    <p class="text-muted">Welcome to your dashboard. Here you can view your sections, instructors, and pending evaluations.</p>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h5 class="card-title mb-3">📚 My Sections</h5>

                <?php if($sections->isEmpty()): ?>
                <p class="mb-0 text-muted">No sections assigned</p>
                <?php else: ?>
                <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="section-item">
                    <h6 class="mb-2"><?php echo e($section->name); ?></h6>

                    <?php
                    // Get instructors from class schedules instead of teaching assignments
                    $sectionSchedules = $schedules->where('section_id', $section->id);
                    $sectionInstructors = $sectionSchedules->map(fn($s) => optional($s->instructor))
                    ->filter()
                    ->unique('id');
                    ?>

                    <?php if($sectionInstructors->isEmpty()): ?>
                    <small class="text-muted">No instructors assigned</small>
                    <?php else: ?>
                    <div class="small">
                        <?php $__currentLoopData = $sectionInstructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-primary">
                            <?php echo e($instructor->fname); ?> <?php echo e($instructor->lname); ?>

                        </span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php if($instructors->isEmpty()): ?>
<div class="alert alert-info text-center shadow-sm">
    <i class="fas fa-info-circle fa-2x mb-2"></i>
    <h6 class="mb-1">No Instructors Found</h6>
    <small>There are currently no teaching assignments matching your section/course.</small>
</div>
<?php else: ?>
<div class="card dashboard-card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-users"></i> My Instructors
    </div>

    <div class="card-body">
        <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="instructor-item mb-3">
            <div class="avatar">
                <?php echo e(strtoupper(substr($instructor->fname,0,1))); ?>

            </div>
            <div>
                <strong><?php echo e($instructor->fname); ?> <?php echo e($instructor->lname); ?></strong>
                <div class="small text-muted"><?php echo e($instructor->email); ?></div>
                <?php
                $instructorSubjects = $schedules->where('instructor_id', $instructor->id)
                ->map(function($s) {
                return optional($s->subject)->name ?? 'Unknown Subject';
                })
                ->filter()
                ->unique()
                ->values();
                ?>
                <?php if($instructorSubjects->isNotEmpty()): ?>
                <div class="small text-muted mt-1">
                    <strong>Subjects:</strong> <?php echo e($instructorSubjects->join(', ')); ?>

                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/students/dashboard.blade.php ENDPATH**/ ?>