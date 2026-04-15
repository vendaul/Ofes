@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Instructor</h1>
    <p>Update instructor information</p>
</div>
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-user"></i> Instructor Details
            </div>
            <div class="card-body">
                <form action="{{ route('instructors.update', $instructor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3">
                        <label for="empid" class="form-label">Employee ID</label>
                        <input type="text" class="form-control @error('empid') is-invalid @enderror" id="empid" name="empid" value="{{ old('empid', $instructor->empid) }}" required>
                        @error('empid')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="fname" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('fname') is-invalid @enderror" id="fname" name="fname" value="{{ old('fname', $instructor->fname) }}" required>
                        @error('fname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="mname" class="form-label">Middle Name</label>
                        <input type="text" class="form-control @error('mname') is-invalid @enderror" id="mname" name="mname" value="{{ old('mname', $instructor->mname) }}">
                        @error('mname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('lname') is-invalid @enderror" id="lname" name="lname" value="{{ old('lname', $instructor->lname) }}" required>
                        @error('lname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="academic_rank" class="form-label">Academic Rank</label>
                        <input type="text" class="form-control @error('academic_rank') is-invalid @enderror" id="academic_rank" name="academic_rank" value="{{ old('academic_rank', $instructor->academic_rank) }}" placeholder="e.g. Assistant Professor" list="academic-rank-list" autocomplete="off">
                        <datalist id="academic-rank-list">
                            @foreach(($academicRankOptions ?? []) as $rankOption)
                            <option value="{{ $rankOption }}"></option>
                            @endforeach
                        </datalist>
                        @error('academic_rank')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="position" class="form-label">Designation (Position)</label>
                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $instructor->position) }}" placeholder="e.g. Department Head" list="position-list" autocomplete="off">
                        <datalist id="position-list">
                            @foreach(($positionOptions ?? []) as $positionOption)
                            <option value="{{ $positionOption }}"></option>
                            @endforeach
                        </datalist>
                        @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="college" class="form-label">College</label>
                        <select class="form-control @error('college') is-invalid @enderror" id="college" name="college">
                            <option value="">Select College</option>
                            @foreach(\App\Models\College::all() as $college)
                            <option value="{{ $college->id }}" {{ old('college', $instructor->college) == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                            @endforeach
                        </select>
                        @error('college')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Instructor
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
                <i class="fas fa-info-circle"></i> Instructor Info
            </div>
            <div class="card-body">
                <p><strong>ID:</strong></p>
                <p class="text-muted">{{ $instructor->id }}</p>

                <p class="mt-3"><strong>Role:</strong></p>
                <p class="text-muted">{{ $instructor->display_role }}</p>

                <p class="mt-3"><strong>Created:</strong></p>
                @if($instructor->created_at)
                <p class="text-muted">{{ $instructor->created_at->format('M d, Y') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif

                <p class="mt-3"><strong>Last Updated:</strong></p>
                @if($instructor->updated_at)
                <p class="text-muted">{{ $instructor->updated_at->format('M d, Y H:i') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
