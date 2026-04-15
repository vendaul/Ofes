@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-calendar-alt"></i> Class Schedules Management</h1>
        <p>Manage all class schedules in the system</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('class-schedules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Class Schedule
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i> Class Schedules
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Schedule Code</th>
                            <th>Subject</th>
                            <th>Section</th>
                            <th>Instructor</th>
                            <th>Academic Year</th>
                            <th>Term</th>
                            <th>Class Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->schedule_code }}</td>
                            <td>
                                <strong>{{ $schedule->subject ? $schedule->subject->code : 'N/A' }}</strong><br>
                                <small>{{ $schedule->subject ? $schedule->subject->name : 'Unknown Subject' }}</small>
                            </td>
                            <td>{{ $schedule->section ? $schedule->section->name : 'N/A' }}</td>
                            <td>{{ $schedule->instructor ? $schedule->instructor->fname . ' ' . $schedule->instructor->lname : 'N/A' }}</td>
                            <td>{{ $schedule->ay }}</td>
                            <td>{{ $schedule->term }}</td>
                            <td>{{ $schedule->class_size }}</td>
                            <td>
                                <a href="{{ route('class-schedules.show', $schedule) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('class-schedules.edit', $schedule) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('class-schedules.destroy', $schedule) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class schedule?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $schedules->links() }}
        </div>
    </div>
</div>
@endsection
