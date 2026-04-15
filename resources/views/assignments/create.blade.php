@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-tasks"></i> Add Teaching Assignment</h1>
    <p>Create a new instructor teaching assignment</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form"></i> Assignment Information
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <strong>Errors found:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form action="{{ route('assignments.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="area_code" value="{{ old('area_code', $selectedAreaId ?? request('area_code') ?? '') }}">
                    <input type="hidden" name="college_id" value="{{ old('college_id', $selectedCollegeId ?? request('college_id') ?? '') }}">

                    <div class="form-group mb-3">
                        <label for="instructor_id" class="form-label">Instructor</label>
                        <select class="form-select @error('instructor_id') is-invalid @enderror" id="instructor_id" name="instructor_id" required>
                            <option value="">-- Select an Instructor --</option>
                            @foreach($instructors as $i)
                            <option value="{{ $i->id }}" @if(old('instructor_id', request('instructor_id'))==$i->id) selected @endif>
                                {{ $i->fname }} {{ $i->lname }}
                            </option>
                            @endforeach
                        </select>
                        @error('instructor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="program_name" class="form-label">Program</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control @error('program_id') is-invalid @enderror" id="program_name" name="program_name" placeholder="Search or type program" autocomplete="off" list="programs-list" value="{{ old('program_name', optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->course_program ? optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->course_program . ' (' . optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->code . ') - ' . optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->name : optional($programs->firstWhere('id', old('program_id', $selectedProgramId ?? '')))->name ?? '') }}">
                        </div>
                        <datalist id="programs-list">
                            @foreach($programs as $program)
                            <option value="{{ $program->course_program }} ({{ $program->code }}) - {{ $program->name }}" data-id="{{ $program->id }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" id="program_id" name="program_id" value="{{ old('program_id', $selectedProgramId ?? '') }}">
                    <input type="hidden" id="college_id" name="college_id" value="{{ old('college_id', $selectedCollegeId ?? request('college_id')) }}">
                        @error('program_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="curriculum_id" class="form-label">Curriculum</label>
                        <select class="form-select @error('curriculum_id') is-invalid @enderror" id="curriculum_id" name="curriculum_id">
                            <option value="">-- Select Curriculum --</option>
                            @foreach($curriculums as $curriculum)
                            <option value="{{ $curriculum->id }}" {{ old('curriculum_id', $selectedCurriculumId ?? '') == $curriculum->id ? 'selected' : '' }}>{{ $curriculum->code ?? $curriculum->name ?? $curriculum->id }}</option>
                            @endforeach
                        </select>
                        @error('curriculum_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="year_level" class="form-label">Year Level</label>
                        <select class="form-select @error('year_level') is-invalid @enderror" id="year_level" name="year_level" required>
                            <option value="">-- Select Year Level --</option>
                            @foreach($yearLevels as $level)
                            <option value="{{ $level }}" @if(old('year_level', $selectedYearLevel ?? '' )==$level) selected @endif>{{ $level }}</option>
                            @endforeach
                        </select>
                        @error('year_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="subject_name" class="form-label">Subject</label>
                        <small class="form-text text-muted mb-1">Showing subjects for selected year level and active period.</small>
                        <input type="text" class="form-control @error('subject_name') is-invalid @enderror" id="subject_name" name="subject_name" placeholder="Type or select subject..." required list="subjects-list">
                        <datalist id="subjects-list">
                            @foreach($subjects as $s)
                            @php
                                $primaryCurriculum = $s->curriculumSubjects->first()?->curriculum;
                                $programId = $primaryCurriculum?->course?->id ?? '';
                                $curriculumId = $primaryCurriculum?->id ?? '';
                            @endphp
                            <option value="{{ $s->code }} - {{ $s->name }}" data-year="{{ $s->subject_year ?? '' }}" data-program="{{ $programId }}" data-curriculum="{{ $curriculumId }}"></option>
                            @endforeach
                        </datalist>
                        <script id="subjects-json" type="application/json">
                            @json($subjectOptions)
                        </script>
                        @error('subject_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="section_name" class="form-label">Section</label>
                        <input type="text" class="form-control @error('section_name') is-invalid @enderror" id="section_name" name="section_name" placeholder="Type or select section..." required list="sections-list">
                        <datalist id="sections-list">
                            @foreach($sections as $sec)
                            <option value="{{ $sec->name }}">
                                @endforeach
                        </datalist>
                        @error('section_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @php $periodLocked = !empty($activePeriod); @endphp
                    <div class="form-group mb-3">
                        <label for="school_year" class="form-label">School Year
                            @if($periodLocked)
                            <span class="badge bg-info">Locked to active period</span>
                            @endif
                        </label>
                        <select class="form-select @error('school_year') is-invalid @enderror" id="school_year" name="school_year" required @if($periodLocked) disabled @endif>
                            <option value="">-- Select School Year --</option>
                            @foreach($schoolYears ?? [] as $year)
                            <option value="{{ $year }}" {{ old('school_year', $activePeriod->year ?? '') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        @if($periodLocked)
                        <input type="hidden" name="school_year" value="{{ $activePeriod->year }}">
                        @endif
                        @error('school_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="semester" class="form-label">Semester
                            @if($periodLocked)
                            <span class="badge bg-info">Locked to active period</span>
                            @endif
                        </label>
                        <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required @if($periodLocked) disabled @endif>
                            <option value="">-- Select Semester --</option>
                            @foreach($semesters ?? [] as $semester)
                            <option value="{{ $semester }}" {{ old('semester', $activePeriod->term ?? '') == $semester ? 'selected' : '' }}>{{ $semester }}</option>
                            @endforeach
                        </select>
                        @if($periodLocked)
                        <input type="hidden" name="semester" value="{{ $activePeriod->term }}">
                        @endif
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Assignment
                        </button>
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-lightbulb"></i> Tips
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> All fields are required</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Ensure instructor is available</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Use format: "2024-2025" for year</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Use: "First" or "Second" for semester</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const yearLevelSelect = document.getElementById('year_level');
        const subjectInput = document.getElementById('subject_name');
        const dataList = document.getElementById('subjects-list');
        const subjectsData = @json($subjectOptions);

        const programNameInput = document.getElementById('program_name');
        const programIdInput = document.getElementById('program_id');
        const curriculumSelect = document.getElementById('curriculum_id');

        const programsData = @json($programOptions);

        const allCurriculums = @json($curriculumOptions);

        const curriculumIndex = allCurriculums.reduce((acc, c) => {
            acc[c.id] = c;
            return acc;
        }, {});

        function normalizeString(v) {
            return (v || '').toString().trim().toLowerCase();
        }

        function syncProgramIdFromName() {
            const typed = normalizeString(programNameInput.value);

            // exact label match first
            let selected = programsData.find(p => normalizeString(p.label) === typed);

            // fallback prefix match (typing "BSIT" should match "BSIT (...) - ...")
            if (!selected && typed.length > 0) {
                selected = programsData.find(p => normalizeString(p.label).startsWith(typed));
            }

            if (!selected && curriculumSelect && curriculumSelect.value) {
                const curriculum = curriculumIndex[curriculumSelect.value];
                if (curriculum && curriculum.course_id) {
                    selected = programsData.find(p => String(p.id) === String(curriculum.course_id));
                }
            }

            programIdInput.value = selected ? selected.id : '';

            if (selected) {
                programNameInput.value = selected.label;
            }
        }

        function updateCurriculumOptions() {
            if (!curriculumSelect) {
                return;
            }

            const selectedProgram = programIdInput.value;
            const selectedCurriculum = curriculumSelect.value;
            curriculumSelect.innerHTML = '<option value="">-- Select Curriculum --</option>';

            const filtered = selectedProgram
                ? allCurriculums.filter(c => String(c.course_id) === String(selectedProgram))
                : allCurriculums;

            filtered.forEach(c => {
                const option = document.createElement('option');
                option.value = c.id;
                option.textContent = c.label;
                curriculumSelect.appendChild(option);
            });

            if (selectedProgram && selectedCurriculum && !filtered.some(c => String(c.id) === String(selectedCurriculum))) {
                curriculumSelect.value = '';
            }
        }

        function renderSubjectOptions() {
            const selectedYear = yearLevelSelect.value;
            const selectedProgram = programIdInput ? programIdInput.value : '';
            const selectedCurriculum = curriculumSelect ? curriculumSelect.value : '';
            dataList.innerHTML = '';

            subjectsData.forEach(item => {
                const yearMatch = !selectedYear || String(item.year) === String(selectedYear);
                const programMatch = !selectedProgram || String(item.program) === String(selectedProgram);
                const curriculumMatch = !selectedCurriculum || String(item.curriculum) === String(selectedCurriculum);

                if (yearMatch && programMatch && curriculumMatch) {
                    const option = document.createElement('option');
                    option.value = item.value;
                    option.dataset.year = item.year;
                    option.dataset.program = item.program;
                    option.dataset.curriculum = item.curriculum;
                    dataList.appendChild(option);
                }
            });

            if (subjectInput.value) {
                const match = subjectsData.some(item => item.value === subjectInput.value &&
                    (!selectedYear || item.year === selectedYear) &&
                    (!selectedProgram || item.program === selectedProgram) &&
                    (!selectedCurriculum || item.curriculum === selectedCurriculum)
                );
                if (!match) {
                    subjectInput.value = '';
                }
            }
        }

        yearLevelSelect.addEventListener('change', renderSubjectOptions);
        if (programNameInput) {
            programNameInput.addEventListener('change', function () {
                syncProgramIdFromName();
                updateCurriculumOptions();
                renderSubjectOptions();
            });
            programNameInput.addEventListener('input', function () {
                if (!programNameInput.value.trim()) {
                    programIdInput.value = '';
                    updateCurriculumOptions();
                    renderSubjectOptions();
                }
            });
        }
        if (curriculumSelect) curriculumSelect.addEventListener('change', renderSubjectOptions);

        // Make sure program/curriculum sync gets triggered once on load.
        if (programNameInput) {
            syncProgramIdFromName();
            updateCurriculumOptions();
        }
        renderSubjectOptions();
    });

</script>

@endsection
