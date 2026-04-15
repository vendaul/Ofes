@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-tasks"></i> Teaching Assignments</h1>
    <p>Manage instructor teaching assignments</p>
</div>

<div class="mb-4">
    <a href="{{ route('assignments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Assignment
    </a>
</div>

@if($assignments->count() > 0)
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Assignments List
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-id-card"></i> ID</th>
                    <th><i class="fas fa-user"></i> Instructor</th>
                    <th><i class="fas fa-book"></i> Subject</th>
                    <th><i class="fas fa-layer-group"></i> Section</th>
                    <th><i class="fas fa-calendar"></i> School Year</th>
                    <th><i class="fas fa-calendar-alt"></i> Semester</th>
                    <th style="width: 150px;"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assignments as $a)
                <tr>
                    <td>
                        <span class="badge bg-primary text-white">{{ $a->asign_id }}</span>
                    </td>
                    <td>
                        {{-- instructor may be null if record was deleted or invalid id was saved --}}
                        <strong>
                            {{ optional($a->instructor)->fname ?? 'N/A' }}
                            {{ optional($a->instructor)->lname ?? '' }}
                        </strong>
                    </td>
                    <td>
                        {{ $a->subject ? $a->subject->name : 'Unknown Subject' }}
                    </td>
                    <td>
                        <span class="badge bg-success">{{ $a->section ? $a->section->name : 'Unknown Section' }}</span>
                    </td>
                    <td>
                        <span class="badge bg-primary">{{ $a->school_year }}</span>
                    </td>
                    <td>
                        <span class="badge bg-warning text-dark">{{ $a->semester }}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('assignments.edit', $a) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('assignments.destroy', $a) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?')">
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
    <i class="fas fa-info-circle"></i> No assignments found. <a href="{{ route('assignments.create') }}">Create one now</a>
</div>
@endif

@endsection
