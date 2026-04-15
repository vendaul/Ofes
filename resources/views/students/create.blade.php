@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add Student</h1>
    <p>Create a student record</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header text-white bg-primary">
                <i class="fas fa-form"></i> Create New Student
            </div>
            <div class="card-body">
                <form action="{{ route('students.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="area_code" value="{{ old('area_code', $selectedArea ?? session('selected_area')) }}">
                    <input type="hidden" name="college_code" value="{{ old('college_code', $selectedCollege ?? session('selected_college')) }}">
                    @if(!empty($preselectedSectionId))
                    <input type="hidden" name="section_id" value="{{ $preselectedSectionId }}">
                    @endif

                    <div class="form-group mb-3">
                        <label for="student_number" class="form-label">Student Number</label>
                        <input type="text" class="form-control @error('sid') is-invalid @enderror" id="student_number" name="sid" placeholder="Enter student number" required>
                        @error('sid')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control @error('fname') is-invalid @enderror" id="first_name" name="fname" placeholder="Enter first name" required>
                        @error('fname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control @error('lname') is-invalid @enderror" id="last_name" name="lname" placeholder="Enter last name" required>
                        @error('lname')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email <span class="text-muted">(optional)</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter student email" value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Student
                    </button>
                    <a href="javascript:history.back()" class="btn btn-secondary">
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
                <li><i class="fas fa-check text-success"></i> <strong>Student ID</strong> must be unique</li>
                <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>First Name</strong> and <strong>Last Name</strong> are required</li>
                <li class="mt-2"><i class="fas fa-check text-success"></i> <strong>Email</strong> is optional but recommended</li>
                <li class="mt-2"><i class="fas fa-check text-success"></i> Section assignment is managed in class schedules</li>
            </ul>
        </div>
    </div>
</div>
</div>

@endsection
