@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-edit"></i> Edit Question</h1>
    <p>Update evaluation question</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-question-circle"></i> Question Details
            </div>
            <div class="card-body">
                <form action="{{ route('questions.update',$question->question_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-3" id="questions-container">
                        <label class="form-label">Question Text</label>
                        <small class="text-muted">Use + to add additional question fields; - to remove.</small>
                        @php
                        $oldTexts = old('question_text', [$question->question_text]);
                        if (!is_array($oldTexts)) {
                        $oldTexts = [$oldTexts];
                        }
                        @endphp
                        @foreach($oldTexts as $idx => $text)
                        <div class="input-group mb-2 question-row">
                            <input type="text" class="form-control" name="question_text[]" value="{{ $text }}" placeholder="Enter evaluation question" required>
                            <button class="btn btn-outline-secondary add-question" type="button">+</button>
                        </div>
                        @endforeach
                        @error('question_text')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $question->category) }}" required>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Question
                        </button>
                        <a href="{{ route('questions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                    <script>
                        document.addEventListener('click', function(e) {
                            if (e.target.matches('.add-question')) {
                                const container = document.querySelector('#questions-container');
                                if (!container) return;
                                const row = document.createElement('div');
                                row.className = 'input-group mb-2 question-row';
                                row.innerHTML = '<input type="text" class="form-control" name="question_text[]" placeholder="Enter evaluation question" required>' +
                                    '<button class="btn btn-outline-secondary remove-question" type="button">-</button>';
                                container.appendChild(row);
                            }
                            if (e.target.matches('.remove-question')) {
                                const row = e.target.closest('.question-row');
                                row && row.remove();
                            }
                        });

                    </script>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Question Info
            </div>
            <div class="card-body">
                <p><strong>Question ID:</strong></p>
                <p class="text-muted">{{ $question->question_id }}</p>

                <p class="mt-3"><strong>Created:</strong></p>
                @if($question->created_at)
                <p class="text-muted">{{ $question->created_at->format('M d, Y') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif

                <p class="mt-3"><strong>Last Updated:</strong></p>
                @if($question->updated_at)
                <p class="text-muted">{{ $question->updated_at->format('M d, Y H:i') }}</p>
                @else
                <p class="text-muted">&mdash; not available</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
