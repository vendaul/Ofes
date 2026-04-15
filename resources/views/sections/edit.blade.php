@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Section</h1>
    <p>Update section information</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-layer-group"></i> Section Details
            </div>
            <div class="card-body">
                <form action="{{ route('sections.update', ['id' => $section->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tab" value="{{ $activeTab ?? request('tab', 'sections') }}">

                    <div class="form-group mb-3">
                        <label for="area_code" class="form-label">Area</label>
                        <select class="form-control @error('area_code') is-invalid @enderror" id="area_code" name="area_code" required>
                            <option value="">Select Area</option>
                            @foreach($areaOptions as $areaCode => $areaName)
                            <option value="{{ $areaCode }}" {{ old('area_code', $section->area_code ?? $selectedArea ?? session('selected_area')) == $areaCode ? 'selected' : '' }}>
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
                            <option value="{{ $college->id }}" data-area-code="{{ $college->area_code }}" {{ (string) old('college_id', $section->college_id ?? $selectedCollege ?? session('selected_college')) === (string) $college->id ? 'selected' : '' }}>
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
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $section->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="year" class="form-label">Year</label>
                        <input type="text" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year', $section->year) }}" required>
                        @error('year_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Section
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
                <i class="fas fa-info-circle"></i> Section Info
            </div>
            <div class="card-body">
                <p><strong>Section ID:</strong></p>
                <p class="text-muted">{{ $section->id }}</p>

                <p class="mt-3"><strong>Created:</strong></p>
                @if($section->created_at)
                <p class="text-muted">{{ $section->created_at->format('M d, Y') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif

                <p class="mt-3"><strong>Last Updated:</strong></p>
                @if($section->updated_at)
                <p class="text-muted">{{ $section->updated_at->format('M d, Y H:i') }}</p>
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
