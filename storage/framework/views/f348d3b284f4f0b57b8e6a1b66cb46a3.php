<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-question-circle me-2"></i> Evaluation Questions
            </h1>
            <p class="text-muted mb-0">Manage evaluation questions for faculty assessment</p>
        </div>
    </div>

    <!-- ===================== TEMPLATES TABLE ===================== -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Available Templates</h5>
        </div>
        <div class="card-body p-0">
            <?php if(!empty($activeTemplateId) && !empty($evaluationStartDate) && !empty($evaluationEndDate)): ?>
            <div class="alert alert-info m-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <strong>Active evaluation period:</strong>
                    <?php echo e(\Illuminate\Support\Carbon::parse($evaluationStartDate)->format('Y-m-d')); ?> to <?php echo e(\Illuminate\Support\Carbon::parse($evaluationEndDate)->format('Y-m-d')); ?>

                    <?php if(!empty($activeTemplateId)): ?>
                    (Template ID: <?php echo e($activeTemplateId); ?>)
                    <?php endif; ?>
                </div>
                <div class="d-flex" style="gap:.5rem;">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#rescheduleEvaluationModal">
                        <i class="fas fa-calendar-alt me-1"></i> Re-schedule Period
                    </button>
                    <form action="<?php echo e(route('questions.stopEvaluation')); ?>" method="POST" style="margin:0;">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Stop the current evaluation period now?');">
                            <i class="fas fa-stop me-1"></i> Stop Evaluation
                        </button>
                    </form>
                </div>
            </div>
            <?php elseif(!empty($activeTemplateId)): ?>
            <div class="alert alert-warning m-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div>
                    <strong>Template selected:</strong> ID <?php echo e($activeTemplateId); ?>

                    <span class="ms-1">No schedule set yet.</span>
                </div>
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#rescheduleEvaluationModal">
                    <i class="fas fa-play me-1"></i> Start Evaluation
                </button>
            </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Date</th>
                            <th>Semester / School Year</th>
                            <th>Description</th>
                            <th width="250">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $templates ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $isTemplateLocked = in_array((int) $template->id, $lockedTemplateIds ?? [], true);
                        $isTemplateInUse = !empty($activeTemplateId) && ((int) $activeTemplateId === (int) $template->id);
                        $isEvaluationStarted = !empty($activeTemplateId) && !empty($evaluationStartDate) && !empty($evaluationEndDate);
                        $hasEvaluationActuallyStarted = !empty($evaluationStartDate) && \Illuminate\Support\Carbon::now()->gte(\Illuminate\Support\Carbon::parse($evaluationStartDate)->startOfDay());
                        $isActiveTemplateStarted = $isTemplateInUse && $hasEvaluationActuallyStarted;
                        $isTemplateReadOnly = $isTemplateLocked || $isActiveTemplateStarted;
                        $templateQuestions = json_decode($template->questions, true) ?? [];
                        ?>
                        <tr>
                            <td><?php echo e($template->template_date ? \Illuminate\Support\Carbon::parse($template->template_date)->format('Y-m-d') : '—'); ?></td>
                            <td><?php echo e($template->semester); ?><?php echo e($template->semester && $template->school_year ? ' / ' : ''); ?><?php echo e($template->school_year); ?></td>
                            <td>
                                <?php echo e($template->description); ?>

                                <?php if($isTemplateInUse): ?>
                                <span class="badge bg-success ms-2">In Use</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center" style="gap: .5rem;">
                                    <?php if($isTemplateInUse): ?>
                                    <button type="button" class="btn btn-success btn-sm" disabled>In Use</button>
                                    <?php elseif($isEvaluationStarted): ?>
                                    <button type="button" class="btn btn-secondary btn-sm" disabled>Use Locked</button>
                                    <?php else: ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#useTemplateModal<?php echo e($template->id); ?>">Use</button>
                                    <?php endif; ?>

                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewTemplateModal<?php echo e($template->id); ?>">View</button>
                                    <?php if($isTemplateReadOnly): ?>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="alert('Evaluation has started, cant edit/delete.');">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="alert('Evaluation has started, cant edit/delete.');">
                                        Delete
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTemplateModal<?php echo e($template->id); ?>">Edit</button>

                                    <form action="<?php echo e(route('question_templates.destroy', $template->id)); ?>" method="POST" style="margin:0;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <!-- view modal -->
                        <div class="modal fade" id="viewTemplateModal<?php echo e($template->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Template Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if($isTemplateReadOnly): ?>
                                        <div class="alert alert-warning mb-3">
                                            Evaluation has started, cant edit/delete.
                                        </div>
                                        <?php endif; ?>

                                        <form id="viewTemplateForm<?php echo e($template->id); ?>" action="<?php echo e(route('question_templates.update', $template)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <input type="hidden" name="return_url" value="<?php echo e(route('questions.index')); ?>">
                                            <input type="hidden" name="guard_explicit_save" value="1">
                                            <input type="hidden" name="explicit_save" value="0" class="explicit-save-flag">

                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input type="date" name="template_date" class="form-control" value="<?php echo e(old('template_date', $template->template_date)); ?>" <?php echo e($isTemplateReadOnly ? 'disabled' : ''); ?>>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Semester</label>
                                                    <input type="text" name="semester" class="form-control" value="<?php echo e(old('semester', $template->semester)); ?>" <?php echo e($isTemplateReadOnly ? 'disabled' : ''); ?>>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">School Year</label>
                                                    <input type="text" name="school_year" class="form-control" value="<?php echo e(old('school_year', $template->school_year)); ?>" <?php echo e($isTemplateReadOnly ? 'disabled' : ''); ?>>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" <?php echo e($isTemplateReadOnly ? 'disabled' : ''); ?>><?php echo e(old('description', $template->description)); ?></textarea>
                                            </div>

                                            <hr>
                                            <?php
                                            $groupedTemplateQuestions = collect($templateQuestions)->groupBy(function ($q) {
                                            return $q['category'] ?? 'General';
                                            });
                                            ?>
                                            <?php if($isActiveTemplateStarted): ?>
                                            <div class="alert alert-secondary mb-2">
                                                View only mode. This template is currently in use.
                                            </div>
                                            <?php $__currentLoopData = $groupedTemplateQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="mb-3">
                                                <h6 class="mb-2"><?php echo e($category ?: 'General'); ?></h6>
                                                <ol class="mb-0">
                                                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($item['question_text'] ?? (is_string($item) ? $item : '')); ?></li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ol>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php else: ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">Questions</h6>
                                            </div>
                                            <?php if(!$isTemplateReadOnly): ?>
                                            <div class="alert alert-light border py-2 mb-2">
                                                <small class="mb-0 d-block">
                                                    <i class="fas fa-pen me-1"></i> Tip: Category and question fields are editable. Click inside a field to modify text.
                                                </small>
                                            </div>
                                            <?php endif; ?>
                                            <small class="text-muted d-block mb-2">Use Add Question below to add fields, and Delete to remove.</small>

                                            <div class="templateCategoryBlocks" id="templateCategoryBlocks<?php echo e($template->id); ?>">
                                                <?php $templateQuestionIndex = 0; ?>

                                                <?php $__empty_1 = true; $__currentLoopData = $groupedTemplateQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <div class="card shadow-sm mb-4 template-category-block border-0">
                                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center flex-grow-1" style="gap:.5rem;">
                                                            <textarea class="form-control fw-bold bg-transparent text-white template-category-input auto-expand-field" placeholder="Category" title="Click to edit category" style="max-width: 300px;" rows="1" <?php echo e($isTemplateReadOnly ? 'disabled' : ''); ?>><?php echo e($category ?: 'General'); ?></textarea>
                                                        </div>

                                                        <div>
                                                            <?php if(!$isTemplateReadOnly): ?>
                                                            <button type="button" class="btn btn-sm btn-success add-template-category-block">+ Add Category</button>
                                                            <button type="button" class="btn btn-sm btn-danger remove-template-category-block">Delete</button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <div class="card-body">
                                                        <div class="templateQuestionsContainer">
                                                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="d-flex align-items-center mb-3 template-question-row">
                                                                <div class="flex-grow-1">
                                                                    <input type="hidden" class="template-question-hidden-category" name="questions[<?php echo e($templateQuestionIndex); ?>][category]" value="<?php echo e($category ?: 'General'); ?>">

                                                                    <textarea class="form-control shadow-sm auto-expand-field" name="questions[<?php echo e($templateQuestionIndex); ?>][question_text]" placeholder="Enter question..." rows="1" <?php echo e($isTemplateReadOnly ? 'disabled' : ''); ?>><?php echo e($item['question_text'] ?? (is_string($item) ? $item : '')); ?></textarea>
                                                                </div>

                                                                <?php if(!$isTemplateReadOnly): ?>
                                                                <div class="ms-2 d-flex gap-1">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-template-question-row">Delete</button>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <?php $templateQuestionIndex++; ?>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>

                                                        <?php if(!$isTemplateReadOnly): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-template-question-row">+ Add Question</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <div class="text-center py-4 text-muted template-empty-state">
                                                    <p class="mb-2">No questions yet.</p>
                                                    <?php if(!$isTemplateReadOnly): ?>
                                                    <button type="button" class="btn btn-primary add-template-category-block">+ Add First Category</button>
                                                    <?php endif; ?>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <?php if($isActiveTemplateStarted): ?>
                                        <button type="button" class="btn btn-outline-secondary" disabled>View Only</button>
                                        <?php elseif($isTemplateReadOnly): ?>
                                        <button type="button" class="btn btn-warning" onclick="alert('Evaluation has started, cant edit/delete.');">Edit Template</button>
                                        <?php else: ?>
                                        <button type="button" form="viewTemplateForm<?php echo e($template->id); ?>" data-explicit-save="1" data-form-id="viewTemplateForm<?php echo e($template->id); ?>" class="btn btn-warning view-template-save-btn">Save</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- use modal -->
                        <div class="modal fade" id="useTemplateModal<?php echo e($template->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-md">
                                <form action="<?php echo e(route('questions.useTemplate', $template->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Activate Template for Evaluation Period</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php if($isTemplateInUse): ?>
                                            <div class="alert alert-success">
                                                This template is currently in use.
                                            </div>
                                            <?php endif; ?>
                                            <p class="mb-0">Use this template for the next evaluation cycle?</p>
                                            <small class="text-muted">Schedule dates will be set from the Start Evaluation button at the top.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Use Template</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- edit modal -->
                        <div class="modal fade" id="editTemplateModal<?php echo e($template->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form id="editTemplateForm<?php echo e($template->id); ?>" action="<?php echo e(route('question_templates.update', $template)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <input type="hidden" name="return_url" value="<?php echo e(route('questions.index')); ?>">
                                    <input type="hidden" name="guard_explicit_save" value="1">
                                    <input type="hidden" name="explicit_save" value="0" class="explicit-save-flag">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Template</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="template_date_<?php echo e($template->id); ?>" class="form-label">Date</label>
                                                    <input type="date" name="template_date" id="template_date_<?php echo e($template->id); ?>" class="form-control" value="<?php echo e(old('template_date', $template->template_date ?? now()->format('Y-m-d'))); ?>">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="semester_<?php echo e($template->id); ?>" class="form-label">Semester</label>
                                                    <input type="text" name="semester" id="semester_<?php echo e($template->id); ?>" class="form-control" value="<?php echo e(old('semester', $template->semester)); ?>" placeholder="First, Second">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="school_year_<?php echo e($template->id); ?>" class="form-label">School Year</label>
                                                    <input type="text" name="school_year" id="school_year_<?php echo e($template->id); ?>" class="form-control" value="<?php echo e(old('school_year', $template->school_year)); ?>" placeholder="e.g., 2024-2025">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description_<?php echo e($template->id); ?>" class="form-label">Description</label>
                                                <textarea name="description" id="description_<?php echo e($template->id); ?>" class="form-control"><?php echo e(old('description', $template->description)); ?></textarea>
                                            </div>
                                            <!-- note: questions editing not included here for brevity -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" data-explicit-save="1" data-form-id="editTemplateForm<?php echo e($template->id); ?>" class="btn btn-success">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-4 d-flex" style="gap:.5rem;">
        <?php if(!($isNewEvaluationMode ?? false)): ?>
        <a href="<?php echo e(route('questions.index', ['new_evaluation' => 1])); ?>" class="btn btn-primary">
            <i class="fas fa-file-circle-plus me-1"></i> Nev Evaluation
        </a>
        <?php else: ?>
        <a href="<?php echo e(route('questions.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Current View
        </a>
        <?php endif; ?>
    </div>

    <?php if($isNewEvaluationMode ?? false): ?>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Nev Evaluation Builder</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                This builder does not change the active evaluation. It only creates a new template draft that you can activate later.
            </div>
            <form action="<?php echo e(route('question_templates.storeFromQuestions')); ?>" method="POST" id="newEvaluationBuilderForm">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nev_template_date" class="form-label">Date</label>
                        <input type="date" name="template_date" id="nev_template_date" class="form-control" value="<?php echo e(old('template_date', now()->format('Y-m-d'))); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nev_semester" class="form-label">Semester</label>
                        <input type="text" name="semester" id="nev_semester" class="form-control" placeholder="First, Second" value="<?php echo e(old('semester')); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nev_school_year" class="form-label">School Year</label>
                        <input type="text" name="school_year" id="nev_school_year" class="form-control" placeholder="e.g., 2024-2025" value="<?php echo e(old('school_year')); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="nev_description" class="form-label">Description</label>
                    <textarea name="description" id="nev_description" class="form-control" rows="2"><?php echo e(old('description')); ?></textarea>
                </div>

                <hr>
                <div id="nevCategoryBlocks">
                    <div class="card shadow-sm mb-4 nev-category-block template-category-block border-0" data-block-index="0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <textarea class="form-control fw-semibold border-0 bg-transparent text-white nev-category-input auto-expand-field" placeholder="Category" style="max-width: 300px;" rows="1" required>General</textarea>
                            <div>
                                <button type="button" class="btn btn-sm btn-success add-nev-category-block">+ Add Category</button>
                                <button type="button" class="btn btn-sm btn-danger remove-nev-category-block">Delete</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <small class="text-muted d-block mb-2">Use Add Question below to add fields, and Delete to remove.</small>
                            <div class="nevQuestionsContainer">
                                <div class="d-flex align-items-center mb-3 nev-question-row" data-question-index="0">
                                    <div class="flex-grow-1">
                                        <textarea class="form-control shadow-sm auto-expand-field" name="questions[0][0][question_text]" placeholder="Enter evaluation question" rows="1" required></textarea>
                                        <input type="hidden" name="questions[0][0][category]" value="General">
                                    </div>
                                    <div class="ms-2 d-flex gap-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-nev-question-row">Delete</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2 add-nev-question-row">+ Add Question</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex" style="gap:.5rem;">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save as New Template
                    </button>
                    <a href="<?php echo e(route('questions.index')); ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if(!empty($activeTemplateId)): ?>
    <div class="modal fade" id="rescheduleEvaluationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <form action="<?php echo e(route('questions.reschedulePeriod')); ?>" method="POST" onsubmit="return confirm('Reminder: Once evaluation has started, you cannot edit templates or stop evaluation.\n\nDo you want to save this schedule?');">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="template_id" value="<?php echo e($activeTemplateId); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo e((!empty($evaluationStartDate) && !empty($evaluationEndDate)) ? 'Re-schedule Evaluation Period' : 'Start Evaluation'); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted"><?php echo e((!empty($evaluationStartDate) && !empty($evaluationEndDate)) ? 'Update the date range for the active evaluation template.' : 'Set the date range to start the selected evaluation template.'); ?></p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reschedule_start_date" class="form-label">Start Date</label>
                                <input type="date" id="reschedule_start_date" name="evaluation_start_date" class="form-control" value="<?php echo e(old('evaluation_start_date', !empty($evaluationStartDate) ? \Illuminate\Support\Carbon::parse($evaluationStartDate)->format('Y-m-d') : now()->format('Y-m-d'))); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reschedule_end_date" class="form-label">End Date</label>
                                <input type="date" id="reschedule_end_date" name="evaluation_end_date" class="form-control" value="<?php echo e(old('evaluation_end_date', !empty($evaluationEndDate) ? \Illuminate\Support\Carbon::parse($evaluationEndDate)->format('Y-m-d') : now()->addDays(7)->format('Y-m-d'))); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Schedule</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===================== QUESTIONS BY CATEGORY ===================== -->

    <style>
        .template-category-block {
            border-radius: 10px;
        }

        .template-category-block .card-header {
            border-bottom: 1px solid #eee;
        }

        .template-category-input {
            color: #fff;
            -webkit-text-fill-color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 0.45rem 0.65rem;
        }

        .template-category-input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .template-category-input:disabled {
            opacity: 1;
            color: #fff;
            -webkit-text-fill-color: #fff;
        }

        .nev-category-input {
            color: #fff;
            -webkit-text-fill-color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 0.45rem 0.65rem;
        }

        .nev-category-input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .auto-expand-field {
            resize: none;
            overflow: hidden;
            line-height: 1.35;
        }

        .template-question-row .auto-expand-field,
        .nev-question-row .auto-expand-field {
            border-radius: 8px;
        }

    </style>

    <script>
        (function() {
            var nevCategoryBlocks = document.getElementById('nevCategoryBlocks');

            function getNextCategoryBlockIndex() {
                if (!nevCategoryBlocks) return 0;
                return nevCategoryBlocks.querySelectorAll('.nev-category-block').length;
            }

            function getNextQuestionIndex(block) {
                var container = block ? block.querySelector('.nevQuestionsContainer') : null;
                if (!container) return 0;
                return container.querySelectorAll('.nev-question-row').length;
            }

            function getBlockCategoryValue(block) {
                var input = block ? block.querySelector('.nev-category-input') : null;
                return input ? input.value : 'General';
            }

            function addQuestionRowToBlock(block, questionValue) {
                if (!block) return;
                var blockIndex = block.getAttribute('data-block-index');
                var nextQuestionIndex = getNextQuestionIndex(block);
                var container = block.querySelector('.nevQuestionsContainer');
                var safeQuestion = (questionValue || '').trim();
                var row = document.createElement('div');
                row.className = 'd-flex align-items-center mb-3 nev-question-row';
                row.setAttribute('data-question-index', String(nextQuestionIndex));
                row.innerHTML =
                    '<div class="flex-grow-1">' +
                    '<textarea class="form-control shadow-sm auto-expand-field" name="questions[' + blockIndex + '][' + nextQuestionIndex + '][question_text]" placeholder="Enter evaluation question" rows="1" required>' + safeQuestion.replace(/</g, '&lt;') + '</textarea>' +
                    '<input type="hidden" name="questions[' + blockIndex + '][' + nextQuestionIndex + '][category]" value="' + getBlockCategoryValue(block).replace(/"/g, '&quot;') + '">' +
                    '</div>' +
                    '<div class="ms-2 d-flex gap-1">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-nev-question-row">Delete</button>' +
                    '</div>';
                container.appendChild(row);
                var newField = row.querySelector('.auto-expand-field');
                if (newField) {
                    autoGrowField(newField);
                }
            }

            function addCategoryBlock() {
                if (!nevCategoryBlocks) return;
                var nextBlockIndex = getNextCategoryBlockIndex();
                var block = document.createElement('div');
                block.className = 'card shadow-sm mb-4 nev-category-block template-category-block border-0';
                block.setAttribute('data-block-index', String(nextBlockIndex));
                block.innerHTML =
                    '<div class="card-header bg-light d-flex justify-content-between align-items-center">' +
                    '<textarea class="form-control fw-semibold border-0 bg-transparent text-white nev-category-input auto-expand-field" placeholder="Category" style="max-width: 300px;" rows="1" required>General</textarea>' +
                    '<div>' +
                    '<button type="button" class="btn btn-sm btn-success add-nev-category-block">+ Add Category</button> ' +
                    '<button type="button" class="btn btn-sm btn-danger remove-nev-category-block">Delete</button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card-body">' +
                    '<small class="text-muted d-block mb-2">Use Add Question below to add fields, and Delete to remove.</small>' +
                    '<div class="nevQuestionsContainer">' +
                    '<div class="d-flex align-items-center mb-3 nev-question-row" data-question-index="0">' +
                    '<div class="flex-grow-1">' +
                    '<textarea class="form-control shadow-sm auto-expand-field" name="questions[' + nextBlockIndex + '][0][question_text]" placeholder="Enter evaluation question" rows="1" required></textarea>' +
                    '<input type="hidden" name="questions[' + nextBlockIndex + '][0][category]" value="General">' +
                    '</div>' +
                    '<div class="ms-2 d-flex gap-1">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-nev-question-row">Delete</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-primary mt-2 add-nev-question-row">+ Add Question</button>' +
                    '</div>';
                nevCategoryBlocks.appendChild(block);
                block.querySelectorAll('.auto-expand-field').forEach(function(field) {
                    autoGrowField(field);
                });
            }

            function getNextTemplateQuestionIndex(form) {
                const indexInputs = form ? form.querySelectorAll('input[name^="questions["]') : [];
                let nextIndex = 0;
                indexInputs.forEach(function(input) {
                    const match = input.name.match(/^questions\[(\d+)\]/);
                    if (match) {
                        const n = parseInt(match[1], 10);
                        if (!Number.isNaN(n)) {
                            nextIndex = Math.max(nextIndex, n + 1);
                        }
                    }
                });
                return nextIndex;
            }

            function buildTemplateQuestionRow(nextIndex, category) {
                const row = document.createElement('div');
                row.className = 'd-flex align-items-center mb-3 template-question-row';
                row.innerHTML =
                    '<div class="flex-grow-1">' +
                    '<input type="hidden" class="template-question-hidden-category" name="questions[' + nextIndex + '][category]" value="' + String(category || 'General').replace(/"/g, '&quot;') + '">' +
                    '<textarea class="form-control shadow-sm auto-expand-field" name="questions[' + nextIndex + '][question_text]" placeholder="Enter question..." rows="1"></textarea>' +
                    '</div>' +
                    '<div class="ms-2 d-flex gap-1">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-template-question-row">Delete</button>' +
                    '</div>';
                return row;
            }

            function buildTemplateCategoryBlock(nextIndex) {
                const block = document.createElement('div');
                block.className = 'card shadow-sm mb-4 template-category-block border-0';
                block.innerHTML =
                    '<div class="card-header bg-light d-flex justify-content-between align-items-center">' +
                    '<div class="d-flex align-items-center flex-grow-1" style="gap:.5rem;">' +
                    '<textarea class="form-control fw-bold bg-transparent text-white template-category-input auto-expand-field" placeholder="Category" title="Click to edit category" style="max-width: 300px;" rows="1">General</textarea>' +
                    '</div>' +
                    '<div>' +
                    '<button type="button" class="btn btn-sm btn-success add-template-category-block">+ Add Category</button> ' +
                    '<button type="button" class="btn btn-sm btn-danger remove-template-category-block">Delete</button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="card-body">' +
                    '<div class="templateQuestionsContainer"></div>' +
                    '<button type="button" class="btn btn-sm btn-outline-primary mt-2 add-template-question-row">+ Add Question</button>' +
                    '</div>';
                const container = block.querySelector('.templateQuestionsContainer');
                container.appendChild(buildTemplateQuestionRow(nextIndex, 'General'));
                block.querySelectorAll('.auto-expand-field').forEach(function(field) {
                    autoGrowField(field);
                });
                return block;
            }

            function getTrackableFields(form) {
                return form.querySelectorAll('input:not([type="button"]):not([type="submit"]):not([type="reset"]), textarea, select');
            }

            function captureFormSnapshot(form) {
                const fields = getTrackableFields(form);
                const snapshot = [];
                fields.forEach(function(field) {
                    snapshot.push({
                        type: field.type || field.tagName.toLowerCase()
                        , value: field.value
                        , checked: !!field.checked
                    });
                });
                return snapshot;
            }

            function restoreFormSnapshot(form, snapshot) {
                const fields = getTrackableFields(form);
                fields.forEach(function(field, index) {
                    const saved = snapshot[index];
                    if (!saved) {
                        return;
                    }
                    if (saved.type === 'checkbox' || saved.type === 'radio') {
                        field.checked = !!saved.checked;
                    } else {
                        field.value = saved.value;
                        if (field.matches('.auto-expand-field')) {
                            autoGrowField(field);
                        }
                    }
                });
            }

            function isFormDirty(form, snapshot) {
                const current = captureFormSnapshot(form);
                if (!snapshot || current.length !== snapshot.length) {
                    return true;
                }
                for (let i = 0; i < current.length; i++) {
                    if (current[i].value !== snapshot[i].value || current[i].checked !== snapshot[i].checked) {
                        return true;
                    }
                }
                return false;
            }

            function autoGrowField(field) {
                if (!field || field.tagName !== 'TEXTAREA') {
                    return;
                }
                field.style.height = 'auto';
                field.style.height = field.scrollHeight + 'px';
            }

            document.querySelectorAll('.auto-expand-field').forEach(function(field) {
                autoGrowField(field);
            });

            document.querySelectorAll('form[id^="viewTemplateForm"], form[id^="editTemplateForm"]').forEach(function(form) {
                const modalEl = form.closest('.modal');
                if (!modalEl) {
                    return;
                }

                if (!form.dataset.baseAction) {
                    form.dataset.baseAction = form.getAttribute('action') || '';
                }

                const explicitSaveField = form.querySelector('.explicit-save-flag');

                const setInitialSnapshot = function() {
                    form.dataset.initialSnapshot = JSON.stringify(captureFormSnapshot(form));
                };

                setInitialSnapshot();

                form.addEventListener('submit', function(e) {
                    const guardEnabled = !!form.querySelector('input[name="guard_explicit_save"][value="1"]');
                    const explicitSave = explicitSaveField ? explicitSaveField.value === '1' : false;

                    if (guardEnabled && !explicitSave) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        return;
                    }
                    form.dataset.isSubmitting = '1';
                });

                modalEl.addEventListener('hide.bs.modal', function(e) {
                    if (form.dataset.isSubmitting === '1') {
                        return;
                    }

                    if (form.dataset.skipDirtyPrompt === '1') {
                        form.dataset.skipDirtyPrompt = '0';
                        return;
                    }

                    let snapshot = [];
                    try {
                        snapshot = JSON.parse(form.dataset.initialSnapshot || '[]');
                    } catch (_) {
                        snapshot = [];
                    }

                    if (!isFormDirty(form, snapshot)) {
                        return;
                    }

                    const confirmClose = confirm('You have unsaved changes.\n\nOK = Discard changes\nCancel = Stay and continue editing');
                    if (!confirmClose) {
                        e.preventDefault();
                        return;
                    }

                    restoreFormSnapshot(form, snapshot);
                    form.dataset.skipDirtyPrompt = '1';
                    if (explicitSaveField) {
                        explicitSaveField.value = '0';
                    }
                });

                modalEl.addEventListener('shown.bs.modal', function() {
                    form.dataset.isSubmitting = '0';
                    form.dataset.skipDirtyPrompt = '0';
                    if (form.dataset.baseAction) {
                        form.setAttribute('action', form.dataset.baseAction);
                    }
                    if (explicitSaveField) {
                        explicitSaveField.value = '0';
                    }
                    setInitialSnapshot();
                });

                modalEl.querySelectorAll('[data-explicit-save="1"]').forEach(function(saveBtn) {
                    saveBtn.addEventListener('click', function() {
                        const targetFormId = saveBtn.getAttribute('data-form-id');
                        const targetForm = targetFormId ? document.getElementById(targetFormId) : form;
                        if (!targetForm) {
                            return;
                        }
                        const targetExplicit = targetForm.querySelector('.explicit-save-flag');
                        if (targetExplicit) {
                            targetExplicit.value = '1';
                        }
                        const baseAction = targetForm.dataset.baseAction || targetForm.getAttribute('action') || '';
                        const joiner = baseAction.includes('?') ? '&' : '?';
                        targetForm.setAttribute('action', baseAction + joiner + '__manual_save=1');
                        targetForm.requestSubmit();
                    });
                });

                form.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && e.target && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                    }
                });

            });

            if (nevCategoryBlocks) {
                nevCategoryBlocks.addEventListener('input', function(e) {
                    if (!e.target.matches('.nev-category-input')) return;
                    var block = e.target.closest('.nev-category-block');
                    if (!block) return;
                    var category = e.target.value;
                    block.querySelectorAll('.nev-question-row input[type="hidden"][name$="[category]"]').forEach(function(hidden) {
                        hidden.value = category;
                    });
                });
            }

            // add/remove question rows for both create modal and standalone
            document.addEventListener('click', function(e) {
                if (e.target.matches('.add-nev-category-block')) {
                    addCategoryBlock();
                    return;
                }

                if (e.target.matches('.remove-nev-category-block')) {
                    var blocks = document.querySelectorAll('.nev-category-block');
                    if (blocks.length <= 1) {
                        return;
                    }
                    var block = e.target.closest('.nev-category-block');
                    block && block.remove();
                    return;
                }

                if (e.target.matches('.add-nev-question-row')) {
                    var block = e.target.closest('.nev-category-block');
                    addQuestionRowToBlock(block, '');
                    return;
                }

                if (e.target.matches('.remove-nev-question-row')) {
                    var block = e.target.closest('.nev-category-block');
                    var rows = block ? block.querySelectorAll('.nev-question-row') : [];
                    if (rows.length <= 1) {
                        return;
                    }
                    var row = e.target.closest('.nev-question-row');
                    row && row.remove();
                    return;
                }

                if (e.target.matches('.add-question')) {
                    const container = e.target.closest('#questions-container');
                    if (!container) return;
                    const row = document.createElement('div');
                    row.className = 'input-group mb-2 question-row';
                    row.innerHTML = '<input type="text" class="form-control" name="question_text[]" placeholder="Enter evaluation question" required>' +
                        '<button class="btn btn-outline-secondary remove-question" type="button">-</button>';
                    container.appendChild(row);
                }
                if (e.target.matches('.remove-question')) {
                    const row = e.target.closest('.question-row');
                    row && row.remove();
                }

                if (e.target.matches('.add-template-category-block')) {
                    const modalBody = e.target.closest('.modal-body');
                    const blocksRoot = modalBody ? modalBody.querySelector('.templateCategoryBlocks') : null;
                    if (!blocksRoot) return;

                    const emptyState = blocksRoot.querySelector('.template-empty-state');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    const form = e.target.closest('form');
                    const nextIndex = getNextTemplateQuestionIndex(form);
                    blocksRoot.appendChild(buildTemplateCategoryBlock(nextIndex));
                    return;
                }

                if (e.target.matches('.remove-template-category-block')) {
                    if (!confirm('Are you sure you want to delete this category and all its questions?')) {
                        return;
                    }
                    const blocksRoot = e.target.closest('.templateCategoryBlocks');
                    const blocks = blocksRoot ? blocksRoot.querySelectorAll('.template-category-block') : [];
                    if (blocks.length <= 1) {
                        return;
                    }
                    const block = e.target.closest('.template-category-block');
                    if (block) {
                        block.remove();
                    }
                    const remaining = blocksRoot.querySelectorAll('.template-category-block').length;
                    if (remaining === 0) {
                        const emptyState = document.createElement('div');
                        emptyState.className = 'text-center py-4 text-muted template-empty-state';
                        emptyState.innerHTML = '<p class="mb-2">No questions yet.</p><button type="button" class="btn btn-primary add-template-category-block">+ Add First Category</button>';
                        blocksRoot.appendChild(emptyState);
                    }
                    return;
                }

                if (e.target.matches('.add-template-question-row')) {
                    const block = e.target.closest('.template-category-block');
                    const container = block ? block.querySelector('.templateQuestionsContainer') : null;
                    if (!container) return;

                    const form = e.target.closest('form');
                    const nextIndex = getNextTemplateQuestionIndex(form);

                    const categoryInput = block.querySelector('.template-category-input');
                    const category = categoryInput ? categoryInput.value : 'General';
                    container.appendChild(buildTemplateQuestionRow(nextIndex, category));
                    return;
                }

                if (e.target.matches('.remove-template-question-row')) {
                    if (!confirm('Are you sure you want to delete this question field?')) {
                        return;
                    }
                    const block = e.target.closest('.template-category-block');
                    const rows = block ? block.querySelectorAll('.template-question-row') : [];
                    if (rows.length <= 1) {
                        return;
                    }
                    const row = e.target.closest('.template-question-row');
                    row && row.remove();
                    return;
                }
            });

            document.addEventListener('input', function(e) {
                if (e.target.matches('.auto-expand-field')) {
                    autoGrowField(e.target);
                }
                if (!e.target.matches('.template-category-input')) {
                    return;
                }
                const block = e.target.closest('.template-category-block');
                if (!block) {
                    return;
                }
                block.querySelectorAll('.template-question-hidden-category').forEach(function(hidden) {
                    hidden.value = e.target.value || 'General';
                });
            });
        })();

    </script>

    <!-- ===================== QUESTIONS BY CATEGORY ===================== -->

    <?php if(!($isNewEvaluationMode ?? false) && empty($activeTemplateId) && $questions->count() > 0): ?>
    <?php $grouped = $questions->groupBy('category'); ?>

    <?php $__currentLoopData = $grouped->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryQuestions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card shadow-sm mb-4">

        <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
            <h6 class="mb-0"><?php echo e($category ?? 'Uncategorized'); ?></h6>

            <button type="button" class="btn btn-sm btn-light " data-bs-toggle="modal" data-bs-target="#addQModal<?php echo e($loop->index); ?>">
                <i class="fas fa-plus"></i> Add Question
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <?php $__currentLoopData = $categoryQuestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex justify-content-between align-items-center flex-wrap">

                                    <div class="me-3">
                                        <strong><?php echo e($index + 1); ?>.</strong>
                                        <?php echo e($q->question_text); ?>

                                    </div>

                                    <div class="d-inline-flex align-items-center" style="gap: .5rem;">
                                        <a href="<?php echo e(route('questions.edit',$q->question_id)); ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="<?php echo e(route('questions.destroy',$q->question_id)); ?>" method="POST" style="margin:0;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this question?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Modals for adding questions -->
    <?php $__currentLoopData = $grouped->reverse(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $categoryQuestions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="addQModal<?php echo e($loop->index); ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Question to <?php echo e($category); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('questions.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" name="category" value="<?php echo e($category); ?>">
                        <div class="form-group" id="questions-container">
                            <label class="form-label">Question Text</label>
                            <small class="text-muted">Click + to add extra question fields.</small>
                            <div class="input-group mb-2 question-row">
                                <input type="text" class="form-control" name="question_text[]" placeholder="Question" required>
                                <button class="btn btn-outline-secondary add-question" type="button">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php elseif(!($isNewEvaluationMode ?? false) && !empty($activeTemplateId)): ?>

    <?php elseif(!($isNewEvaluationMode ?? false)): ?>
    <div class="alert alert-info">
        No questions found.
        <a href="<?php echo e(route('questions.create')); ?>">Create one now</a>
    </div>
    <?php endif; ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes/resources/views/questions/index.blade.php ENDPATH**/ ?>