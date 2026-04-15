@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-eye"></i> Class Schedule Details</h1>
        <p>View detailed information about this class schedule</p>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-calendar-alt"></i> Schedule Information
            <div class="float-end">
                <a href="{{ route('class-schedules.edit', $classSchedule) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('class-schedules.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Schedule Code:</strong></td>
                            <td>{{ $classSchedule->schedule_code }}</td>
                        </tr>
                        <tr>
                            <td><strong>Subject:</strong></td>
                            <td>{{ $classSchedule->subject ? $classSchedule->subject->code . ' - ' . $classSchedule->subject->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Section:</strong></td>
                            <td>{{ $classSchedule->section ? $classSchedule->section->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Instructor:</strong></td>
                            <td>{{ $classSchedule->instructor ? $classSchedule->instructor->fname . ' ' . $classSchedule->instructor->lname : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Academic Year:</strong></td>
                            <td>{{ $classSchedule->ay }}</td>
                        </tr>
                        <tr>
                            <td><strong>Term:</strong></td>
                            <td>{{ $classSchedule->term }}</td>
                        </tr>
                        <tr>
                            <td><strong>Year Level:</strong></td>
                            <td>{{ $classSchedule->year_level }}</td>
                        </tr>
                        <tr>
                            <td><strong>Class Size:</strong></td>
                            <td>{{ $classSchedule->class_size }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Lecture Schedule</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Day:</strong></td>
                            <td>{{ $classSchedule->lec_week_day ?: 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Time:</strong></td>
                            <td>{{ $classSchedule->lec_start_time ? $classSchedule->lec_start_time . ' - ' . $classSchedule->lec_end_time : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Room:</strong></td>
                            <td>{{ $classSchedule->lec_room_id ?: 'Not set' }}</td>
                        </tr>
                    </table>

                    <h5 class="mt-4">Laboratory Schedule</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Day:</strong></td>
                            <td>{{ $classSchedule->lab_week_day ?: 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Time:</strong></td>
                            <td>{{ $classSchedule->lab_start_time ? $classSchedule->lab_start_time . ' - ' . $classSchedule->lab_end_time : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Room:</strong></td>
                            <td>{{ $classSchedule->lab_room_id ?: 'Not set' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($classSchedule->students->count() > 0)
            <div class="mt-4">
                <h5>Enrolled Students ({{ $classSchedule->students->count() }})</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classSchedule->students as $student)
                            <tr>
                                <td>{{ $student->sid }}</td>
                                <td>{{ $student->fname }} {{ $student->lname }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="mt-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No students enrolled in this class yet.
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
