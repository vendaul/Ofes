@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Teaching Assignment</h1>
    <p>Update instructor teaching assignment</p>
</div>

@php $periodLocked = !empty($activePeriod); @endphp

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> <strong>Errors found:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form"></i> Assignment Information
            </div>
            <div class="card-body">
                <form action="{{ route('assignments.update', $assignment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="instructor_id" class="form-label">Instructor</label>
                        <select class="form-select @error('instructor_id') is-invalid @enderror" id="instructor_id" name="instructor_id" required>
                            <option value="">-- Select an Instructor --</option>
                            @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ $assignment->instructor_id == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->fname }} {{ $instructor->lname }}
                            </option>
                            @endforeach
                        </select>
                        @error('instructor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="subject_name" class="form-label">Subject</label>
                        <input type="text" class="form-control @error('subject_name') is-invalid @enderror" id="subject_name" name="subject_name" value="{{ $assignment->subject ? $assignment->subject->code . ' - ' . $assignment->subject->name : '' }}" placeholder="Type or select subject..." required list="subjects-list">
                        <datalist id="subjects-list">
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->code }} - {{ $subject->name }}">
                                @endforeach
                        </datalist>
                        @error('subject_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="section_name" class="form-label">Section</label>
                        <input type="text" class="form-control @error('section_name') is-invalid @enderror" id="section_name" name="section_name" value="{{ $assignment->section ? $assignment->section->name : '' }}" placeholder="Type or select section..." required list="sections-list">
                        <datalist id="sections-list">
                            @foreach($sections as $section)
                            <option value="{{ $section->name }}">
                                @endforeach
                        </datalist>
                        @error('section_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="school_year" class="form-label">School Year
                            @if($periodLocked)
                            <span class="badge bg-info">Locked to active period</span>
                            @endif
                        </label>
                        <select class="form-select @error('school_year') is-invalid @enderror" id="school_year" name="school_year" required @if($periodLocked) disabled @endif>
                            <option value="">-- Select School Year --</option>
                            @foreach($schoolYears ?? [] as $year)
                            <option value="{{ $year }}" {{ old('school_year', $activePeriod->year ?? $assignment->school_year) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endforeach
                        </select>
                        @if($periodLocked)
                        <input type="hidden" name="school_year" value="{{ $activePeriod->year }}">
                        @endif
                        @error('school_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="semester" class="form-label">Semester
                            @if($periodLocked)
                            <span class="badge bg-info">Locked to active period</span>
                            @endif
                        </label>
                        <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required @if($periodLocked) disabled @endif>
                            <option value="">-- Select Semester --</option>
                            @foreach($semesters ?? [] as $semester)
                            <option value="{{ $semester }}" {{ old('semester', $activePeriod->term ?? $assignment->semester) == $semester ? 'selected' : '' }}>
                                {{ $semester }}
                            </option>
                            @endforeach
                        </select>
                        @if($periodLocked)
                        <input type="hidden" name="semester" value="{{ $activePeriod->term }}">
                        @endif
                        @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Assignment
                        </button>
                        <a href="{{ route('instructors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-info-circle"></i> Assignment Info
            </div>
            <div class="card-body">
                <p><strong>Assignment ID:</strong></p>
                <p class="text-muted">{{ $assignment->asign_id }}</p>

                <p class="mt-3"><strong>Created:</strong></p>
                @if($assignment->created_at)
                <p class="text-muted">{{ $assignment->created_at->format('M d, Y') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif

                <p class="mt-3"><strong>Last Updated:</strong></p>
                @if($assignment->updated_at)
                <p class="text-muted">{{ $assignment->updated_at->format('M d, Y H:i') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
