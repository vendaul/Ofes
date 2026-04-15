<?php $__env->startSection('content'); ?>

<style>
    .progress-sticky-wrap {
        max-height: 68vh;
        overflow: auto;
    }

    .progress-sticky-header {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: var(--bs-primary) !important;
        background-clip: padding-box;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

</style>

<!-- HEADER -->
<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="mb-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <small class="text-muted">
            Welcome back, <?php echo e(Auth::user()->name ?? 'Admin'); ?>

        </small>
    </div>

    <div class="text-end">
        <small class="text-muted d-block">Active Period</small>
        <strong>
            <?php echo e($activePeriod ? ($activePeriod->year . ' - ' . $activePeriod->term) : 'Not set'); ?>

        </strong>
    </div>
</div>

<?php if(session('info')): ?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <?php echo e(session('info')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- FILTER BAR -->
<div class="card mb-3 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">

            <div class="col-md-3">
                <select id="area_code" name="area_code" class="form-select form-select-sm" onchange="this.form.submit();">
                    <option value="">All Areas</option>
                    <?php $__currentLoopData = $areaOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($code); ?>" <?php echo e($code == $areaCode ? 'selected' : ''); ?>>
                        <?php echo e($name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="col-md-3">
                <select id="college_id" name="college_id" class="form-select form-select-sm" onchange="this.form.submit();">
                    <option value="">All Colleges</option>
                    <?php $__currentLoopData = $collegeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e($id == $collegeId ? 'selected' : ''); ?>>
                        <?php echo e($name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

        </form>
    </div>
</div>

<!-- STAT CARDS -->
<?php
$cardBaseParams = array_filter([
'area_code' => $areaCode,
'college_id' => $collegeId,
], fn ($value) => $value !== null && $value !== '');

$progressBaseParams = array_filter([
'area_code' => $areaCode,
'college_id' => $collegeId,
'view' => $selectedCardView,
], fn ($value) => $value !== null && $value !== '');
?>
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <a href="<?php echo e(route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'total-instructors']))); ?>" class="text-decoration-none text-reset d-block">
            <div class="card shadow-sm text-center p-3 <?php echo e($selectedCardView === 'total-instructors' ? 'border border-primary' : ''); ?>">
                <i class="fas fa-user-tie text-primary mb-2"></i>
                <small>Faculty Performance Overview</small>
                <h4 class="mb-0"><?php echo e(number_format($totalInstructors)); ?></h4>
                <small class="text-muted">Click to view details</small>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="<?php echo e(route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'faculty-evaluated']))); ?>" class="text-decoration-none text-reset d-block">
            <div class="card shadow-sm text-center p-3 <?php echo e($selectedCardView === 'faculty-evaluated' ? 'border border-primary' : ''); ?>">
                <i class="fas fa-check-circle text-success mb-2"></i>
                <small>Faculty Evaluated</small>
                <h4 class="mb-0"><?php echo e(number_format($instructorsEvaluated)); ?></h4>
                <small class="text-muted">
                    <?php echo e(number_format($instructorCompletionRate, 2)); ?>% completion
                </small>
                <small class="text-muted d-block">Click to view details</small>
            </div>
        </a>
    </div>

    

<div class="col-md-3">
    <a href="<?php echo e(route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'evaluation-completion']))); ?>" class="text-decoration-none text-reset d-block">
        <div class="card shadow-sm text-center p-3 <?php echo e($selectedCardView === 'evaluation-completion' ? 'border border-primary' : ''); ?>">
            <i class="fas fa-chart-line text-warning mb-2"></i>
            <small>Evaluation Completion</small>
            <h4 class="mb-0">
                <?php echo e(number_format($studentEvaluationCompletion, 2)); ?>%
            </h4>
            <small class="text-muted">
                <?php echo e(number_format($studentEvaluations)); ?> / <?php echo e(number_format($totalExpectedEvaluations)); ?>

            </small>
            <small class="text-muted d-block">Click to view details</small>
        </div>
    </a>
</div>

<!-- KEEPING YOUR 5TH CARD BUT MAKING IT BALANCED -->
<div class="col-md-3">
    <a href="<?php echo e(route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'assignments-evaluated']))); ?>" class="text-decoration-none text-reset d-block">
        <div class="card shadow-sm text-center p-3 <?php echo e($selectedCardView === 'assignments-evaluated' ? 'border border-primary' : ''); ?>">
            <i class="fas fa-tasks text-secondary mb-2"></i>
            <small>Assignments Evaluated</small>
            <h4 class="mb-0">
                <?php echo e(number_format($evaluatedAssignments)); ?> / <?php echo e(number_format($totalAssignments)); ?>

            </h4>
            <small class="text-muted">
                <?php echo e(number_format($assignmentCompletionRate, 2)); ?>% complete
            </small>
            <small class="text-muted d-block">Click to view details</small>
        </div>
    </a>
</div>

</div>

<?php if($selectedCardView && !$selectedProgressAssignment): ?>
<div class="modal fade" id="cardDetailsModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardDetailsModalLabel">
                    <i class="fas fa-list-ul text-primary"></i> <?php echo e($cardDetailTitle); ?>

                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if($cardDetailRows->isEmpty()): ?>
                <p class="text-muted mb-0">No records found for this card.</p>
                <?php elseif($selectedCardView === 'total-instructors'): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Faculty</th>
                                <th class="text-end">Average Rating (%)</th>
                                <th class="text-end">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $cardDetailRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['instructor_name'] ?: 'N/A'); ?></td>
                                <td class="text-end"><?php echo e($row['average_rating'] !== null ? number_format($row['average_rating'], 2) : 'N/A'); ?></td>
                                <td class="text-end"><?php echo e($row['rank'] ?? 'N/A'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php elseif($selectedCardView === 'faculty-evaluated'): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th class="text-end">Evaluated Assignments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $cardDetailRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['instructor_name']); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['assignment_count'])); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php elseif($selectedCardView === 'student-evaluations'): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Instructor</th>
                                <th>Section</th>
                                <th class="text-end">Students Evaluated </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $cardDetailRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['subject_name']); ?></td>
                                <td><?php echo e($row['instructor_name']); ?></td>
                                <td><?php echo e($row['section_name']); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['evaluated_students'])); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php elseif($selectedCardView === 'evaluation-completion'): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Section</th>
                                <th class="text-end">Evaluated</th>
                                <th class="text-end">Expected</th>
                                <th class="text-end">Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $cardDetailRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['subject_name']); ?></td>
                                <td><?php echo e($row['section_name']); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['evaluated_students'])); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['total_students'])); ?></td>
                                <td class="text-end"><?php echo e(number_format($row['progress'], 2)); ?>%</td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php elseif($selectedCardView === 'assignments-evaluated'): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Instructor</th>
                                <th>Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $cardDetailRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['subject_name']); ?></td>
                                <td><?php echo e($row['instructor_name']); ?></td>
                                <td><?php echo e($row['section_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo e($row['status'] === 'Evaluated' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($row['status']); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <a href="<?php echo e(route('admin.dashboard', $cardBaseParams)); ?>" class="btn btn-outline-secondary">Close</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('cardDetailsModal');
        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }

        const clearUrl = <?php echo json_encode(route('admin.dashboard', $cardBaseParams), 512) ?>;
        const modalInstance = new bootstrap.Modal(modalElement);

        modalElement.addEventListener('hidden.bs.modal', function() {
            window.location.href = clearUrl;
        }, {
            once: true
        });

        modalInstance.show();
    });

</script>
<?php endif; ?>

<!-- PROGRESS SECTION -->
<div class="card shadow-sm mb-4 progress-sticky-wrap">
    <div class="card-header bg-primary d-flex align-items-center gap-2 flex-wrap progress-sticky-header">
        <div class="me-auto">
            <div class="fw-bold text-white">
                <i class="fas fa-chart-bar text-white"></i> Evaluation Progress
            </div>
            <small class="text-white-50 d-block">
                &nbsp;|&nbsp;
                Evaluation Deadline: <?php echo e(!empty($evaluationDeadline) ? \Carbon\Carbon::parse($evaluationDeadline)->format('M d, Y') : 'Not set'); ?>

            </small>
        </div>
        <?php if(!$subjectProgress->isEmpty()): ?>
        <?php
        $progressYearLevels = collect($subjectProgress)
        ->map(function ($item) {
        $sectionName = (string) ($item['section_name'] ?? '');
        if (preg_match('/^(\d+)/', $sectionName, $matches)) {
        return $matches[1];
        }
        return null;
        })
        ->filter()
        ->unique()
        ->sort()
        ->values();

        $progressSections = collect($subjectProgress)
        ->pluck('section_name')
        ->filter()
        ->unique()
        ->sort()
        ->values();
        ?>
        <div class="d-flex flex-column gap-2" style="min-width:280px; max-width:460px; width:100%;">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input type="text" id="progressSearch" class="form-control border-start-0" placeholder="Search subject or section…" autocomplete="off">
            </div>
            <div class="d-flex gap-2">
                <select id="progressYearFilter" class="form-select form-select-sm" style="min-width:170px;">
                    <option value="">All Year Levels</option>
                    <?php $__currentLoopData = $progressYearLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yearLevel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($yearLevel); ?>"><?php echo e($yearLevel); ?><?php echo e($yearLevel == '1' ? 'st' : ($yearLevel == '2' ? 'nd' : ($yearLevel == '3' ? 'rd' : 'th'))); ?> Year</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <select id="progressSectionFilter" class="form-select form-select-sm" style="min-width:180px;">
                    <option value="">All Sections</option>
                    <?php $__currentLoopData = $progressSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sectionName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $sectionYear = null;
                    if (preg_match('/^(\d+)/', (string) $sectionName, $matches)) {
                    $sectionYear = $matches[1];
                    }
                    ?>
                    <option value="<?php echo e(strtolower($sectionName)); ?>" data-year="<?php echo e($sectionYear); ?>"><?php echo e($sectionName); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="card-body">

        <?php if($subjectProgress->isEmpty()): ?>
        <p class="text-muted">No subject assignment data found yet.</p>
        <?php else: ?>

        <div id="progressCards">
            <?php $__currentLoopData = $subjectProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
            $cardYearLevel = null;
            if (preg_match('/^(\d+)/', (string) ($subject['section_name'] ?? ''), $matches)) {
            $cardYearLevel = $matches[1];
            }
            ?>
            <div class="progress-card mb-3 p-3 border rounded" data-search="<?php echo e(strtolower($subject['subject_name'] . ' ' . $subject['section_name'] . ' ' . $subject['instructor_name'])); ?>" data-section="<?php echo e(strtolower($subject['section_name'])); ?>" data-year="<?php echo e($cardYearLevel); ?>">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo e($subject['subject_name']); ?></strong>
                        <div class="small text-muted">
                            <?php echo e($subject['instructor_name']); ?> • <?php echo e($subject['section_name']); ?>

                        </div>
                    </div>

                    <div class="text-end">
                        <strong class="text-primary d-block">
                            <?php echo e(number_format($subject['progress'], 2)); ?>%
                        </strong>
                        <?php if($subject['average_rating'] !== null): ?>
                        <span class="badge bg-info text-dark mt-1" title="Average Rating">
                            <i class="fas fa-star" style="font-size:.7rem;"></i>
                            <?php echo e(number_format($subject['average_rating'], 2)); ?>%
                        </span>
                        <?php endif; ?>
                        <a href="<?php echo e(route('admin.dashboard', array_merge($progressBaseParams, ['progress_assignment_id' => $subject['assignment_id']]))); ?>" class="btn btn-sm btn-primary mt-2 px-3 d-inline-flex align-items-center gap-1 shadow-sm fw-semibold">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>

                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($subject['progress']); ?>%;">
                    </div>
                </div>

                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span><?php echo e(number_format($subject['evaluated_students'])); ?> evaluated</span>
                    <span><?php echo e(number_format($subject['class_size'] ?? 0)); ?> students</span>
                </div>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <p id="progressNoResults" class="text-muted d-none">No results match your search.</p>

        <?php endif; ?>

    </div>
</div>

<script>
    (function() {
        const input = document.getElementById('progressSearch');
        const yearFilter = document.getElementById('progressYearFilter');
        const sectionFilter = document.getElementById('progressSectionFilter');
        if (!input && !yearFilter && !sectionFilter) return;

        const sectionOptionCache = sectionFilter ?
            Array.from(sectionFilter.querySelectorAll('option')).slice(1).map(function(option) {
                return {
                    value: option.value
                    , label: option.textContent
                    , year: option.dataset.year || ''
                , };
            }) : [];

        const syncSectionOptions = function() {
            if (!sectionFilter) return;
            const selectedYear = yearFilter ? yearFilter.value : '';
            const previousValue = sectionFilter.value;

            sectionFilter.innerHTML = '';

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'All Sections';
            sectionFilter.appendChild(defaultOption);

            sectionOptionCache.forEach(function(item) {
                if (selectedYear && item.year !== selectedYear) {
                    return;
                }
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                option.dataset.year = item.year;
                sectionFilter.appendChild(option);
            });

            const isPreviousValueAvailable = Array.from(sectionFilter.options).some(function(option) {
                return option.value === previousValue;
            });
            sectionFilter.value = isPreviousValueAvailable ? previousValue : '';
        };

        const applyProgressFilters = function() {
            const q = input ? input.value.toLowerCase().trim() : '';
            const selectedYear = yearFilter ? yearFilter.value : '';
            const selectedSection = sectionFilter ? sectionFilter.value : '';
            const cards = document.querySelectorAll('#progressCards .progress-card');
            let visible = 0;
            cards.forEach(function(card) {
                const matchSearch = !q || card.dataset.search.includes(q);
                const matchYear = !selectedYear || card.dataset.year === selectedYear;
                const matchSection = !selectedSection || card.dataset.section === selectedSection;
                const match = matchSearch && matchYear && matchSection;
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            const noResults = document.getElementById('progressNoResults');
            if (noResults) noResults.classList.toggle('d-none', visible > 0);
        };

        if (input) {
            input.addEventListener('input', applyProgressFilters);
        }
        if (yearFilter) {
            yearFilter.addEventListener('change', function() {
                syncSectionOptions();
                applyProgressFilters();
            });
        }
        if (sectionFilter) {
            sectionFilter.addEventListener('change', applyProgressFilters);
        }

        syncSectionOptions();
    }());

</script>

<?php if($selectedProgressAssignment): ?>
<div class="modal fade" id="progressStudentsModal" tabindex="-1" aria-labelledby="progressStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressStudentsModalLabel">
                    <i class="fas fa-user-check text-primary"></i>
                    Student Evaluation Status:
                    <?php echo e($selectedProgressAssignment->subject?->name ?? 'Unknown Subject'); ?>

                    (<?php echo e($selectedProgressAssignment->section?->name ?? ('Section ' . $selectedProgressAssignment->section_id)); ?>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if($progressStudentRows->isEmpty()): ?>
                <p class="text-muted mb-0">No enrolled students found for this class schedule.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Student No.</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $progressStudentRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($student['student_number'] ?: 'N/A'); ?></td>
                                <td><?php echo e($student['student_name']); ?></td>
                                <td>
                                    <span class="badge <?php echo e($student['status'] === 'Evaluated' ? 'bg-success' : 'bg-secondary'); ?>">
                                        <?php echo e($student['status']); ?>

                                    </span>
                                </td>
                                <td><?php echo e($student['date_submitted'] ? \Carbon\Carbon::parse($student['date_submitted'])->format('M d, Y h:i A') : '—'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?php
                $pendingCount = $progressStudentRows->where('status', 'Not Evaluated')->count();
                ?>
                <?php if($pendingCount > 0): ?>
                <form method="POST" action="<?php echo e(route('admin.dashboard.notify-pending')); ?>" class="me-auto">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="class_schedule_id" value="<?php echo e($selectedProgressAssignment->id); ?>">
                    <input type="hidden" name="area_code" value="<?php echo e($areaCode); ?>">
                    <input type="hidden" name="college_id" value="<?php echo e($collegeId); ?>">
                    <input type="hidden" name="view" value="<?php echo e($selectedCardView); ?>">
                    <input type="hidden" name="progress_assignment_id" value="<?php echo e($selectedProgressAssignment->id); ?>">
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Send reminder to <?php echo e($pendingCount); ?> student(s) who have not evaluated this subject yet?');">
                        <i class="fas fa-bell"></i> Notify Students (<?php echo e($pendingCount); ?>)
                    </button>
                </form>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.dashboard', $progressBaseParams)); ?>" class="btn btn-outline-secondary">Close</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('progressStudentsModal');
        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }

        const clearUrl = <?php echo json_encode(route('admin.dashboard', $progressBaseParams), 512) ?>;
        const modalInstance = new bootstrap.Modal(modalElement);

        modalElement.addEventListener('hidden.bs.modal', function() {
            window.location.href = clearUrl;
        }, {
            once: true
        });

        modalInstance.show();
    });

</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/April2Ofes copy/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>