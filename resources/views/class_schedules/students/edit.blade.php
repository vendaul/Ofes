@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-user-edit"></i> Edit Student</h1>
    <p>Class: {{ $classSchedule->subject?->name ?? 'Unknown Subject' }} — {{ $classSchedule->section?->name ?? 'Unknown Section' }} | {{ $classSchedule->ay }} {{ $classSchedule->term }}</p>
</div>

<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-user-edit"></i> Edit Student Details
    </div>
    <div class="card-body">
        <form action="{{ route('class-schedule-students.update', [$classSchedule->id, $enrollment->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="sid" class="form-label">Student Number <span class="text-danger">*</span></label>
                    <input type="text" name="sid" id="sid" class="form-control @error('sid') is-invalid @enderror" value="{{ old('sid', $student->sid) }}" required>
                    @error('sid')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8">
                    <label for="lname" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="lname" id="lname" class="form-control @error('lname') is-invalid @enderror" value="{{ old('lname', $student->lname) }}" required>
                    @error('lname')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="fname" id="fname" class="form-control @error('fname') is-invalid @enderror" value="{{ old('fname', $student->fname) }}" required>
                    @error('fname')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="mname" class="form-label">Middle Name</label>
                    <input type="text" name="mname" id="mname" class="form-control @error('mname') is-invalid @enderror" value="{{ old('mname', $student->mname) }}">
                    @error('mname')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->email) }}">
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="apply_all_subjects" id="apply_all_subjects" value="1" {{ old('apply_all_subjects') ? 'checked' : '' }}>
                <label class="form-check-label" for="apply_all_subjects">
                    Apply to all subjects in this section/year level/SY/term
                </label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="apply_specific_subjects" id="apply_specific_subjects" value="1" {{ old('apply_specific_subjects') ? 'checked' : '' }}>
                <label class="form-check-label" for="apply_specific_subjects">
                    Apply to specific subjects only
                </label>
            </div>

            <div class="border rounded p-3 mb-4" id="subject_picker_edit" style="display:none;">
                <small class="text-muted d-block mb-2">Select one or more subjects:</small>
                @forelse($sectionSubjectSchedules as $schedule)
                @php
                $selectedValues = old('selected_schedule_ids', $selectedScheduleIds ?? []);
                $selectedStrings = array_map('strval', (array) $selectedValues);
                @endphp
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="selected_schedule_ids[]" id="edit_schedule_{{ $schedule->id }}" value="{{ $schedule->id }}" {{ in_array((string) $schedule->id, $selectedStrings, true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="edit_schedule_{{ $schedule->id }}">
                        {{ $schedule->subject?->code ?? 'N/A' }} - {{ $schedule->subject?->name ?? 'Unknown Subject' }}
                    </label>
                </div>
                @empty
                <small class="text-muted">No subjects found for this section/year level/SY/term.</small>
                @endforelse
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="{{ route('class-schedule-students.index', $classSchedule->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const allCheckbox = document.getElementById('apply_all_subjects');
        const specificCheckbox = document.getElementById('apply_specific_subjects');
        const picker = document.getElementById('subject_picker_edit');
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
    });

</script>

@endsection
