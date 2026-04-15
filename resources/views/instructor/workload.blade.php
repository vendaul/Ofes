@extends('layouts.faculty')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-tasks"></i> My Workload</h1>
    <p>View your teaching assignments and evaluation completion status</p>
</div>

<!-- Filter Form - Always visible -->
<div class="card mb-4">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-filter"></i> Filter Assignments
    </div>
    <div class="card-body">
        @if(isset($availableFilters) && $availableFilters->count() > 0)
        <form method="GET" action="{{ route('instructor.workload') }}" class="row g-3">
            <div class="col-md-8">
                <label for="filter" class="form-label">Term and Academic Year</label>
                <select name="filter" id="filter" class="form-select">
                    <option value="">All Terms and Academic Years</option>
                    @foreach($availableFilters as $filter)
                    <option value="{{ $filter }}" {{ (isset($selectedFilter) && $selectedFilter == $filter) ? 'selected' : '' }}>
                        {{ $filter }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('instructor.workload') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Clear
                </a>
            </div>
        </form>
        @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No assignment term/year combinations available for filtering yet.
        </div>
        @endif
    </div>
</div>

@if($schedules->count() > 0)
<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> My Teaching Assignments
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-code"></i> Course Code</th>
                    <th><i class="fas fa-book"></i> Course Name</th>
                    <th><i class="fas fa-layer-group"></i> Section</th>
                    <th><i class="fas fa-calendar"></i> Academic Year</th>
                    <th><i class="fas fa-calendar-alt"></i> Term</th>
                    <th><i class="fas fa-star"></i> Eval Avg (0-100)</th>
                    <th><i class="fas fa-link"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $schedule)
                <tr>
                    <td>
                        <span class="badge bg-primary text-white">{{ $schedule->subject ? $schedule->subject->code : 'N/A' }}</span>
                    </td>
                    <td>
                        <strong>{{ $schedule->subject ? $schedule->subject->name : 'Unknown Subject' }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-success">{{ $schedule->section ? $schedule->section->name : 'Unknown Section' }}</span>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $schedule->ay }}</span>
                    </td>
                    <td>
                        <span class="badge bg-warning text-dark">{{ $schedule->term }}</span>
                    </td>
                    <td>
                        @php
                        $eval = $schedule->evaluation_result;
                        $avg = $eval ? number_format(min(max($eval->overall_average * 20, 0), 100), 2) : 'N/A';
                        @endphp
                        <span class="badge bg-info text-white">{{ $avg }}</span>
                    </td>
                    <td>
                        @if($schedule->evaluation_result)
                        <a href="{{ route('instructor.results', $schedule->id) }}" class="btn btn-sm btn-success">View Results</a>
                        @else
                        <span class="text-muted">No result yet</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if(!isset($schedules) || $schedules->count() === 0)
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> You have no teaching assignments or class schedules at this time.
</div>
@endif

<div class="row mt-4">
    <div class="col-md-12">
        <a href="{{ route('instructor.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

@endsection
