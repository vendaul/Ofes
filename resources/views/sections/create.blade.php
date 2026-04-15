@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-layer-group"></i> Add New Section</h1>
    <p>Create a new section</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form "></i> Section Information
            </div>
            <div class="card-body">
                <form action="{{ route('sections.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="{{ $activeTab ?? request('tab', 'sections') }}">

                    <div class="form-group mb-3">
                        <label for="area_code" class="form-label">Area</label>
                        <select class="form-control @error('area_code') is-invalid @enderror" id="area_code" name="area_code" required>
                            <option value="">Select Area</option>
                            @foreach($areaOptions as $areaCode => $areaName)
                            <option value="{{ $areaCode }}" {{ old('area_code', $selectedArea ?? session('selected_area')) == $areaCode ? 'selected' : '' }}>
                                {{ $areaName }}
                            </option>
                            @endforeach
                        </select>
                        @error('area_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="college_id" class="form-label">College</label>
                        <select class="form-control @error('college_id') is-invalid @enderror" id="college_id" name="college_id" required>
                            <option value="">Select College</option>
                            @foreach($colleges as $college)
                            <option value="{{ $college->id }}" data-area-code="{{ $college->area_code }}" {{ (string) old('college_id', $selectedCollege ?? session('selected_college')) === (string) $college->id ? 'selected' : '' }}>
                                {{ $college->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('college_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Section Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="e.g., Section A, BS-CS-2A" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-control @error('year') is-invalid @enderror" id="year" name="year" required>
                            <option value="">Select Year</option>
                            <option value="First" {{ old('year') === 'First' ? 'selected' : '' }}>First</option>
                            <option value="Second" {{ old('year') === 'Second' ? 'selected' : '' }}>Second</option>
                            <option value="Third" {{ old('year') === 'Third' ? 'selected' : '' }}>Third</option>
                            <option value="Fourth" {{ old('year') === 'Fourth' ? 'selected' : '' }}>Fourth</option>
                        </select>
                        @error('year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Section
                        </button>
                        <a href="{{ route('sections.index') }}" class="btn btn-secondary">
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
                    <li><i class="fas fa-check text-success"></i> <strong>Section Name</strong> should be descriptive</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>Year Level</strong> is typically 1-4</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Example: "BS-CS-2A" for 2nd year Computer Science</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('area_code');
        const collegeSelect = document.getElementById('college_id');

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
