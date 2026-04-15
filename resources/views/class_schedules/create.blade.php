@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-plus"></i> Create Class Schedule</h1>
        <p>Add a new class schedule to the system</p>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-plus"></i> Schedule Details
        </div>
        <div class="card-body">
            <form action="{{ route('class-schedules.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="area_code" class="form-label">Area</label>
                            <select name="area_code" id="area_code" class="form-control" required>
                                <option value="">Select Area</option>
                                @foreach($areaOptions as $areaCode => $areaName)
                                <option value="{{ $areaCode }}" {{ old('area_code', $selectedArea ?? session('selected_area')) == $areaCode ? 'selected' : '' }}>
                                    {{ $areaName }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="college_id" class="form-label">College</label>
                            <select name="college_id" id="college_id" class="form-control" required>
                                <option value="">Select College</option>
                                @foreach($colleges as $college)
                                <option value="{{ $college->id }}" data-area-code="{{ $college->area_code }}" {{ (string) old('college_id', $selectedCollege ?? session('selected_college')) === (string) $college->id ? 'selected' : '' }}>
                                    {{ $college->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subject_name" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject_name" id="subject_name" class="form-control" placeholder="Type or select subject..." required list="subjects-list">
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
                            <input type="text" name="section_name" id="section_name" class="form-control" placeholder="Type or select section..." required list="sections-list">
                            <datalist id="sections-list">
                                @foreach($sections as $section)
                                <option value="{{ $section->name }}">
                                    @endforeach
                            </datalist>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="instructor_id" class="form-label">Instructor <span class="text-danger">*</span></label>
                            <select name="instructor_id" id="instructor_id" class="form-control" required>
                                <option value="">Select Instructor</option>
                                @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}">{{ $instructor->fname }} {{ $instructor->lname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="schedule_code" class="form-label">Schedule Code <span class="text-danger">*</span></label>
                            <input type="text" name="schedule_code" id="schedule_code" class="form-control" required>
                        </div>
                    </div>
                </div>

                @php $periodLocked = !empty($activePeriod); @endphp
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="ay" class="form-label">Academic Year
                                @if($periodLocked)
                                <span class="badge bg-info">Locked to active period</span>
                                @endif
                            </label>
                            <input type="text" name="ay" id="ay" class="form-control" placeholder="e.g., 2023-2024" value="{{ old('ay', $activePeriod->year ?? '') }}" @if($periodLocked) readonly @endif>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="term" class="form-label">Term
                                @if($periodLocked)
                                <span class="badge bg-info">Locked to active period</span>
                                @endif
                            </label>
                            <input type="text" name="term" id="term" class="form-control" placeholder="e.g., 1st Semester" value="{{ old('term', $activePeriod->term ?? '') }}" @if($periodLocked) readonly @endif>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="year_level" class="form-label">Year Level</label>
                            <input type="text" name="year_level" id="year_level" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="class_size" class="form-label">Class Size</label>
                            <input type="number" name="class_size" id="class_size" class="form-control">
                        </div>
                    </div>
                </div>

                <h5 class="mt-4">Lecture Schedule</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lec_week_day" class="form-label">Day</label>
                            <select name="lec_week_day" id="lec_week_day" class="form-control">
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lec_start_time" class="form-label">Start Time</label>
                            <input type="time" name="lec_start_time" id="lec_start_time" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lec_end_time" class="form-label">End Time</label>
                            <input type="time" name="lec_end_time" id="lec_end_time" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lec_room_id" class="form-label">Room</label>
                            <input type="text" name="lec_room_id" id="lec_room_id" class="form-control" placeholder="Room number">
                        </div>
                    </div>
                </div>

                <h5 class="mt-4">Laboratory Schedule</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lab_week_day" class="form-label">Day</label>
                            <select name="lab_week_day" id="lab_week_day" class="form-control">
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lab_start_time" class="form-label">Start Time</label>
                            <input type="time" name="lab_start_time" id="lab_start_time" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lab_end_time" class="form-label">End Time</label>
                            <input type="time" name="lab_end_time" id="lab_end_time" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="lab_room_id" class="form-label">Room</label>
                            <input type="text" name="lab_room_id" id="lab_room_id" class="form-control" placeholder="Room number">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('class-schedules.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Schedule
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
