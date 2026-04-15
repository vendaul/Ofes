@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add Student to {{ $classSchedule->subject?->name ?? 'Unknown Subject' }}</h1>
    <p>Class: {{ $classSchedule->section?->name ?? 'Unknown Section' }} | {{ $classSchedule->ay }} {{ $classSchedule->term }}</p>
</div>

@php $activeTab = old('mode', 'existing') === 'new' ? 'new' : 'existing'; @endphp

<div class="card">
    <div class="card-header text-white bg-primary">
        <ul class="nav nav-tabs card-header-tabs" id="addStudentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'existing' ? 'active' : '' }}" id="tab-existing" data-bs-toggle="tab" data-bs-target="#pane-existing" type="button" role="tab">
                    <i class="fas fa-search"></i> Pick Existing Student
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'new' ? 'active' : '' }}" id="tab-new" data-bs-toggle="tab" data-bs-target="#pane-new" type="button" role="tab">
                    <i class="fas fa-user-plus"></i> Create New Student
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content" id="addStudentTabsContent">

        {{-- ── TAB 1: Pick existing student ── --}}
        <div class="tab-pane fade {{ $activeTab === 'existing' ? 'show active' : '' }}" id="pane-existing" role="tabpanel">
            @if(count($availableStudents) > 0)
            <form action="{{ route('class-schedule-students.store', $classSchedule->id) }}" method="POST" class="mt-2">
                @csrf
                <input type="hidden" name="mode" value="existing">

                <div class="mb-3">
                    <label for="user_student_id" class="form-label">Select Student <span class="text-danger">*</span></label>
                    <select name="user_student_id" id="user_student_id" class="form-select @error('user_student_id') is-invalid @enderror" required>
                        <option value="">-- Select a student --</option>
                        @foreach($availableStudents as $s)
                        <option value="{{ $s->id }}" {{ old('user_student_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->sid }} — {{ $s->fname }} {{ $s->lname }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="class_type" value="Regular">
                <input type="hidden" name="class_status" value="P">
                <input type="hidden" name="remark" value="ENROLLED">

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="enroll_all_subjects" id="enroll_all_subjects_existing" value="1" {{ old('enroll_all_subjects') ? 'checked' : '' }}>
                    <label class="form-check-label" for="enroll_all_subjects_existing">
                        Enroll in all subjects in this section/year level/SY/term
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="enroll_specific_subjects" id="enroll_specific_subjects_existing" value="1" {{ old('enroll_specific_subjects') ? 'checked' : '' }}>
                    <label class="form-check-label" for="enroll_specific_subjects_existing">
                        Enroll in specific subjects only
                    </label>
                </div>

                <div class="border rounded p-3 mb-3 subject-picker" id="subject_picker_existing" style="display:none;">
                    <small class="text-muted d-block mb-2">Select one or more subjects:</small>
                    @forelse($sectionSubjectSchedules as $schedule)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="selected_schedule_ids[]" id="existing_schedule_{{ $schedule->id }}" value="{{ $schedule->id }}" {{ in_array((string) $schedule->id, array_map('strval', old('selected_schedule_ids', [])), true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="existing_schedule_{{ $schedule->id }}">
                            {{ $schedule->subject?->code ?? 'N/A' }} - {{ $schedule->subject?->name ?? 'Unknown Subject' }}
                        </label>
                    </div>
                    @empty
                    <small class="text-muted">No other subjects found for this section/year level/SY/term.</small>
                    @endforelse
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Student</button>
                    <a href="{{ route('class-schedule-students.index', $classSchedule->id) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
            @else
            <div class="alert alert-info mt-2 mb-0">
                <i class="fas fa-info-circle"></i> All available students are already enrolled in this class schedule.
            </div>
            <div class="mt-3">
                <a href="{{ route('class-schedule-students.index', $classSchedule->id) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            @endif
        </div>

        {{-- ── TAB 2: Create new student ── --}}
        <div class="tab-pane fade {{ $activeTab === 'new' ? 'show active' : '' }}" id="pane-new" role="tabpanel">
            <form action="{{ route('class-schedule-students.store', $classSchedule->id) }}" method="POST" class="mt-2">
                @csrf
                <input type="hidden" name="mode" value="new">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="sid" class="form-label">Student Number <span class="text-danger">*</span></label>
                        <input type="text" name="sid" id="sid" class="form-control @error('sid') is-invalid @enderror" value="{{ old('sid') }}" required>
                        @error('sid')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8">
                        <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="lname" id="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname') }}" required>
                        @error('lname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="fname" id="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname') }}" required>
                        @error('fname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="mname" class="form-label">Middle Name</label>
                        <input type="text" name="mname" id="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname') }}">
                        @error('mname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email_new" class="form-label">Email <span class="text-muted">(optional)</span></label>
                    <input type="email" name="email" id="email_new" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="class_type" value="Regular">

                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="enroll_all_subjects" id="enroll_all_subjects_new" value="1" {{ old('enroll_all_subjects') ? 'checked' : '' }}>
                    <label class="form-check-label" for="enroll_all_subjects_new">
                        Enroll in all subjects in this section/year level/SY/term
                    </label>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="enroll_specific_subjects" id="enroll_specific_subjects_new" value="1" {{ old('enroll_specific_subjects') ? 'checked' : '' }}>
                    <label class="form-check-label" for="enroll_specific_subjects_new">
                        Enroll in specific subjects only
                    </label>
                </div>

                <div class="border rounded p-3 mb-3 subject-picker" id="subject_picker_new" style="display:none;">
                    <small class="text-muted d-block mb-2">Select one or more subjects:</small>
                    @forelse($sectionSubjectSchedules as $schedule)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="selected_schedule_ids[]" id="new_schedule_{{ $schedule->id }}" value="{{ $schedule->id }}" {{ in_array((string) $schedule->id, array_map('strval', old('selected_schedule_ids', [])), true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="new_schedule_{{ $schedule->id }}">
                            {{ $schedule->subject?->code ?? 'N/A' }} - {{ $schedule->subject?->name ?? 'Unknown Subject' }}
                        </label>
                    </div>
                    @empty
                    <small class="text-muted">No other subjects found for this section/year level/SY/term.</small>
                    @endforelse
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="fas fa-user-plus"></i> Create &amp; Enroll</button>
                    <a href="{{ route('class-schedule-students.index', $classSchedule->id) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function wireSubjectPicker(allId, specificId, pickerId) {
            const allCheckbox = document.getElementById(allId);
            const specificCheckbox = document.getElementById(specificId);
            const picker = document.getElementById(pickerId);
            if (!allCheckbox || !specificCheckbox || !picker) return;

            const subjectCheckboxes = Array.from(picker.querySelectorAll('input[name="selected_schedule_ids[]"]'));

            const toggle = () => {
                if (allCheckbox.checked) {
                    picker.style.display = 'block';
                    subjectCheckboxes.forEach(cb => {
                        cb.checked = true;
                        cb.disabled = true;
                    });
                    return;
                }

                subjectCheckboxes.forEach(cb => {
                    cb.disabled = false;
                });

                picker.style.display = specificCheckbox.checked ? 'block' : 'none';
            };

            allCheckbox.addEventListener('change', function() {
                if (allCheckbox.checked) {
                    specificCheckbox.checked = false;
                }
                toggle();
            });

            specificCheckbox.addEventListener('change', function() {
                if (specificCheckbox.checked) {
                    allCheckbox.checked = false;
                    subjectCheckboxes.forEach(cb => {
                        cb.disabled = false;
                        cb.checked = false;
                    });
                }
                toggle();
            });
            toggle();

            const form = allCheckbox.closest('form');
            if (form) {
                const submitButton = form.querySelector('button[type="submit"]');

                const updateSubmitState = () => {
                    const hasSelection = subjectCheckboxes.some(cb => cb.checked);
                    if (submitButton) {
                        submitButton.disabled = specificCheckbox.checked && !hasSelection;
                    }
                };

                subjectCheckboxes.forEach(cb => cb.addEventListener('change', updateSubmitState));

                form.addEventListener('submit', function(event) {
                    if (specificCheckbox.checked) {
                        const hasSelection = subjectCheckboxes.some(cb => cb.checked);
                        if (!hasSelection) {
                            event.preventDefault();
                            event.stopImmediatePropagation();
                            alert('Please select at least one subject before saving.');
                        }
                    }
                });

                updateSubmitState();
            }
        }

        wireSubjectPicker('enroll_all_subjects_existing', 'enroll_specific_subjects_existing', 'subject_picker_existing');
        wireSubjectPicker('enroll_all_subjects_new', 'enroll_specific_subjects_new', 'subject_picker_new');
    });

</script>

@endsection
