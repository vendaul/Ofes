@extends('layouts.faculty')

@section('content')

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
    }

    h2 {
        text-align: center;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .section-title {
        font-weight: bold;
        margin-top: 20px;
    }

    .info-table {
        width: 100%;
        margin-bottom: 20px;
    }

    .info-table td {
        padding: 5px 10px;
    }

    .info-label {
        width: 250px;
    }

    .summary {
        margin-top: 10px;
        margin-bottom: 20px;
    }

    .summary p {
        margin: 3px 0;
    }

    table.report {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    table.report th,
    table.report td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
    }

    table.report th {
        font-weight: bold;
    }

    .total-row {
        font-weight: bold;
    }

</style>

<h2>INDIVIDUAL FACULTY EVALUATION REPORT</h2>

<div class="section-title">A. Faculty Information</div>

<table class="info-table">
    <tr>
        <td class="info-label">Name of Faculty Evaluated</td>
        <td>: {{ $instructor->name }}</td>
    </tr>
    <tr>
        <td class="info-label">Department/College</td>
        <td>: {{ $instructor->department ?: optional($instructor->collegeRelation)->name ?: 'N/A' }}</td>
    </tr>
    <tr>
        <td class="info-label">Current Faculty Rank</td>
        <td>: {{ $instructor->academic_rank ?? $instructor->position ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="info-label">Semester/Term & Academic Year</td>
        <td>: {{ $semester ?? 'N/A' }}</td>
    </tr>
</table>

<div class="section-title">B. Summary of Average SET Rating</div>

<div class="summary">
    <p><strong>Computation:</strong></p>
    <p>Step 1: Get the average SET rating for each class.</p>
    <p>Step 2: Multiply the number of students in each class with its average SET rating to get the Weighted SET Score per class.</p>
    <p>Step 3: Get the total number of students and the total weighted SET score.</p>
</div>

<table class="report">
    <thead>
        <tr>
            <th>Seq</th>
            <th>(1)<br>Course Code</th>
            <th>(2)<br>Year/Section</th>
            <th>(3)<br>No. of Students</th>
            <th>(4)<br>Average SET Rating</th>
            <th>(3 x 4)<br>Weighted SET Score</th>
        </tr>
    </thead>
    <tbody>
        @php
        $totalStudents = 0;
        $totalWeighted = 0;
        @endphp

        @foreach($classes as $index => $class)
        @php
        $weighted = $class->weighted;
        $totalStudents += $class->students;
        $totalWeighted += $weighted;
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $class->course_code }}</td>
            <td>{{ $class->section }}</td>
            <td>{{ $class->students }}</td>
            <td>{{ $class->average }}</td>
            <td>{{ number_format($weighted, 0) }}</td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="3">TOTAL</td>
            <td>{{ $totalStudents }}</td>
            <td colspan="1">TOTAL</td>
            <td>{{ number_format($totalWeighted, 0) }}</td>
        </tr>
    </tbody>
</table>

<div class="row mt-4">
    <div class="col-md-12 mb-2">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-download"></i> Download / Print Report
        </button>
    </div>
    <div class="col-md-12">
        <a href="{{ route('instructor.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

@endsection
