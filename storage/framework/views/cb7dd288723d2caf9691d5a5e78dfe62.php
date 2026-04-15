

<?php $__env->startSection('content'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Evaluation Report
                    </h2>
                </div>

                <div class="card-body">
                    <?php if(isset($message)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <?php echo e($message); ?>

                    </div>
                    <?php else: ?>
                    <!-- Report Header -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5 class="text-primary">Course Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Instructor:</strong></td>
                                    <td><?php echo e($assignment->instructor->name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td><?php echo e($assignment->subject->name ?? 'N/A'); ?> (<?php echo e($assignment->subject->code ?? ''); ?>)</td>
                                </tr>
                                <tr>
                                    <td><strong>Section:</strong></td>
                                    <td><?php echo e($assignment->section->name ?? 'N/A'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Semester:</strong></td>
                                    <td><?php echo e($assignment->term ?? 'N/A'); ?> <?php echo e($assignment->ay ?? ''); ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <h5 class="text-success">Summary Statistics</h5>
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <?php $overallAverage100 = number_format(min(max(($overallAverage * 20), 0), 100), 2); ?>
                                    <h3 class="text-primary mb-2"><?php echo e($overallAverage100); ?>/100</h3>
                                    <h5 class="text-<?php echo e($overallRating === 'Excellent' ? 'success' : ($overallRating === 'Good' ? 'info' : ($overallRating === 'Fair' ? 'warning' : 'danger'))); ?>">
                                        <?php echo e($overallRating); ?>

                                    </h5>
                                    <p class="mb-1">Overall Rating</p>
                                    <small class="text-muted"><?php echo e($totalEvaluations); ?> evaluation<?php echo e($totalEvaluations > 1 ? 's' : ''); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category-wise Results -->
                    <h5 class="text-primary mb-3">Detailed Results by Category</h5>

                    <?php $__currentLoopData = $categoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $catAvg100 = number_format(min(max(($data['average'] * 20), 0), 100), 2); ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center text-white">
                            <h6 class="mb-0"><?php echo e($category); ?></h6>
                            <span class="badge bg-primary fs-6">Average: <?php echo e($catAvg100); ?>/100</span>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th class="text-center">Average Rating</th>
                                            <th class="text-center">Response Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $data['questions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $questionText => $questionData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($questionText); ?></td>
                                            <td class="text-center">
                                                <?php $questionAvg100 = number_format(min(max(($questionData['average'] * 20), 0), 100), 2); ?>
                                                <span class="badge bg-<?php echo e($questionData['average'] >= 4 ? 'success' : ($questionData['average'] >= 3 ? 'warning' : 'danger')); ?> fs-6">
                                                    <?php echo e($questionAvg100); ?>/100
                                                </span>
                                            </td>
                                            <td class="text-center"><?php echo e($questionData['response_count'] ?? count($questionData['ratings'] ?? [])); ?></td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <!-- Student Comments Section -->
                    <?php
                    $comments = $evaluations->where('comment', '!=', null)->where('comment', '!=', '');
                    ?>
                    <?php if($comments->count() > 0): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-comments"></i> Student Feedback (<?php echo e($comments->count()); ?>)
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php $__currentLoopData = $comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evaluation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> Anonymous
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> <?php echo e(\Carbon\Carbon::parse($evaluation->date_submitted)->format('M d, Y')); ?>

                                    </small>
                                </div>
                                <p class="mb-0"><?php echo e($evaluation->comment); ?></p>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button onclick="window.print()" class="btn btn-secondary me-2">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <a href="<?php echo e(route('instructor.dashboard')); ?>" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {

        .btn,
        .card-header.bg-primary,
        .card-header.bg-info {
            display: none !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }

        body {
            background: white !important;
        }

        .container {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    }

</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.faculty', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/reports/instructor_result.blade.php ENDPATH**/ ?>