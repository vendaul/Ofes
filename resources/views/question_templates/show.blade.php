@extends('layouts.app')

@section('content')

<!-- Template Header -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h2 class="mb-2">Template Details</h2>
        <p class="mb-1 text-muted">
            <strong>Date:</strong> {{ $template->template_date ? \\Illuminate\\Support\\Carbon::parse($template->template_date)->format('Y-m-d') : '—' }}
        </p>
        <p class="mb-1 text-muted">
            @if($template->semester)
            <strong>Semester:</strong> {{ $template->semester }}
            @endif
            @if($template->school_year)
            &nbsp;|&nbsp;<strong>School Year:</strong> {{ $template->school_year }}
            @endif
        </p>
        <p class="text-muted mb-0">{{ $template->description }}</p>
    </div>
</div>
<br><br>
<div class="container py-4">

    <!-- Questions Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Questions by Category</h5>
        </div>

        <div class="card-body table-responsive">

            @php
            $grouped = collect($questions)->groupBy('category');
            @endphp

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Category</th>
                        <th>Questions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($grouped as $category => $items)
                    <tr>
                        <!-- Category Column -->
                        <td class="fw-bold text-primary">
                            {{ $category }}
                        </td>

                        <!-- Questions Column -->
                        <td>
                            <ol class="mb-0 ps-3">
                                @foreach($items as $q)
                                <li class="mb-2">
                                    {{ $q['question_text'] }}
                                </li>
                                @endforeach
                            </ol>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>

    <!-- Back Button -->

</div>
<div class="mt-4">
    <a href="{{ route('questions.index') }}" class="btn btn-secondary">
        ← Back to Templates
    </a>
</div>
@endsection
