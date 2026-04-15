@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add New Instructor</h1>
    <p>Create a new instructor account</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form"></i> Instructor Information
            </div>
            <div class="card-body">
                <form action="{{ route('instructors.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="empid" class="form-label">Employee ID</label>
                        <input type="text" class="form-control @error('empid') is-invalid @enderror" id="empid" name="empid" placeholder="Enter employee ID" value="{{ old('empid') }}" required>
                        @error('empid')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="fname" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('fname') is-invalid @enderror" id="fname" name="fname" placeholder="Enter first name" value="{{ old('fname') }}" required>
                        @error('fname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="mname" class="form-label">Middle Name</label>
                        <input type="text" class="form-control @error('mname') is-invalid @enderror" id="mname" name="mname" placeholder="Enter middle name" value="{{ old('mname') }}">
                        @error('mname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="lname" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('lname') is-invalid @enderror" id="lname" name="lname" placeholder="Enter last name" value="{{ old('lname') }}" required>
                        @error('lname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="academic_rank" class="form-label">Academic Rank</label>
                        <input type="text" class="form-control @error('academic_rank') is-invalid @enderror" id="academic_rank" name="academic_rank" value="{{ old('academic_rank') }}" placeholder="e.g. Assistant Professor" list="academic-rank-list" autocomplete="off">
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
                        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}" placeholder="e.g. Department Head" list="position-list" autocomplete="off">
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
                        <select class="form-control @error('college') is-invalid @enderror" id="college" name="college" required>
                            <option value="">Select College</option>
                            @foreach($collegeOptions as $college)
                            <option value="{{ $college->id }}" {{ old('college', $selectedCollege ?? '') == $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                            @endforeach
                        </select>
                        @error('college')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="hidden" name="area_code" value="{{ old('area_code', $selectedArea ?? '') }}">
                    @if(!empty($selectedArea))
                    <div class="form-group mb-3">
                        <label class="form-label">Selected Area</label>
                        <input class="form-control" value="{{ $selectedAreaName ?? $selectedArea }}" readonly>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Instructor
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
                <i class="fas fa-lightbulb"></i> Tips
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> <strong>Employee ID</strong> must be unique</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>First Name</strong>, <strong>Middle Name</strong>, and <strong>Last Name</strong> are required</li>
                    <li class="mt-2"><i class="fas fa-info-circle text-info"></i> Email and password will be set when instructor logs in</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
