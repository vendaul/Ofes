@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-edit"></i> Edit Class Schedule</h1>
        <p>Modify the details of this class schedule</p>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-plus"></i> Schedule Details
        </div>
        <div class="card-body">
            <form action="{{ route('class-schedules.update', $classSchedule) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="area_code" class="form-label">Area</label>
                            <select name="area_code" id="area_code" class="form-control">
                                <option value="">Select Area</option>
                                @foreach($areaOptions as $areaCode => $areaName)
                                <option value="{{ $areaCode }}" {{ old('area_code', $classSchedule->area_code ?? $selectedArea ?? session('selected_area')) == $areaCode ? 'selected' : '' }}>
                                    {{ $areaName }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="college_id" class="form-label">College</label>
                            <select name="college_id" id="college_id" class="form-control">
                                <option value="">Select College</option>
                                @foreach($colleges as $college)
                                <option value="{{ $college->id }}" data-area-code="{{ $college->area_code }}" {{ (string) old('college_id', $classSchedule->college_id ?? $selectedCollege ?? session('selected_college')) === (string) $college->id ? 'selected' : '' }}>
                                    {{ $college->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-calendar-check"></i>
                    <strong>Active Period:</strong>
                    @if($activePeriod)
                    {{ $activePeriod->year }} - {{ $activePeriod->term }}
                    @else
                    Not set
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject (Course Code & Name) <span class="text-danger">*</span></label>
                            <input type="text" name="subject_name" id="subject_name" class="form-control" value="{{ $classSchedule->subject ? $classSchedule->subject->code . ' - ' . $classSchedule->subject->name : '' }}" placeholder="Type or select subject..." required list="subjects-list">
                            <datalist id="subjects-list">
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->code }} - {{ $subject->name }}">
                                    @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="section_name" class="form-label">Section <span class="text-danger">*</span></label>
                            <input type="text" name="section_name" id="section_name" class="form-control" value="{{ $classSchedule->section ? $classSchedule->section->name : '' }}" placeholder="Type or select section..." required list="sections-list">
                            <datalist id="sections-list">
                                @foreach($sections as $section)
                                <option value="{{ $section->name }}">
                                    @endforeach
                            </datalist>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('instructors.workload', $classSchedule->instructor_id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Workload
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Schedule
                    </button>
                </div>
            </form>
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
