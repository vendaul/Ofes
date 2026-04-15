@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Question Template</h1>
    @php $periodLocked = !empty($activePeriod); @endphp
    <form action="{{ route('question_templates.update', $questionTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="template_date" class="form-label">Date</label>
                <input type="date" name="template_date" id="template_date" class="form-control" value="{{ old('template_date', $questionTemplate->template_date ?? now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="semester" class="form-label">Semester
                    @if($periodLocked)
                    <span class="badge bg-info">Locked to active period</span>
                    @endif
                </label>
                <input type="text" name="semester" id="semester" class="form-control" value="{{ old('semester', $activePeriod->term ?? $questionTemplate->semester) }}" placeholder="First, Second" @if($periodLocked) readonly @endif>
            </div>
            <div class="col-md-4 mb-3">
                <label for="school_year" class="form-label">School Year
                    @if($periodLocked)
                    <span class="badge bg-info">Locked to active period</span>
                    @endif
                </label>
                <input type="text" name="school_year" id="school_year" class="form-control" value="{{ old('school_year', $activePeriod->year ?? $questionTemplate->school_year) }}" placeholder="e.g., 2024-2025" @if($periodLocked) readonly @endif>
            </div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control">{{ $questionTemplate->description }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Categories & Questions</label>
            @php
            $questionsArray = json_decode($questionTemplate->questions, true) ?? [];
            @endphp
            <div id="questions-list">
                @foreach($questionsArray as $i => $q)
                <div class="row mb-2">
                    <div class="col-md-5">
                        <input type="text" name="questions[{{ $i }}][category]" class="form-control" value="{{ $q['category'] ?? '' }}" placeholder="Category" required>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="questions[{{ $i }}][question_text]" class="form-control" value="{{ $q['question_text'] ?? (is_string($q) ? $q : '') }}" placeholder="Question" required>
                    </div>
                </div>
                @endforeach
            </div>
            <!-- Optionally, add JS to allow adding/removing questions dynamically -->
        </div>
        <button type="submit" class="btn btn-success">Update Template</button>
        <a href="{{ route('questions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
