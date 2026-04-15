@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-users"></i> Students in {{ $section->name }}</h1>
    <p>Manage students assigned to this section</p>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('info'))
<div class="alert alert-info">{{ session('info') }}</div>
@endif

<div class="mb-4">
    <a href="{{ route('students.create', ['section_id' => $section->id]) }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add Student
    </a>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importStudentsModal">
        <i class="fas fa-upload"></i> Import Students
    </button>
    <a href="{{ route('sections.index', ['tab' => 'sections']) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($students->count() > 0)
<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> Students List ({{ $students->count() }})
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-id-card"></i> Student ID</th>
                    <th><i class="fas fa-user"></i> Name</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-calendar"></i> Created</th>
                    <th style="width: 200px;"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>
                        <span class="badge bg-primary text-white">{{ $student->sid }}</span>
                    </td>
                    <td>
                        {{ $student->fname }} {{ $student->lname }}
                    </td>
                    <td>
                        @php
                        $studentEmail = $student->email ?: optional($student->user)->email;
                        @endphp
                        @if($studentEmail)
                        {{ $studentEmail }}
                        @else
                        <span class="text-muted">No email</span>
                        @endif
                    </td>
                    <td>
                        {{ $student->created_at ? $student->created_at->format('M d, Y') : 'N/A' }}
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('students.show', ['student' => $student->id, 'section_id' => $section->id, 'tab' => 'sections']) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('students.edit', ['student' => $student->id, 'section_id' => $section->id, 'tab' => 'sections']) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="section_id" value="{{ $section->id }}">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                    <i class="fas fa-trash"></i> Delete
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
    <i class="fas fa-info-circle"></i> No students found in this section.
    <a href="{{ route('students.create', ['section_id' => $section->id]) }}">Add the first student now</a>
</div>
@endif

<!-- Import Students Modal -->
<div class="modal fade" id="importStudentsModal" tabindex="-1" aria-labelledby="importStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                        <label for="csv_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">
                            Upload a CSV file with columns: student_id, fname, lname, email (optional), password (optional)
                        </div>
                    </div>
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
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
