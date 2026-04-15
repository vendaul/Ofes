<?php $__env->startSection('content'); ?>

<div class="page-header">
    <h1><i class="fas fa-calendar-alt"></i> Manage Semester / School Year</h1>
    <p>Add or remove academic periods used by assignments and term selection.</p>
</div>

<div class="card mb-4">
    <div class="card-header text-white bg-primary"><i class="fas fa-plus-circle"></i> Add New Period</div>
    <div class="card-body">
        <form action="<?php echo e(route('settings.semester.update')); ?>" method="POST" class="row g-3">
            <?php echo csrf_field(); ?>
            <div class="col-md-4">
                <label for="year" class="form-label">School Year</label>
                <input id="year" name="year" value="<?php echo e(old('year')); ?>" type="text" class="form-control <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="2024-2025">
                <?php $__errorArgs = ['year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div id="year_completion_hint" class="form-text text-muted d-none"></div>
            </div>
            <div class="col-md-4">
                <label for="term" class="form-label">Semester / Term</label>
                <input id="term" name="term" value="<?php echo e(old('term')); ?>" type="text" class="form-control <?php $__errorArgs = ['term'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="First">
                <?php $__errorArgs = ['term'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
                <label for="name" class="form-label">Display Name (optional)</label>
                <input id="name" name="name" value="<?php echo e(old('name')); ?>" type="text" class="form-control" placeholder="First Term 2024-2025">
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i>
                    <?php echo e(!empty($editPeriod) ? 'Update Period' : 'Save Period'); ?>

                </button>
                <?php if(!empty($editPeriod)): ?>
                <a href="<?php echo e(route('settings.semester')); ?>" class="btn btn-secondary">Cancel Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header text-white bg-primary"><i class="fas fa-table"></i> Existing periods</div>
    <div class="card-body table-responsive">
        <?php if($periods->count() > 0): ?>
        <form method="POST" action="<?php echo e(route('settings.semester.update')); ?>" id="bulkDeletePeriodsForm" class="mb-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-danger btn-sm" id="bulkDeletePeriodsBtn" disabled>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </form>
        <?php endif; ?>
        <table class="table table-bordered table-striped table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 44px;" class="text-center">
                        <input type="checkbox" id="periodsSelectAll" title="Select all">
                    </th>
                    <th>ID</th>
                    <th>School Year</th>
                    <th>Semester / Term</th>
                    <th>Name</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="period-select-item" name="bulk_delete_period_ids[]" value="<?php echo e($period->id); ?>" form="bulkDeletePeriodsForm">
                    </td>
                    <td><?php echo e($period->id); ?></td>
                    <td><?php echo e($period->year ?? '-'); ?></td>
                    <td><?php echo e($period->term ?? '-'); ?></td>
                    <td><?php echo e($period->name ?? '-'); ?></td>
                    <td>
                        <?php if(!empty($activePeriod) && $activePeriod->id == $period->id): ?>
                        <span class="badge bg-success">Active</span>
                        <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="d-flex gap-2">
                        <?php if(empty($activePeriod) || $activePeriod->id != $period->id): ?>
                        <form method="POST" action="<?php echo e(route('settings.semester.update')); ?>" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="set_active_id" value="<?php echo e($period->id); ?>" />
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-star"></i> Set Active</button>
                        </form>
                        <?php endif; ?>
                        <button type="button" class="btn btn-warning btn-sm edit-period-btn" data-id="<?php echo e($period->id); ?>" data-year="<?php echo e($period->year); ?>" data-term="<?php echo e($period->term); ?>" data-name="<?php echo e($period->name); ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form method="POST" action="<?php echo e(route('settings.semester.update')); ?>" onsubmit="return confirm('Delete this period?');" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="delete_period_id" value="<?php echo e($period->id); ?>" />
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center">No semester/school-year records found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit period modal -->
<div class="modal fade" id="editPeriodModal" tabindex="-1" aria-labelledby="editPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPeriodModalLabel">Edit Period</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPeriodForm" action="<?php echo e(route('settings.semester.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="edit_period_id" id="edit_period_id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_year" class="form-label">School Year</label>
                        <input type="text" class="form-control" id="modal_year" name="year" required>
                        <div id="modal_year_completion_hint" class="form-text text-muted d-none"></div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_term" class="form-label">Semester / Term</label>
                        <input type="text" class="form-control" id="modal_term" name="term" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_name" class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="modal_name" name="name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function buildSchoolYearSuggestion(rawValue) {
            var value = (rawValue || '').trim();

            // If user types a single year like 2027, suggest 2027-2028.
            if (/^\d{4}$/.test(value)) {
                var startYear = parseInt(value, 10);
                return startYear + '-' + (startYear + 1);
            }

            return '';
        }

        function setupSchoolYearAutofill(yearInputId, hintId) {
            var yearInput = document.getElementById(yearInputId);
            var hint = document.getElementById(hintId);

            if (!yearInput) {
                return;
            }

            function hideHint() {
                if (!hint) {
                    return;
                }
                hint.classList.add('d-none');
                hint.textContent = '';
            }

            function refreshSuggestion() {
                var suggestion = buildSchoolYearSuggestion(yearInput.value);
                yearInput.dataset.schoolYearSuggestion = suggestion;

                if (!hint) {
                    return;
                }

                if (suggestion) {
                    hint.textContent = 'Press Tab to complete: ' + suggestion;
                    hint.classList.remove('d-none');
                } else {
                    hideHint();
                }
            }

            yearInput.addEventListener('input', refreshSuggestion);
            yearInput.addEventListener('focus', refreshSuggestion);

            yearInput.addEventListener('keydown', function(event) {
                if (event.key !== 'Tab' || event.shiftKey) {
                    return;
                }

                var suggestion = yearInput.dataset.schoolYearSuggestion || '';
                if (!suggestion) {
                    return;
                }

                yearInput.value = suggestion;
                hideHint();
                yearInput.dataset.schoolYearSuggestion = '';

                yearInput.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            });

            yearInput.addEventListener('blur', hideHint);
        }

        function buildDisplayName(yearValue, termValue) {
            var year = (yearValue || '').trim();
            var term = (termValue || '').trim();

            if (term && year) {
                return term + ' ' + year;
            }

            return term || year;
        }

        function setupDisplayNameAutofill(yearInputId, termInputId, nameInputId) {
            var yearInput = document.getElementById(yearInputId);
            var termInput = document.getElementById(termInputId);
            var nameInput = document.getElementById(nameInputId);

            if (!yearInput || !termInput || !nameInput) {
                return {
                    syncFromSource: function() {}
                };
            }

            var lastGenerated = buildDisplayName(yearInput.value, termInput.value);
            var currentName = (nameInput.value || '').trim();

            if (!currentName || currentName === lastGenerated) {
                nameInput.dataset.autofill = '1';
            } else {
                nameInput.dataset.autofill = '0';
            }

            function syncFromSource() {
                var generated = buildDisplayName(yearInput.value, termInput.value);
                var nameValue = (nameInput.value || '').trim();
                var shouldAutofill = nameInput.dataset.autofill === '1' || nameValue === '' || nameValue === lastGenerated;

                if (shouldAutofill) {
                    nameInput.value = generated;
                    nameInput.dataset.autofill = '1';
                }

                lastGenerated = generated;
            }

            yearInput.addEventListener('input', syncFromSource);
            termInput.addEventListener('input', syncFromSource);

            nameInput.addEventListener('input', function() {
                var generated = buildDisplayName(yearInput.value, termInput.value);
                var nameValue = (nameInput.value || '').trim();
                nameInput.dataset.autofill = (nameValue === '' || nameValue === generated) ? '1' : '0';
            });

            return {
                syncFromSource: syncFromSource
            };
        }

        setupSchoolYearAutofill('year', 'year_completion_hint');
        setupSchoolYearAutofill('modal_year', 'modal_year_completion_hint');

        var addFormAutofill = setupDisplayNameAutofill('year', 'term', 'name');
        addFormAutofill.syncFromSource();

        var editFormAutofill = setupDisplayNameAutofill('modal_year', 'modal_term', 'modal_name');
        var periodsSelectAll = document.getElementById('periodsSelectAll');
        var bulkDeletePeriodsForm = document.getElementById('bulkDeletePeriodsForm');
        var bulkDeletePeriodsBtn = document.getElementById('bulkDeletePeriodsBtn');
        var periodSelectItems = Array.from(document.querySelectorAll('.period-select-item'));

        var editModal = new bootstrap.Modal(document.getElementById('editPeriodModal'));

        function updateBulkDeletePeriodsState() {
            if (!bulkDeletePeriodsBtn) {
                return;
            }

            var selectedCount = periodSelectItems.filter(function(item) {
                return item.checked;
            }).length;

            bulkDeletePeriodsBtn.disabled = selectedCount === 0;
        }

        if (periodsSelectAll) {
            periodsSelectAll.addEventListener('change', function() {
                periodSelectItems.forEach(function(item) {
                    item.checked = periodsSelectAll.checked;
                });
                updateBulkDeletePeriodsState();
            });
        }

        periodSelectItems.forEach(function(item) {
            item.addEventListener('change', function() {
                if (periodsSelectAll) {
                    periodsSelectAll.checked = periodSelectItems.length > 0 && periodSelectItems.every(function(option) {
                        return option.checked;
                    });
                }
                updateBulkDeletePeriodsState();
            });
        });

        if (bulkDeletePeriodsForm) {
            bulkDeletePeriodsForm.addEventListener('submit', function(event) {
                var selectedCount = periodSelectItems.filter(function(item) {
                    return item.checked;
                }).length;

                if (selectedCount === 0) {
                    event.preventDefault();
                    alert('Please select at least one period to delete.');
                    return;
                }

                if (!confirm('Delete ' + selectedCount + ' selected period(s)?')) {
                    event.preventDefault();
                }
            });
        }

        updateBulkDeletePeriodsState();

        document.querySelectorAll('.edit-period-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var year = this.getAttribute('data-year');
                var term = this.getAttribute('data-term');
                var name = this.getAttribute('data-name');

                document.getElementById('edit_period_id').value = id;
                document.getElementById('modal_year').value = year;
                document.getElementById('modal_term').value = term;
                document.getElementById('modal_name').value = name;

                var modalName = document.getElementById('modal_name');
                var expectedName = buildDisplayName(year, term);
                modalName.dataset.autofill = (!name || name.trim() === '' || name.trim() === expectedName) ? '1' : '0';
                editFormAutofill.syncFromSource();

                editModal.show();
            });
        });
    });

</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/settings/semester.blade.php ENDPATH**/ ?>