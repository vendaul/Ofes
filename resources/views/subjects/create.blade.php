@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-book"></i> Add New Subject</h1>
    <p>Create a new subject</p>
</div>

@if(!($selectedArea ?? session('selected_area')) || !($selectedCollege ?? session('selected_college')))
<div class="alert alert-warning d-flex align-items-center gap-2">
    <i class="fas fa-exclamation-triangle fa-lg"></i>
    <div>
        <strong>Area and College are required.</strong>
        Please set them below before saving this subject.
    </div>
</div>
@endif
@php
$defaultProgramId = old('program_id', $activeProgramId ?? null);
$defaultCurriculumId = old('curriculum_id', $activeCurriculumId ?? null);

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
            <div class="card mb-3">
                <div class="card-header text-white bg-primary">Program / Curriculum</div>
                <div class="card-body">
                    <form action="{{ route('subjects.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tab" value="{{ $activeTab ?? request('tab', 'subjects') }}">

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="area_code" class="form-label">Area</label>
                                <select id="area_code" name="area_code" class="form-select @error('area_code') is-invalid @enderror" required>
                                    <option value="">Select Area</option>
                                    @foreach($areaOptions as $areaCode => $areaName)
                                    <option value="{{ $areaCode }}" {{ (string) old('area_code', $selectedArea ?? session('selected_area')) === (string) $areaCode ? 'selected' : '' }}>{{ $areaName }}</option>
                                    @endforeach
                                </select>
                                @error('area_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="college_id" class="form-label">College</label>
                                <select id="college_id" name="college_id" class="form-select @error('college_id') is-invalid @enderror" required>
                                    <option value="">Select College</option>
                                    @foreach($colleges as $college)
                                    <option value="{{ $college->id }}" data-area-code="{{ $college->area_code }}" {{ (string) old('college_id', $selectedCollege ?? session('selected_college')) === (string) $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                                    @endforeach
                                </select>
                                @error('college_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-md-8">
                                <label for="program_combo" class="form-label">Program</label>
                                <div class="input-group mb-2">
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
                            <div class="col-md-4 align-self-end">
                                <button type="button" class="btn btn-outline-success" id="toggle-new-program" data-bs-toggle="modal" data-bs-target="#new-program-modal">+ New Program</button>
                            </div>
                        </div>

                        <div class="modal fade" id="new-program-modal" tabindex="-1" aria-labelledby="newProgramModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newProgramModalLabel">Add New Program</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="new_program_code_modal" class="form-label">Program Code</label>
                                            <input type="text" class="form-control @error('new_program_code') is-invalid @enderror" id="new_program_code_modal" name="new_program_code" placeholder="Program code" value="{{ old('new_program_code') }}">
                                            @error('new_program_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_program_name_modal" class="form-label">Program Description</label>
                                            <input type="text" class="form-control @error('new_program_name') is-invalid @enderror" id="new_program_name_modal" name="new_program_name" placeholder="Program description " value="{{ old('new_program_name') }}">
                                            @error('new_program_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="cancel-new-program" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success" id="save-new-program">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-md-8">
                                <label for="curriculum_combo" class="form-label">Curriculum</label>
                                <div class="input-group mb-2">
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
                            <div class="col-md-4 align-self-end">
                                <button type="button" class="btn btn-outline-success" id="toggle-new-curriculum">+ New Curriculum</button>
                            </div>
                        </div>

                        <div class="row g-2 mt-2 d-none" id="new-curriculum-row">
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('new_curriculum_code') is-invalid @enderror" id="new_curriculum_code_modal" name="new_curriculum_code" placeholder="Curriculum code" value="{{ old('new_curriculum_code') }}">
                                @error('new_curriculum_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control @error('new_curriculum_desc') is-invalid @enderror" id="new_curriculum_desc_modal" name="new_curriculum_desc" placeholder="Description" value="{{ old('new_curriculum_desc') }}">
                                @error('new_curriculum_desc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12 mt-2 d-flex gap-1">
                                <button type="button" class="btn btn-success" id="save-new-curriculum">Save</button>
                                <button type="button" class="btn btn-secondary" id="cancel-new-curriculum">Cancel</button>
                            </div>
                        </div>

                        <div class="card-header text-white bg-primary">
                            <i class="fas fa-form"></i> Subject Information
                        </div>
                        <div class="card-body">

                            <div class="form-group mb-3">
                                <label for="code" class="form-label">Subject Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" placeholder="e.g., CS101, MATH201" required>
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="name" class="form-label">Subject Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="e.g., Introduction to Computer Science" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="subject_year" class="form-label">Year Level</label>
                                <select class="form-select @error('subject_year') is-invalid @enderror" id="subject_year" name="subject_year">
                                    <option value="" disabled {{ old('subject_year') ? '' : 'selected' }}>Select year</option>
                                    <option value="First" {{ old('subject_year') == 'First' ? 'selected' : '' }}>First</option>
                                    <option value="Second" {{ old('subject_year') == 'Second' ? 'selected' : '' }}>Second</option>
                                    <option value="Third" {{ old('subject_year') == 'Third' ? 'selected' : '' }}>Third</option>
                                    <option value="Fourth" {{ old('subject_year') == 'Fourth' ? 'selected' : '' }}>Fourth</option>
                                </select>
                                @error('subject_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="subject_term" class="form-label">Semester / Term</label>
                                <select class="form-select @error('subject_term') is-invalid @enderror" id="subject_term" name="subject_term">
                                    <option value="" disabled {{ old('subject_term') ? '' : 'selected' }}>Select term</option>
                                    <option value="First" {{ old('subject_term') == 'First' ? 'selected' : '' }}>First</option>
                                    <option value="Second" {{ old('subject_term') == 'Second' ? 'selected' : '' }}>Second</option>
                                    <option value="Third" {{ old('subject_term') == 'Third' ? 'selected' : '' }}>Third</option>
                                </select>
                                @error('subject_term')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Subject
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

    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-lightbulb"></i> Tips
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> <strong>Subject Code</strong> should be unique</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Use standard codes (e.g., CS101)</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>Subject Name</strong> should be descriptive</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggleProgram = document.getElementById('toggle-new-program');
        var newProgramRow = document.getElementById('new-program-row');
        var toggleCurriculum = document.getElementById('toggle-new-curriculum');
        var newCurriculumRow = document.getElementById('new-curriculum-row');

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

            if (!filtered.length) {
                curriculumCombo.value = '';
                curriculumId.value = '';
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

        if (toggleProgram) {
            toggleProgram.addEventListener('click', function() {
                // Bootstrap modal opens. Do not clear program_id here so existing/new program persists during curriculum creation.
                if (!programCombo.value && programId.value) {
                    var existingOption = programOptions.find(function(opt) {
                        return opt.dataset.id === programId.value;
                    });
                    if (existingOption) {
                        programCombo.value = existingOption.value;
                    }
                }
                // keep current program selected for curriculum filtering
                renderCurriculumsForProgram(programId.value);
            });
        }

        if (toggleCurriculum) {
            toggleCurriculum.addEventListener('click', function() {
                newCurriculumRow.classList.toggle('d-none');
                if (!newCurriculumRow.classList.contains('d-none')) {
                    curriculumCombo.value = '';
                    curriculumId.value = '';
                }
            });
        }

        if (programCombo && programId) {
            programCombo.addEventListener('input', function() {
                var selectedProgramId = findProgramIdByText(this.value);
                programId.value = selectedProgramId;

                renderCurriculumsForProgram(selectedProgramId);

                if (!selectedProgramId) {
                    curriculumCombo.value = '';
                    curriculumId.value = '';
                }
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

        // New program save/cancel handling with modal
        var saveNewProgram = document.getElementById('save-new-program');
        var cancelNewProgram = document.getElementById('cancel-new-program');
        var newProgramCode = document.getElementById('new_program_code_modal');
        var newProgramName = document.getElementById('new_program_name_modal');
        var newProgramModalEl = document.getElementById('new-program-modal');
        var newProgramModal = newProgramModalEl ? new bootstrap.Modal(newProgramModalEl) : null;

        // New curriculum inline row handling
        var saveNewCurriculum = document.getElementById('save-new-curriculum');
        var cancelNewCurriculum = document.getElementById('cancel-new-curriculum');
        var newCurriculumCode = document.getElementById('new_curriculum_code_modal');
        var newCurriculumDesc = document.getElementById('new_curriculum_desc_modal');
        var newCurriculumRow = document.getElementById('new-curriculum-row');

        if (saveNewProgram) {
            saveNewProgram.addEventListener('click', function() {
                if (!newProgramCode || !newProgramName) return;
                if (!newProgramCode.value.trim() || !newProgramName.value.trim()) {
                    alert('Program code and description are required.');
                    return;
                }

                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route("programs.ajaxCreate") }}', {
                    method: 'POST'
                    , headers: {
                        'Content-Type': 'application/json'
                        , 'X-CSRF-TOKEN': token
                        , 'Accept': 'application/json'
                    }
                    , body: JSON.stringify({
                        new_program_code: newProgramCode.value.trim()
                        , new_program_name: newProgramName.value.trim()
                    })
                }).then(function(response) {
                    if (!response.ok) {
                        return response.json().then(function(err) {
                            throw new Error(err.message || 'Error saving program');
                        });
                    }
                    return response.json();
                }).then(function(data) {
                    programCombo.value = data.course_program + ' (' + data.code + ') - ' + data.name;
                    programId.value = data.id;

                    if (programDatalist) {
                        var newOption = document.createElement('option');
                        newOption.value = programCombo.value;
                        newOption.dataset.id = data.id;
                        programDatalist.appendChild(newOption);
                        programOptions.push(newOption);
                    }

                    if (newProgramModal) {
                        newProgramModal.hide();
                    }
                }).catch(function(err) {
                    alert('Failed to save program: ' + err.message);
                });
            });
        }

        if (cancelNewProgram) {
            cancelNewProgram.addEventListener('click', function() {
                if (newProgramCode) newProgramCode.value = '';
                if (newProgramName) newProgramName.value = '';
            });
        }

        if (newProgramModalEl) {
            newProgramModalEl.addEventListener('hidden.bs.modal', function() {
                if (newProgramCode) newProgramCode.value = '';
                if (newProgramName) newProgramName.value = '';
            });
        }

        if (saveNewCurriculum) {
            saveNewCurriculum.addEventListener('click', function() {
                var selectedProgramId = programId.value || findProgramIdByText(programCombo.value);
                if (!selectedProgramId) {
                    alert('Program is required before creating a curriculum.');
                    return;
                }

                if (!newCurriculumCode || !newCurriculumDesc) return;
                if (!newCurriculumCode.value.trim() || !newCurriculumDesc.value.trim()) {
                    alert('Curriculum code and description are required.');
                    return;
                }

                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route("curriculums.ajaxCreate") }}', {
                    method: 'POST'
                    , headers: {
                        'Content-Type': 'application/json'
                        , 'X-CSRF-TOKEN': token
                        , 'Accept': 'application/json'
                    }
                    , body: JSON.stringify({
                        new_curriculum_code: newCurriculumCode.value.trim()
                        , new_curriculum_desc: newCurriculumDesc.value.trim()
                        , program_id: selectedProgramId
                    })
                }).then(function(response) {
                    if (!response.ok) {
                        return response.json().then(function(err) {
                            throw new Error((err.message || 'Error saving curriculum'));
                        });
                    }
                    return response.json();
                }).then(function(data) {
                    curriculumCombo.value = data.code + ' - ' + data.desc + (programCombo.value ? ' (' + programCombo.value + ')' : '');
                    curriculumId.value = data.id;

                    if (newCurriculumRow) {
                        newCurriculumRow.classList.add('d-none');
                    }

                    // Add new option to datalist immediately
                    if (curriculumDatalist) {
                        var newOption = document.createElement('option');
                        newOption.value = curriculumCombo.value;
                        newOption.dataset.id = data.id;
                        newOption.dataset.courseId = data.course_id;
                        curriculumDatalist.appendChild(newOption);
                    }

                    if (newCurriculumCode) newCurriculumCode.value = '';
                    if (newCurriculumDesc) newCurriculumDesc.value = '';
                }).catch(function(err) {
                    alert('Failed to save curriculum: ' + err.message);
                });
            });
        }

        if (cancelNewCurriculum) {
            cancelNewCurriculum.addEventListener('click', function() {
                if (newCurriculumCode) newCurriculumCode.value = '';
                if (newCurriculumDesc) newCurriculumDesc.value = '';
                if (newCurriculumRow) {
                    newCurriculumRow.classList.add('d-none');
                }
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

@endsection
