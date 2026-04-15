@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-user"></i> Student Details</h1>
    <p>View student information and details</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-id-card"></i> Student Information
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>#:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-primary text-white">{{ $student->id }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Student ID:</strong>
                    </div>
                    <div class="col-sm-9">
                        <code>{{ $student->sid }}</code>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Full Name:</strong>
                    </div>
                    <div class="col-sm-9">
                        <strong>{{ $student->fname }} {{ $student->lname }}</strong>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Section:</strong>
                    </div>
                    <div class="col-sm-9">
                        <?php
                            // Get section from student's class schedule enrollments
                            $section = null;
                            if ($student->classScheduleEnrollments->isNotEmpty()) {
                                $schedule = $student->classScheduleEnrollments->first()->classSchedule;
                                if ($schedule && $schedule->section) {
                                    $section = $schedule->section;
                                }
                            }
                        ?>
                        @if($section)
                        <span class="badge bg-success">{{ $section->name }}</span>
                        @elseif($student->course_code && $student->year_level)
                        <span class="badge bg-success">{{ $student->course_code }} - Year {{ $student->year_level }}</span>
                        @else
                        <span class="badge bg-secondary">Not Assigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Email:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $student->user ? $student->user->email : 'N/A' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Account Status:</strong>
                    </div>
                    <div class="col-sm-9">
                        @if($student->user)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-warning">No Account</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('students.edit', ['student' => $student->id, 'section_id' => request('section_id'), 'tab' => request('tab', 'students')]) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Student
                    </a>
                    @if(request()->filled('section_id'))
                    <a href="{{ route('sections.students', ['id' => request('section_id')]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    @else
                    <a href="javascript:history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Quick Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('students.create', request()->filled('section_id') ? ['section_id' => request('section_id')] : []) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-user-plus"></i> Add New Student
                    </a>
                    <a href="#" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> View Evaluations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
