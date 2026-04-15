@extends('layouts.admin')

@section('content')

<div class="page-header">
    <h1><i class="fas fa-question-circle"></i> Add New Question</h1>
    <p>Create a new evaluation question</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-form"></i> Question Information
            </div>
            <div class="card-body">
                <form action="{{ route('questions.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3" id="questions-container">
                        <label class="form-label">Question Text</label>
                        <small class="text-muted">Use + to add additional question fields; - to remove.</small>
                        <div class="input-group mb-2 question-row">
                            <input type="text" class="form-control" name="question_text[]" placeholder="Enter evaluation question" required>
                            <button class="btn btn-outline-secondary add-question" type="button">+</button>
                        </div>
                        @error('question_text')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" placeholder="e.g., Teaching Quality, Communication" required>
                        @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Question
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
                <i class="fas fa-lightbulb"></i> Tips
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Write clear, concise questions</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Use appropriate categories</li>
                    <li class="mt-2"><i class="fas fa-check text-success"></i> Avoid ambiguous wording</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
