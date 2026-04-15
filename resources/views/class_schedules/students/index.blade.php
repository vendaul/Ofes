@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-users"></i> Students in {{ $classSchedule->subject?->name ?? 'Unknown Subject' }}</h1>
    <p>Class: {{ $classSchedule->section?->name ?? 'Unknown Section' }} | {{ $classSchedule->ay }} {{ $classSchedule->term }}</p>
</div>

<div class="mb-4">
    <a href="{{ route('class-schedule-students.create', $classSchedule->id) }}" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Add Student
    </a>
    <a href="javascript:history.back()" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($students->count() > 0)
<div class="card">
    <div class="card-header text-white bg-primary">
        <i class="fas fa-list"></i> Enrolled Students ({{ $students->count() }})
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-id-card"></i> Student Number</th>
                    <th><i class="fas fa-user"></i> Name</th>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <th><i class="fas fa-layer-group"></i> Status</th>
                    <th style="width: 160px;"><i class="fas fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $cs)
                <tr>
                    <td>
                        <span class="badge bg-primary">{{ $cs->student?->sid ?? 'Unknown' }}</span>
                    </td>
                    <td>
                        {{ $cs->student?->fname ?? '' }} {{ $cs->student?->lname ?? '' }}
                    </td>
                    <td>
                        {{ $cs->student?->email ?? 'No email' }}
                    </td>
                    <td>
                        @php
                        $studentType = strtolower((string) ($cs->student?->student_type ?? ''));
                        @endphp
                        @if($studentType === 'regular')
                        <span class="badge bg-info">Regular</span>
                        @elseif($studentType === 'irregular')
                        <span class="badge bg-warning text-dark">Irregular</span>
                        @else
                        <span class="badge bg-secondary">{{ $cs->student?->student_type ?: 'N/A' }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('class-schedule-students.edit', [$classSchedule->id, $cs->id]) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('class-schedule-students.destroy', [$classSchedule->id, $cs->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> No students enrolled in this class schedule yet.
    <a href="{{ route('class-schedule-students.create', $classSchedule->id) }}">Add the first student</a>
</div>
@endif

@endsection
