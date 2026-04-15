@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New Question Template</h1>
    @php $periodLocked = !empty($activePeriod); @endphp
    <form action="{{ route('question_templates.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="template_date" class="form-label">Date</label>
                <input type="date" name="template_date" id="template_date" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="semester" class="form-label">Semester
                    @if($periodLocked)
                    <span class="badge bg-info">Locked to active period</span>
                    @endif
                </label>
                <input type="text" name="semester" id="semester" class="form-control" placeholder="First, Second" value="{{ old('semester', $activePeriod->term ?? '') }}" @if($periodLocked) readonly @endif>
            </div>
            <div class="col-md-4 mb-3">
                <label for="school_year" class="form-label">School Year
                    @if($periodLocked)
                    <span class="badge bg-info">Locked to active period</span>
                    @endif
                </label>
                <input type="text" name="school_year" id="school_year" class="form-control" placeholder="e.g., 2024-2025" value="{{ old('school_year', $activePeriod->year ?? '') }}" @if($periodLocked) readonly @endif>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="questions" class="form-label">Questions (one per line)</label>
            <textarea name="questions[]" id="questions" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Save Template</button>
        <a href="{{ route('questions') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
