@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-user-graduate"></i> Students Management</h1>
    <p>Manage student records in the system</p>
</div>

<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('students.create') }}" class="btn btn-primary me-2">
            <i class="fas fa-plus"></i> Add New Student
        </a>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importStudentsModal">
            <i class="fas fa-upload"></i> Import Students
        </button>
    </div>
    <div>
        <small class="text-muted">
            Current filter:
            <strong>Area</strong> {{ $selectedArea ?: 'All' }} |
            <strong>College</strong> {{ $selectedCollege ?: 'All' }}
        </small>
    </div>
</div>

@if($students->count() > 0)
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Students List
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Number</th>
                    <th>Name</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td><span class="badge bg-primary text-white">{{ $student->id }}</span></td>
                    <td><strong>{{ $student->sid }}</strong></td>
                    <td>{{ $student->fname }} {{ $student->lname }}</td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No students found.
    <a href="{{ route('students.create') }}">Create one now</a>
</div>
@endif

<div class="modal fade" id="importStudentsModal" tabindex="-1" aria-labelledby="importStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importStudentsModalLabel">
                    <i class="fas fa-upload"></i> Import Students
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="importFile" class="form-label">Select CSV or Excel File</label>
                        <input type="file" class="form-control" id="importFile" name="file" accept=".csv,.xlsx,.xls,.txt" required>
                        <div class="form-text">Supported formats: CSV, Excel (.xlsx, .xls), Text (.txt)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
