@extends('layouts.admin')

@section('content')

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
            Welcome back, {{ Auth::user()->name ?? 'Admin' }}
        </small>
    </div>

    <div class="text-end">
        <small class="text-muted d-block">Active Period</small>
        <strong>
            {{ $activePeriod ? ($activePeriod->year . ' - ' . $activePeriod->term) : 'Not set' }}
        </strong>
    </div>
</div>

@if(session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('info') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- FILTER BAR -->
<div class="card mb-3 shadow-sm">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">

            <div class="col-md-3">
                <select id="area_code" name="area_code" class="form-select form-select-sm" onchange="this.form.submit();">
                    <option value="">All Areas</option>
                    @foreach ($areaOptions as $code => $name)
                    <option value="{{ $code }}" {{ $code == $areaCode ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select id="college_id" name="college_id" class="form-select form-select-sm" onchange="this.form.submit();">
                    <option value="">All Colleges</option>
                    @foreach ($collegeOptions as $id => $name)
                    <option value="{{ $id }}" {{ $id == $collegeId ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
            </div>

        </form>
    </div>
</div>

<!-- STAT CARDS -->
@php
$cardBaseParams = array_filter([
'area_code' => $areaCode,
'college_id' => $collegeId,
], fn ($value) => $value !== null && $value !== '');

$progressBaseParams = array_filter([
'area_code' => $areaCode,
'college_id' => $collegeId,
'view' => $selectedCardView,
], fn ($value) => $value !== null && $value !== '');
@endphp
<div class="row g-3 mb-4">

    <div class="col-md-3">
        <a href="{{ route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'total-instructors'])) }}" class="text-decoration-none text-reset d-block">
            <div class="card shadow-sm text-center p-3 {{ $selectedCardView === 'total-instructors' ? 'border border-primary' : '' }}">
                <i class="fas fa-user-tie text-primary mb-2"></i>
                <small>Faculty Performance Overview</small>
                <h4 class="mb-0">{{ number_format($totalInstructors) }}</h4>
                <small class="text-muted">Click to view details</small>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'faculty-evaluated'])) }}" class="text-decoration-none text-reset d-block">
            <div class="card shadow-sm text-center p-3 {{ $selectedCardView === 'faculty-evaluated' ? 'border border-primary' : '' }}">
                <i class="fas fa-check-circle text-success mb-2"></i>
                <small>Faculty Evaluated</small>
                <h4 class="mb-0">{{ number_format($instructorsEvaluated) }}</h4>
                <small class="text-muted">
                    {{ number_format($instructorCompletionRate, 2) }}% completion
                </small>
                <small class="text-muted d-block">Click to view details</small>
            </div>
        </a>
    </div>

    {{-- <div class="col-md-3">
        <a href="{{ route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'student-evaluations'])) }}" class="text-decoration-none text-reset d-block">
    <div class="card shadow-sm text-center p-3 {{ $selectedCardView === 'student-evaluations' ? 'border border-primary' : '' }}">
        <i class="fas fa-users text-info mb-2"></i>
        <small>Student Evaluations</small>
        <h4 class="mb-0">{{ number_format($studentEvaluations) }}</h4>
        <small class="text-muted">
            {{ number_format($studentEvaluators) }} students
        </small>
        <small class="text-muted d-block">Click to view details</small>
    </div>
    </a>
</div> --}}

<div class="col-md-3">
    <a href="{{ route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'evaluation-completion'])) }}" class="text-decoration-none text-reset d-block">
        <div class="card shadow-sm text-center p-3 {{ $selectedCardView === 'evaluation-completion' ? 'border border-primary' : '' }}">
            <i class="fas fa-chart-line text-warning mb-2"></i>
            <small>Evaluation Completion</small>
            <h4 class="mb-0">
                {{ number_format($studentEvaluationCompletion, 2) }}%
            </h4>
            <small class="text-muted">
                {{ number_format($studentEvaluations) }} / {{ number_format($totalExpectedEvaluations) }}
            </small>
            <small class="text-muted d-block">Click to view details</small>
        </div>
    </a>
</div>

<!-- KEEPING YOUR 5TH CARD BUT MAKING IT BALANCED -->
<div class="col-md-3">
    <a href="{{ route('admin.dashboard', array_merge($cardBaseParams, ['view' => 'assignments-evaluated'])) }}" class="text-decoration-none text-reset d-block">
        <div class="card shadow-sm text-center p-3 {{ $selectedCardView === 'assignments-evaluated' ? 'border border-primary' : '' }}">
            <i class="fas fa-tasks text-secondary mb-2"></i>
            <small>Assignments Evaluated</small>
            <h4 class="mb-0">
                {{ number_format($evaluatedAssignments) }} / {{ number_format($totalAssignments) }}
            </h4>
            <small class="text-muted">
                {{ number_format($assignmentCompletionRate, 2) }}% complete
            </small>
            <small class="text-muted d-block">Click to view details</small>
        </div>
    </a>
</div>

</div>

@if($selectedCardView && !$selectedProgressAssignment)
<div class="modal fade" id="cardDetailsModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cardDetailsModalLabel">
                    <i class="fas fa-list-ul text-primary"></i> {{ $cardDetailTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($cardDetailRows->isEmpty())
                <p class="text-muted mb-0">No records found for this card.</p>
                @elseif($selectedCardView === 'total-instructors')
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
                            @foreach($cardDetailRows as $row)
                            <tr>
                                <td>{{ $row['instructor_name'] ?: 'N/A' }}</td>
                                <td class="text-end">{{ $row['average_rating'] !== null ? number_format($row['average_rating'], 2) : 'N/A' }}</td>
                                <td class="text-end">{{ $row['rank'] ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif($selectedCardView === 'faculty-evaluated')
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th class="text-end">Evaluated Assignments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cardDetailRows as $row)
                            <tr>
                                <td>{{ $row['instructor_name'] }}</td>
                                <td class="text-end">{{ number_format($row['assignment_count']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif($selectedCardView === 'student-evaluations')
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
                            @foreach($cardDetailRows as $row)
                            <tr>
                                <td>{{ $row['subject_name'] }}</td>
                                <td>{{ $row['instructor_name'] }}</td>
                                <td>{{ $row['section_name'] }}</td>
                                <td class="text-end">{{ number_format($row['evaluated_students']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif($selectedCardView === 'evaluation-completion')
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
                            @foreach($cardDetailRows as $row)
                            <tr>
                                <td>{{ $row['subject_name'] }}</td>
                                <td>{{ $row['section_name'] }}</td>
                                <td class="text-end">{{ number_format($row['evaluated_students']) }}</td>
                                <td class="text-end">{{ number_format($row['total_students']) }}</td>
                                <td class="text-end">{{ number_format($row['progress'], 2) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif($selectedCardView === 'assignments-evaluated')
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
                            @foreach($cardDetailRows as $row)
                            <tr>
                                <td>{{ $row['subject_name'] }}</td>
                                <td>{{ $row['instructor_name'] }}</td>
                                <td>{{ $row['section_name'] }}</td>
                                <td>
                                    <span class="badge {{ $row['status'] === 'Evaluated' ? 'bg-success' : 'bg-secondary' }}">{{ $row['status'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.dashboard', $cardBaseParams) }}" class="btn btn-outline-secondary">Close</a>
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

        const clearUrl = @json(route('admin.dashboard', $cardBaseParams));
        const modalInstance = new bootstrap.Modal(modalElement);

        modalElement.addEventListener('hidden.bs.modal', function() {
            window.location.href = clearUrl;
        }, {
            once: true
        });

        modalInstance.show();
    });

</script>
@endif

<!-- PROGRESS SECTION -->
<div class="card shadow-sm mb-4 progress-sticky-wrap">
    <div class="card-header bg-primary d-flex align-items-center gap-2 flex-wrap progress-sticky-header">
        <div class="me-auto">
            <div class="fw-bold text-white">
                <i class="fas fa-chart-bar text-white"></i> Evaluation Progress
            </div>
            <small class="text-white-50 d-block">
                &nbsp;|&nbsp;
                Evaluation Deadline: {{ !empty($evaluationDeadline) ? \Carbon\Carbon::parse($evaluationDeadline)->format('M d, Y') : 'Not set' }}
            </small>
        </div>
        @if (!$subjectProgress->isEmpty())
        @php
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
        @endphp
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
                    @foreach($progressYearLevels as $yearLevel)
                    <option value="{{ $yearLevel }}">{{ $yearLevel }}{{ $yearLevel == '1' ? 'st' : ($yearLevel == '2' ? 'nd' : ($yearLevel == '3' ? 'rd' : 'th')) }} Year</option>
                    @endforeach
                </select>
                <select id="progressSectionFilter" class="form-select form-select-sm" style="min-width:180px;">
                    <option value="">All Sections</option>
                    @foreach($progressSections as $sectionName)
                    @php
                    $sectionYear = null;
                    if (preg_match('/^(\d+)/', (string) $sectionName, $matches)) {
                    $sectionYear = $matches[1];
                    }
                    @endphp
                    <option value="{{ strtolower($sectionName) }}" data-year="{{ $sectionYear }}">{{ $sectionName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
    </div>

    <div class="card-body">

        @if ($subjectProgress->isEmpty())
        <p class="text-muted">No subject assignment data found yet.</p>
        @else

        <div id="progressCards">
            @foreach ($subjectProgress as $subject)
            @php
            $cardYearLevel = null;
            if (preg_match('/^(\d+)/', (string) ($subject['section_name'] ?? ''), $matches)) {
            $cardYearLevel = $matches[1];
            }
            @endphp
            <div class="progress-card mb-3 p-3 border rounded" data-search="{{ strtolower($subject['subject_name'] . ' ' . $subject['section_name'] . ' ' . $subject['instructor_name']) }}" data-section="{{ strtolower($subject['section_name']) }}" data-year="{{ $cardYearLevel }}">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $subject['subject_name'] }}</strong>
                        <div class="small text-muted">
                            {{ $subject['instructor_name'] }} • {{ $subject['section_name'] }}
                        </div>
                    </div>

                    <div class="text-end">
                        <strong class="text-primary d-block">
                            {{ number_format($subject['progress'], 2) }}%
                        </strong>
                        @if($subject['average_rating'] !== null)
                        <span class="badge bg-info text-dark mt-1" title="Average Rating">
                            <i class="fas fa-star" style="font-size:.7rem;"></i>
                            {{ number_format($subject['average_rating'], 2) }}%
                        </span>
                        @endif
                        <a href="{{ route('admin.dashboard', array_merge($progressBaseParams, ['progress_assignment_id' => $subject['assignment_id']])) }}" class="btn btn-sm btn-primary mt-2 px-3 d-inline-flex align-items-center gap-1 shadow-sm fw-semibold">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </div>
                </div>

                <div class="progress mt-2" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $subject['progress'] }}%;">
                    </div>
                </div>

                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>{{ number_format($subject['evaluated_students']) }} evaluated</span>
                    <span>{{ number_format($subject['class_size'] ?? 0) }} students</span>
                </div>

            </div>
            @endforeach
        </div>

        <p id="progressNoResults" class="text-muted d-none">No results match your search.</p>

        @endif

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

@if($selectedProgressAssignment)
<div class="modal fade" id="progressStudentsModal" tabindex="-1" aria-labelledby="progressStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressStudentsModalLabel">
                    <i class="fas fa-user-check text-primary"></i>
                    Student Evaluation Status:
                    {{ $selectedProgressAssignment->subject?->name ?? 'Unknown Subject' }}
                    ({{ $selectedProgressAssignment->section?->name ?? ('Section ' . $selectedProgressAssignment->section_id) }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($progressStudentRows->isEmpty())
                <p class="text-muted mb-0">No enrolled students found for this class schedule.</p>
                @else
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
                            @foreach($progressStudentRows as $student)
                            <tr>
                                <td>{{ $student['student_number'] ?: 'N/A' }}</td>
                                <td>{{ $student['student_name'] }}</td>
                                <td>
                                    <span class="badge {{ $student['status'] === 'Evaluated' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $student['status'] }}
                                    </span>
                                </td>
                                <td>{{ $student['date_submitted'] ? \Carbon\Carbon::parse($student['date_submitted'])->format('M d, Y h:i A') : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                @php
                $pendingCount = $progressStudentRows->where('status', 'Not Evaluated')->count();
                @endphp
                @if($pendingCount > 0)
                <form method="POST" action="{{ route('admin.dashboard.notify-pending') }}" class="me-auto">
                    @csrf
                    <input type="hidden" name="class_schedule_id" value="{{ $selectedProgressAssignment->id }}">
                    <input type="hidden" name="area_code" value="{{ $areaCode }}">
                    <input type="hidden" name="college_id" value="{{ $collegeId }}">
                    <input type="hidden" name="view" value="{{ $selectedCardView }}">
                    <input type="hidden" name="progress_assignment_id" value="{{ $selectedProgressAssignment->id }}">
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Send reminder to {{ $pendingCount }} student(s) who have not evaluated this subject yet?');">
                        <i class="fas fa-bell"></i> Notify Students ({{ $pendingCount }})
                    </button>
                </form>
                @endif
                <a href="{{ route('admin.dashboard', $progressBaseParams) }}" class="btn btn-outline-secondary">Close</a>
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

        const clearUrl = @json(route('admin.dashboard', $progressBaseParams));
        const modalInstance = new bootstrap.Modal(modalElement);

        modalElement.addEventListener('hidden.bs.modal', function() {
            window.location.href = clearUrl;
        }, {
            once: true
        });

        modalInstance.show();
    });

</script>
@endif

@endsection
