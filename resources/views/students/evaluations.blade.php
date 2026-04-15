@extends('layouts.student')

@section('content')
<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-clipboard-list"></i> My Evaluations</h1>
        <p>View and complete pending evaluations for your instructors.</p>
    </div>
    <div class="card border-info" style="min-width:210px;">
        <div class="card-body py-2 px-3">
            <h6 class="card-title mb-1">Active Period</h6>
            <p class="card-text mb-0">{{ $activePeriod ? $activePeriod->year . ' / ' . $activePeriod->term : 'N/A' }}</p>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body">
                <h5 class="card-title">My Sections</h5>
                <p class="card-text mb-0">{{ $courseCode ?? $student->course_code ?? 'N/A' }} - {{ $yearLevel ?? $student->year_level ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body">
                <form method="GET" action="{{ route('student.evaluations') }}" class="row g-2">
                    <div class="col-9">
                        <label for="instructor_id" class="form-label">Choose Instructor</label>
                        <select class="form-select" name="instructor_id" onchange="this.form.submit()">
                            <option value="">All Instructors</option>
                            @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ isset($selectedInstructorId) && $selectedInstructorId == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->fname }} {{ $instructor->lname }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@if(!empty($evaluationStartDate) && !empty($evaluationEndDate))
<div class="mb-4 alert alert-info">
    <strong>Evaluation window:</strong>
    {{ \Illuminate\Support\Carbon::parse($evaluationStartDate)->format('Y-m-d') }} to {{ \Illuminate\Support\Carbon::parse($evaluationEndDate)->format('Y-m-d') }}.
    @if($evaluationFuture)
    <span class="badge bg-warning">Not yet open</span>
    @elseif($evaluationOpen)
    <span class="badge bg-success">Open</span>
    @elseif($evaluationExpired)
    <span class="badge bg-danger">Closed</span>
    @endif
</div>
@endif

@if($assignments->isEmpty())
<div class="alert alert-info text-center">
    <i class="fas fa-info-circle fa-2x mb-3"></i>
    <h5>No Instructors Found</h5>
    <p>There are currently no teaching assignments matching your section/course.</p>
</div>
@else
<div class="card mb-4">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-users"></i> My Instructors & Subjects
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Instructor</th>
                        <th>Section</th>
                        <th>Rating (1-100)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    @php
                    $studentRating100 = $assignment->student_evaluation_rating;
                    $rating100 = $studentRating100 !== null ? number_format($studentRating100, 2) : null;
                    @endphp
                    <tr>
                        <td>{{ $assignment->subject ? $assignment->subject->name : 'Unknown Subject' }}</td>
                        <td>{{ $assignment->instructor ? $assignment->instructor->fname . ' ' . $assignment->instructor->lname : 'N/A' }}</td>
                        <td>{{ $assignment->section ? $assignment->section->name : 'N/A' }}</td>
                        <td>{{ $rating100 ?? 'N/A' }}</td>
                        <td>
                            @if($assignment->evaluated)
                            <span class="badge bg-success">Evaluated</span>
                            @else
                            <span class="badge bg-warning text-dark">Pending Evaluation</span>
                            @endif
                        </td>
                        <td>
                            @if($assignment->evaluated)
                            <button class="btn btn-sm btn-secondary" disabled>Reviewed</button>
                            @else
                            @if($evaluationFuture)
                            <button class="btn btn-sm btn-warning" disabled>Not Open</button>
                            @elseif($evaluationExpired)
                            <button class="btn btn-sm btn-danger" disabled>Evaluation Closed</button>
                            @elseif($evaluationOpen)
                            <a href="{{ route('evaluate.show', $assignment->id) }}" class="btn btn-sm btn-success">Evaluate</a>
                            @else
                            <button class="btn btn-sm btn-secondary" disabled>Unavailable</button>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
