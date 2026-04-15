@extends('layouts.student')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-calendar-alt"></i> My Class Schedules</h1>
    <p>View your class schedules filtered by term and academic year.</p>
</div>

<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-filter"></i> Filter Schedules
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('student.schedules') }}" class="row g-3">
            <div class="col-md-8">
                <label for="filter" class="form-label">Term and Academic Year</label>
                <select name="filter" id="filter" class="form-select">
                    <option value="">All Terms and Years</option>
                    @foreach($scheduleFilters as $filter)
                    <option value="{{ $filter }}" {{ $selectedFilter == $filter ? 'selected' : '' }}>{{ $filter }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> Class Schedules
    </div>
    <div class="card-body">
        @if($schedules->isEmpty())
        <div class="alert alert-info">
            No schedules found for the selected filters.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Term</th>
                        <th>AY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->subject ? $schedule->subject->name : 'N/A' }}</td>
                        <td>{{ $schedule->section ? $schedule->section->name : 'N/A' }}</td>
                        <td>{{ $schedule->term ?? 'N/A' }}</td>
                        <td>{{ $schedule->ay ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
