@extends('layouts.faculty')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">
                        <i class="fas fa-chart-bar"></i> Evaluation Report
                    </h2>
                </div>

                <div class="card-body">
                    @if(isset($message))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> {{ $message }}
                    </div>
                    @else
                    <!-- Report Header -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5 class="text-primary">Course Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Instructor:</strong></td>
                                    <td>{{ $assignment->instructor->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $assignment->subject->name ?? 'N/A' }} ({{ $assignment->subject->code ?? '' }})</td>
                                </tr>
                                <tr>
                                    <td><strong>Section:</strong></td>
                                    <td>{{ $assignment->section->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Semester:</strong></td>
                                    <td>{{ $assignment->term ?? 'N/A' }} {{ $assignment->ay ?? '' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-4">
                            <h5 class="text-success">Summary Statistics</h5>
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    @php $overallAverage100 = number_format(min(max(($overallAverage * 20), 0), 100), 2); @endphp
                                    <h3 class="text-primary mb-2">{{ $overallAverage100 }}/100</h3>
                                    <h5 class="text-{{ $overallRating === 'Excellent' ? 'success' : ($overallRating === 'Good' ? 'info' : ($overallRating === 'Fair' ? 'warning' : 'danger')) }}">
                                        {{ $overallRating }}
                                    </h5>
                                    <p class="mb-1">Overall Rating</p>
                                    <small class="text-muted">{{ $totalEvaluations }} evaluation{{ $totalEvaluations > 1 ? 's' : '' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category-wise Results -->
                    <h5 class="text-primary mb-3">Detailed Results by Category</h5>

                    @foreach($categoryData as $category => $data)
                    @php $catAvg100 = number_format(min(max(($data['average'] * 20), 0), 100), 2); @endphp
                    <div class="card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center text-white">
                            <h6 class="mb-0">{{ $category }}</h6>
                            <span class="badge bg-primary fs-6">Average: {{ $catAvg100 }}/100</span>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th class="text-center">Average Rating</th>
                                            <th class="text-center">Response Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['questions'] as $questionText => $questionData)
                                        <tr>
                                            <td>{{ $questionText }}</td>
                                            <td class="text-center">
                                                @php $questionAvg100 = number_format(min(max(($questionData['average'] * 20), 0), 100), 2); @endphp
                                                <span class="badge bg-{{ $questionData['average'] >= 4 ? 'success' : ($questionData['average'] >= 3 ? 'warning' : 'danger') }} fs-6">
                                                    {{ $questionAvg100 }}/100
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $questionData['response_count'] ?? count($questionData['ratings'] ?? []) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <!-- Student Comments Section -->
                    @php
                    $comments = $evaluations->where('comment', '!=', null)->where('comment', '!=', '');
                    @endphp
                    @if($comments->count() > 0)
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-comments"></i> Student Feedback ({{ $comments->count() }})
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach($comments as $evaluation)
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> Anonymous
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($evaluation->date_submitted)->format('M d, Y') }}
                                    </small>
                                </div>
                                <p class="mb-0">{{ $evaluation->comment }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <button onclick="window.print()" class="btn btn-secondary me-2">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                            <a href="{{ route('instructor.dashboard') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {

        .btn,
        .card-header.bg-primary,
        .card-header.bg-info {
            display: none !important;
        }

        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }

        body {
            background: white !important;
        }

        .container {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    }

</style>
@endsection
