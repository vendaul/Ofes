@extends('layouts.faculty')

@section('content')

<div class="page-header d-flex justify-content-between align-items-start">
    <div>
        <h1><i class="fas fa-chalkboard-teacher"></i> Instructor Dashboard</h1>
        <p>Welcome back, {{ $instructor->fname }} {{ $instructor->lname }}!</p>
    </div>
    <div class="text-end">
        <div class="badge bg-info text-dark py-2 px-3">
            <strong>Active Period:</strong><br>
            {{ $activePeriod ? $activePeriod->year . ' / ' . $activePeriod->term : 'N/A' }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                <h4 class="card-title">My Workload</h4>
                <p class="card-text">View your teaching assignments, subjects, and sections</p>
                <a href="{{ route('instructor.workload') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-eye"></i> View Workload
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-chart-bar fa-3x text-success mb-3"></i>
                <h4 class="card-title">Evaluation Reports</h4>
                <p class="card-text">View summary reports of your teaching evaluations</p>
                <a href="{{ route('instructor.reports') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-chart-line"></i> View Reports
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">


    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-info-circle"></i> Quick Stats
            </div>
            <div class="card-body">
                <div class="row text-center">

                    <div class="col-md-4">
                        <h3 class="text-success">{{ $workloadCount }}</h3>
                        <p class="mb-0">Total Workload </p>
                    </div>

                    <div class="col-md-4">
                        <h3 class="text-info">
                            @php
                            $evaluatedAssignments = $assignmentStats->filter(fn($item) => !is_null($item['average_rating']));
                            $avgRating = $evaluatedAssignments->count() ? $evaluatedAssignments->avg('average_rating') : null;
                            $scaledRating = $avgRating !== null ? round(min(max($avgRating * 20, 0), 100), 1) : null;
                            @endphp
                            {{ $scaledRating !== null ? $scaledRating : 'N/A' }}
                        </h3>
                        <p class="mb-0">Average Rating (0-100)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
