@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Question Templates</h1>
    <a href="{{ route('question_templates.create') }}" class="btn btn-primary mb-3">Add New Template</a>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Semester</th>
                <th>School Year</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr>
                <td>{{ $template->name }}</td>
                <td>{{ $template->template_date ? \Illuminate\Support\Carbon::parse($template->template_date)->format('Y-m-d') : '—' }}</td>
                <td>{{ $template->semester }}</td>
                <td>{{ $template->school_year }}</td>
                <td>{{ $template->description }}</td>
                <td>
                    <a href="{{ route('question_templates.edit', $template) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('question_templates.destroy', $template) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
