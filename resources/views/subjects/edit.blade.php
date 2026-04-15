@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Subject</h1>
    <p>Update subject information and curriculum mapping</p>
</div>

@php
$mappedRow = $subject->curriculumSubjects->first();
$defaultProgramId = old('program_id', $currentProgramId ?? null);
$defaultCurriculumId = old('curriculum_id', $currentCurriculumId ?? null);

$defaultProgram = collect($programs)->firstWhere('id', (int) $defaultProgramId);
$defaultCurriculum = collect($curriculums)->firstWhere('id', (int) $defaultCurriculumId);

$defaultProgramQuery = old('program_query', $defaultProgram
? (($defaultProgram->course_program ?: $defaultProgram->code) . ' (' . ($defaultProgram->code ?: '-') . ') - ' . ($defaultProgram->name ?: 'N/A'))
: '');

$defaultCurriculumQuery = old('curriculum_query', $defaultCurriculum
? ($defaultCurriculum->code . ' - ' . $defaultCurriculum->desc . ($defaultCurriculum->course ? ' (' . $defaultCurriculum->course->course_program . ')' : ''))
: '');
@endphp

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-book"></i> Subject Details
            </div>
            <div class="card-body">
                <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tab" value="{{ $activeTab ?? request('tab', 'subjects') }}">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="area_code" class="form-label">Area</label>
                            <select id="area_code" name="area_code" class="form-select @error('area_code') is-invalid @enderror">
                                <option value="">Select Area</option>
                                @foreach($areaOptions as $code => $name)
                                <option value="{{ $code }}" {{ (string) old('area_code', $selectedArea ?? $subject->area_code) === (string) $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('area_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="college_id" class="form-label">College</label>
                            <select id="college_id" name="college_id" class="form-select @error('college_id') is-invalid @enderror">
                                <option value="">Select College</option>
                                @foreach($collegeOptions as $id => $name)
                                <option value="{{ $id }}" {{ (string) old('college_id', $selectedCollege ?? $subject->college_id) === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('college_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header text-white bg-primary">Program / Curriculum</div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label for="program_combo" class="form-label">Program</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" id="program_combo" class="form-control" placeholder="Search or type program" autocomplete="off" list="program_datalist" value="{{ $defaultProgramQuery }}">
                                    </div>
                                    <input type="hidden" id="program_id" name="program_id" value="{{ $defaultProgramId }}">
                                    <datalist id="program_datalist">
                                        @foreach($programs as $program)
                                        <option value="{{ $program->course_program }} ({{ $program->code }}) - {{ $program->name }}" data-id="{{ $program->id }}"></option>
                                        @endforeach
                                    </datalist>
                                    @error('program_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="curriculum_combo" class="form-label">Curriculum</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" id="curriculum_combo" class="form-control" placeholder="Search or type curriculum" autocomplete="off" list="curriculum_datalist" value="{{ $defaultCurriculumQuery }}">
                                    </div>
                                    <input type="hidden" id="curriculum_id" name="curriculum_id" value="{{ $defaultCurriculumId }}">
                                    <datalist id="curriculum_datalist">
                                        @foreach($curriculums as $curriculum)
                                        <option value="{{ $curriculum->code }} - {{ $curriculum->desc }}@if($curriculum->course) ({{ $curriculum->course->course_program }})@endif" data-id="{{ $curriculum->id }}" data-course-id="{{ $curriculum->course_id }}"></option>
                                        @endforeach
                                    </datalist>
                                    @error('curriculum_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="subject_year" class="form-label">Year Level</label>
                                    <select class="form-select @error('subject_year') is-invalid @enderror" id="subject_year" name="subject_year">
                                        <option value="" {{ old('subject_year', $mappedRow->s_year ?? '') === '' ? 'selected' : '' }}>Select year</option>
                                        <option value="First" {{ old('subject_year', $mappedRow->s_year ?? '') == 'First' ? 'selected' : '' }}>First</option>
                                        <option value="Second" {{ old('subject_year', $mappedRow->s_year ?? '') == 'Second' ? 'selected' : '' }}>Second</option>
                                        <option value="Third" {{ old('subject_year', $mappedRow->s_year ?? '') == 'Third' ? 'selected' : '' }}>Third</option>
                                        <option value="Fourth" {{ old('subject_year', $mappedRow->s_year ?? '') == 'Fourth' ? 'selected' : '' }}>Fourth</option>
                                    </select>
                                    @error('subject_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="subject_term" class="form-label">Semester / Term</label>
                                    <select class="form-select @error('subject_term') is-invalid @enderror" id="subject_term" name="subject_term">
                                        <option value="" {{ old('subject_term', $mappedRow->s_term ?? '') === '' ? 'selected' : '' }}>Select term</option>
                                        <option value="First" {{ old('subject_term', $mappedRow->s_term ?? '') == 'First' ? 'selected' : '' }}>First</option>
                                        <option value="Second" {{ old('subject_term', $mappedRow->s_term ?? '') == 'Second' ? 'selected' : '' }}>Second</option>
                                        <option value="Third" {{ old('subject_term', $mappedRow->s_term ?? '') == 'Third' ? 'selected' : '' }}>Third</option>
                                    </select>
                                    @error('subject_term')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header text-white bg-primary">
                            <i class="fas fa-form"></i> Subject Information
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Subject Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $subject->code) }}" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="name" class="form-label">Subject Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subject->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Subject
                        </button>
                        <a href="{{ route('subjects.index', ['tab' => $activeTab ?? request('tab', 'subjects')]) }}#{{ $activeTab ?? request('tab', 'subjects') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<div class="col-md-4">
    <div class="card">
        <div class="card-header text-white bg-primary">
            <i class="fas fa-info-circle"></i> Subject Info
        </div>
        <div class="card-body">
            <p><strong>Subject ID:</strong></p>
            <p class="text-muted">{{ $subject->subject_id }}</p>

            <p class="mt-3"><strong>Created:</strong></p>
            @if($subject->created_at)
            <p class="text-muted">{{ $subject->created_at->format('M d, Y') }}</p>
            @else
            <p class="text-muted">&mdash; not available</p>
            @endif

            <p class="mt-3"><strong>Last Updated:</strong></p>
            @if($subject->updated_at)
            <p class="text-muted">{{ $subject->updated_at->format('M d, Y H:i') }}</p>
            @else
            <p class="text-muted">&mdash; not available</p>
            @endif
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var programCombo = document.getElementById('program_combo');
        var programDatalist = document.getElementById('program_datalist');
        var programId = document.getElementById('program_id');
        var curriculumCombo = document.getElementById('curriculum_combo');
        var curriculumDatalist = document.getElementById('curriculum_datalist');
        var curriculumId = document.getElementById('curriculum_id');
        var areaSelect = document.getElementById('area_code');
        var collegeSelect = document.getElementById('college_id');

        var programOptions = programDatalist ? Array.from(programDatalist.options) : [];
        var curriculumEntries = curriculumDatalist ? Array.from(curriculumDatalist.options).map(function(opt) {
            return {
                id: opt.dataset.id || ''
                , courseId: opt.dataset.courseId || ''
                , value: opt.value || ''
            };
        }) : [];

        function renderCurriculumsForProgram(programIdValue) {
            if (!curriculumDatalist) return;
            curriculumDatalist.innerHTML = '';

            var filtered = !programIdValue ? curriculumEntries : curriculumEntries.filter(function(entry) {
                return entry.courseId === String(programIdValue);
            });

            filtered.forEach(function(entry) {
                var option = document.createElement('option');
                option.value = entry.value;
                option.dataset.id = entry.id;
                option.dataset.courseId = entry.courseId;
                curriculumDatalist.appendChild(option);
            });

            var currentCurriculumId = curriculumId ? curriculumId.value : '';
            if (currentCurriculumId) {
                var stillValid = filtered.some(function(entry) {
                    return entry.id === String(currentCurriculumId);
                });

                if (!stillValid) {
                    if (curriculumCombo) curriculumCombo.value = '';
                    if (curriculumId) curriculumId.value = '';
                }
            }
        }

        function findProgramIdByText(text) {
            if (!text) return '';
            var normalized = text.trim().toLowerCase();
            var exact = programOptions.find(function(opt) {
                return opt.value.trim().toLowerCase() === normalized;
            });
            if (exact) return exact.dataset.id || '';

            var candidates = programOptions.filter(function(opt) {
                return opt.value.trim().toLowerCase().indexOf(normalized) === 0;
            });
            if (candidates.length === 1) return candidates[0].dataset.id || '';
            return '';
        }

        function syncCollegeOptions() {
            if (!areaSelect || !collegeSelect) return;

            var selectedArea = areaSelect.value || '';

            Array.from(collegeSelect.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                var collegeArea = option.dataset.areaCode || '';
                option.hidden = selectedArea !== '' && collegeArea !== selectedArea;
            });

            var selectedOption = collegeSelect.options[collegeSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.areaCode && selectedOption.dataset.areaCode !== selectedArea) {
                collegeSelect.value = '';
            }
        }

        if (programCombo && programId) {
            programCombo.addEventListener('input', function() {
                var selectedProgramId = findProgramIdByText(this.value);
                programId.value = selectedProgramId;
                renderCurriculumsForProgram(selectedProgramId);
            });
        }

        if (curriculumCombo && curriculumId && curriculumDatalist) {
            curriculumCombo.addEventListener('input', function() {
                var normalized = this.value.trim().toLowerCase();
                var found = Array.from(curriculumDatalist.options).find(function(opt) {
                    return opt.value.trim().toLowerCase() === normalized;
                });
                curriculumId.value = found ? (found.dataset.id || '') : '';
            });
        }

        if (programId && programId.value) {
            renderCurriculumsForProgram(programId.value);
        }

        if (areaSelect) {
            areaSelect.addEventListener('change', syncCollegeOptions);
            syncCollegeOptions();
        }
    });

</script>
@endpush
