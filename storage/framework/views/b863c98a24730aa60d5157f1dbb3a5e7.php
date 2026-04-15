<?php $__env->startSection('content'); ?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-star-half-alt me-2"></i>
                        Instructor Evaluation
                    </h2>
                    <p class="mb-0 mt-1 opacity-75">Please rate your instructor on the following criteria</p>
                </div>

                <div class="card-body">
                    <form action="<?php echo e(route('evaluate.store')); ?>" method="POST" id="evaluationForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="class_schedule_id" value="<?php echo e($class_schedule_id ?? $assign_id ?? ''); ?>">

                        <?php
                        $facultyName = '____________________________';
                        if (!empty($classSchedule) && !empty($classSchedule->instructor)) {
                        $facultyName = $classSchedule->instructor->fullname
                        ?: trim(($classSchedule->instructor->fname ?? '') . ' ' . ($classSchedule->instructor->lname ?? ''));
                        }

                        $collegeDisplay = !empty($collegeName) ? $collegeName : '____________________________';

                        $courseDisplay = '____________________________';
                        if (!empty($classSchedule) && !empty($classSchedule->subject)) {
                        $code = $classSchedule->subject->code ?? '';
                        $name = $classSchedule->subject->name ?? '';
                        $courseDisplay = trim($code . ($code && $name ? ' - ' : '') . $name);
                        }

                        $programLevel = '____________________________';
                        if (!empty($classSchedule)) {
                        $yearLevel = !empty($classSchedule->year_level) ? 'Year ' . $classSchedule->year_level : null;
                        $sectionName = !empty($classSchedule->section) ? $classSchedule->section->name : null;
                        $programLevel = trim(($yearLevel ?? '') . (($yearLevel && $sectionName) ? ' / ' : '') . ($sectionName ?? ''));
                        if ($programLevel === '') {
                        $programLevel = '____________________________';
                        }
                        }

                        $semesterDisplay = '____________________________';
                        if (!empty($classSchedule) && (!empty($classSchedule->term) || !empty($classSchedule->ay))) {
                        $semesterDisplay = trim(($classSchedule->term ?? '') . ((!empty($classSchedule->term) && !empty($classSchedule->ay)) ? ' / ' : '') . ($classSchedule->ay ?? ''));
                        }
                        ?>

                        <div class="set-info-container mt-4 mb-5">
                            <div class="text-center mb-4">
                                <h4 class="fw-bold mb-1">EVALUATION INSTRUMENT</h4>
                                <h5 class="fw-bold mb-0">STUDENT EVALUATION OF TEACHERS (SET)</h5>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold">A. Faculty Information <small>(to be accomplished by the Designated Office)</small></h6>

                                <table class="table table-borderless w-100 w-md-75 faculty-info-table">
                                    <tr>
                                        <td>Name of Faculty being Evaluated</td>
                                        <td>: <?php echo e($facultyName); ?></td>
                                    </tr>
                                    <tr>
                                        <td>College/Department</td>
                                        <td>: <?php echo e($collegeDisplay); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Course Code/Title</td>
                                        <td>: <?php echo e($courseDisplay); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Program Level</td>
                                        <td>: <?php echo e($programLevel); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Semester or Term/Academic Year</td>
                                        <td>: <?php echo e($semesterDisplay); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold">B. Rating Scale</h6>

                                <table class="table table-bordered text-center scale-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Scale</th>
                                            <th>Qualitative Description</th>
                                            <th>Operational Definition</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>5</td>
                                            <td>Always manifested</td>
                                            <td>Evident in nearly all relevant situations (91-100% of instances).</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Often manifested</td>
                                            <td>Evident most of the time, with occasional lapses (61-90%).</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Sometimes manifested</td>
                                            <td>Evident about half the time (31-60%).</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Seldom manifested</td>
                                            <td>Infrequently demonstrated; rarely evident (11-30%).</td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>Never/Rarely manifested</td>
                                            <td>Almost never evident, only isolated cases (0-10%).</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold">C. Instruction:</h6>
                                <p class="mb-0">
                                    Read the benchmark statements carefully. Please rate the faculty on each of the following
                                    statements using the above-listed rating scale. Encircle your rating.
                                </p>
                            </div>
                        </div>

                        <?php
                        $questionsByCategory = $questions->groupBy('category');
                        ?>

                        <?php $__currentLoopData = $questionsByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryQuestions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="category-section mb-5">
                            <div class="category-header mb-4">
                                <h4 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-list-check me-2"></i>
                                    <?php echo e($category ?: 'General'); ?>

                                </h4>
                            </div>

                            <div class="questions-container">
                                <?php $__currentLoopData = $categoryQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="question-card mb-4 p-3 border rounded">
                                    <div class="question-text mb-3">
                                        <h6 class="fw-bold text-dark mb-3">
                                            <span class="badge bg-secondary me-2"><?php echo e($loop->iteration); ?></span>
                                            <?php echo e($question->question_text); ?>

                                        </h6>
                                    </div>

                                    <div class="rating-section">
                                        <label class="form-label fw-semibold text-muted mb-2">Rate this aspect:</label>
                                        <div class="rating-scale d-flex align-items-center">
                                            <div class="rating-options d-flex gap-2 flex-wrap">
                                                <?php for($i = 1; $i <= 5; $i++): ?> <div class="rating-option">
                                                    <input type="radio" name="ratings[<?php echo e($question->question_id); ?>]" value="<?php echo e($i); ?>" id="rating_<?php echo e($question->question_id); ?>_<?php echo e($i); ?>" class="btn-check" required>
                                                    <label class="btn btn-outline-primary rating-btn" for="rating_<?php echo e($question->question_id); ?>_<?php echo e($i); ?>">
                                                        <?php echo e($i); ?>

                                                    </label>
                                            </div>
                                            <?php endfor; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="additional-comments-section mt-5 pt-4 border-top">
                    <div class="mb-4">
                        <label for="comment" class="form-label fw-semibold">
                            <i class="fas fa-comment me-2"></i>
                            Additional Comments (Optional)
                        </label>
                        <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="Share any additional feedback or suggestions..."></textarea>
                    </div>
                </div>

                <div class="text-center mt-4 d-flex flex-column align-items-center gap-2">
                    <div class="d-flex gap-3">
                        <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-secondary btn-lg px-5" onclick="return confirm('Are you sure you want to cancel? Your answers will not be saved.')">
                            <i class="fas fa-arrow-left me-2"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit Evaluation
                        </button>
                    </div>
                    <p class="text-muted mb-0">
                        <small>Please ensure all questions are rated before submitting</small>
                    </p>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .rating-scale {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .rating-options {
        flex: 1;
        max-width: 300px;
    }

    .rating-btn {
        min-width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .rating-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-check:checked+.rating-btn {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .question-card {
        background: #fafbfc;
        transition: all 0.3s ease;
    }

    .question-card:hover {
        background: #f1f3f4;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .category-header h4 {
        font-size: 1.2rem;
        margin-bottom: 0;
    }

    .set-info-container {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 20px;
    }

    .faculty-info-table td:first-child {
        width: 55%;
        color: #495057;
    }

    .faculty-info-table td:last-child {
        font-weight: 600;
        color: #212529;
    }

    .scale-table th,
    .scale-table td {
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .rating-options {
            justify-content: center;
        }

        .rating-scale {
            flex-direction: column;
            gap: 10px;
        }

        .set-info-container {
            padding: 15px;
        }

        .faculty-info-table td {
            display: block;
            width: 100%;
            padding-top: 0.35rem;
            padding-bottom: 0.35rem;
        }

        .faculty-info-table tr {
            border-bottom: 1px solid #f1f3f5;
        }
    }

</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('evaluationForm');

        // Add visual feedback for selected ratings
        const ratingButtons = document.querySelectorAll('.rating-btn');
        ratingButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from siblings
                const siblings = this.closest('.rating-options').querySelectorAll('.rating-btn');
                siblings.forEach(sib => sib.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');
            });
        });

        // Form validation
        form.addEventListener('submit', function(e) {
            const requiredRatings = form.querySelectorAll('input[type="radio"][required]');
            let allRated = true;

            // Check if all questions have been rated
            const questionGroups = {};
            requiredRatings.forEach(radio => {
                const name = radio.name;
                if (!questionGroups[name]) {
                    questionGroups[name] = false;
                }
                if (radio.checked) {
                    questionGroups[name] = true;
                }
            });

            for (const group in questionGroups) {
                if (!questionGroups[group]) {
                    allRated = false;
                    break;
                }
            }

            if (!allRated) {
                e.preventDefault();
                alert('Please rate all questions before submitting the evaluation.');
                return false;
            }

            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        });
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/students/evaluate.blade.php ENDPATH**/ ?>