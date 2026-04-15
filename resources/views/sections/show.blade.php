@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-eye"></i> View Section</h1>
    <p>Section details and information</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-layer-group"></i> Section Details
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Section ID:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-primary text-white">{{ $section->id }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Section Name:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $section->name }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Year:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-primary">Year {{ $section->year }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Total Students:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-success">
                            <i class="fas fa-users"></i> {{ $section->students_count ?? $section->students->count() }} Students
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Created At:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $section->created_at ? $section->created_at->format('M d, Y H:i') : 'N/A' }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Last Updated:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $section->updated_at ? $section->updated_at->format('M d, Y H:i') : 'N/A' }}
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('sections.edit', ['id' => $section->id]) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Section
                    </a>
                    <a href="{{ route('sections.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sections
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- STUDENTS LIST SECTION --}}
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center text-white bg-primary">
                <h5 class="mb-0"><i class="fas fa-users"></i> Students in {{ $section->name }}</h5>
                <a href="{{ route('students.create') }}?section_id={{ $section->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Add Student
                </a>
            </div>
            <div class="card-body">
                @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-id-card"></i> Student ID</th>
                                <th><i class="fas fa-user"></i> Name</th>
                                <th><i class="fas fa-envelope"></i> Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td><span class="badge bg-primary text-white">{{ $student->sid ?: 'N/A' }}</span></td>
                                <td>{{ trim(($student->fname ?? '') . ' ' . ($student->lname ?? '')) ?: 'N/A' }}</td>
                                <td>{{ $student->email ?: optional($student->user)->email ?: 'No email' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Students Found</h5>
                    <p class="text-muted mb-0">There are currently no enrolled students in this section.</p>
                </div>
                @endif

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
                    <a href="{{ route('sections.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Add New Section
                    </a>
                    <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i> Add Student to Section
                    </a>
                    <a href="{{ route('assignments.create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-chalkboard-teacher"></i> Assign Instructor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
