@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Student</h1>
    <p>Update student information</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-user"></i> Student Details
            </div>
            <div class="card-body">
                <form action="{{ route('students.update', $student->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tab" value="{{ $activeTab ?? request('tab', 'students') }}">
                    @if(request()->filled('section_id'))
                    <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                    @endif

                    <div class="form-group mb-3">
                        <label for="area_code" class="form-label">Area</label>
                        <select class="form-control @error('area_code') is-invalid @enderror" id="area_code" name="area_code" required>
                            <option value="">Select Area</option>
                            @foreach($areaOptions as $areaCode => $areaName)
                            <option value="{{ $areaCode }}" {{ old('area_code', $selectedArea ?? session('selected_area') ?? $student->area_code) == $areaCode ? 'selected' : '' }}>
                                {{ $areaName }}
                            </option>
                            @endforeach
                        </select>
                        @error('area_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="college_code" class="form-label">College</label>
                        <select class="form-control @error('college_code') is-invalid @enderror" id="college_code" name="college_code" required>
                            <option value="">Select College</option>
                            @foreach($colleges as $college)
                            <option value="{{ $college->id }}" data-area-code="{{ $college->area_code }}" {{ (string) old('college_code', $selectedCollege ?? session('selected_college') ?? $student->college_code) === (string) $college->id ? 'selected' : '' }}>
                                {{ $college->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('college_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="student_number" class="form-label">Student Number</label>
                        <input type="text" class="form-control @error('sid') is-invalid @enderror" id="student_number" name="sid" value="{{ old('sid', $student->sid) }}" required>
                        @error('sid')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('fname') is-invalid @enderror" id="first_name" name="fname" value="{{ old('fname', $student->fname) }}" required>
                        @error('fname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('lname') is-invalid @enderror" id="last_name" name="lname" value="{{ old('lname', $student->lname) }}" required>
                        @error('lname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Student
                        </button>
                        @if(request()->filled('section_id'))
                        <a href="{{ route('sections.students', ['id' => request('section_id')]) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        @else
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-info-circle"></i> Student Info
            </div>
            <div class="card-body">
                <p><strong>Student ID:</strong></p>
                <p class="text-muted">{{ $student->sid }}</p>

                <p class="mt-3"><strong>Created:</strong></p>
                @if($student->created_at)
                <p class="text-muted">{{ $student->created_at->format('M d, Y') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif

                <p class="mt-3"><strong>Last Updated:</strong></p>
                @if($student->updated_at)
                <p class="text-muted">{{ $student->updated_at->format('M d, Y H:i') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('area_code');
        const collegeSelect = document.getElementById('college_code');

        function syncCollegeOptions() {
            const selectedArea = areaSelect.value;

            Array.from(collegeSelect.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                option.hidden = selectedArea !== '' && option.dataset.areaCode !== selectedArea;
            });

            const selectedOption = collegeSelect.options[collegeSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.areaCode && selectedOption.dataset.areaCode !== selectedArea) {
                collegeSelect.value = '';
            }
        }

        areaSelect.addEventListener('change', syncCollegeOptions);
        syncCollegeOptions();
    });

</script>

@endsection
